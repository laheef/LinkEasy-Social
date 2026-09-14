<?php
namespace App\Services;
use App\Database;
/** Verified subscription lifecycle only. Real sandbox acceptance remains a launch gate. */
final class PayPal
{
    private HttpClient $http;
    private ?string $accessToken=null;
    public function __construct(?HttpClient $http=null){$this->http=$http??new HttpClient();}
    public function configured(): bool{return env('PAYPAL_ENABLED',false) && (bool)getenv('PAYPAL_CLIENT_ID')&&(bool)getenv('PAYPAL_CLIENT_SECRET');}
    public function baseUrl(): string{return env('PAYPAL_MODE','sandbox')==='live'?'https://api-m.paypal.com':'https://api-m.sandbox.paypal.com';}
    public function planId(string $cycle): string{return (string)getenv($cycle==='yearly'?'PAYPAL_PLAN_YEARLY_ID':'PAYPAL_PLAN_MONTHLY_ID');}
    public function createSubscription(int $userId,string $cycle,string $requestKey): array {
        if(!$this->configured()||!in_array($cycle,['monthly','yearly'],true)||!preg_match('/^[a-f0-9]{32}$/',$requestKey)||!$this->planId($cycle))throw new \RuntimeException('Checkout is not configured.');
        $response=$this->request('POST','/v1/billing/subscriptions',[
            'plan_id'=>$this->planId($cycle),'custom_id'=>(string)$userId,
            'application_context'=>['brand_name'=>config('brand.name'),'user_action'=>'SUBSCRIBE_NOW','return_url'=>config('brand.app_url').url('paypal_return'),'cancel_url'=>config('brand.app_url').url('paypal_cancel')]
        ],['PayPal-Request-Id: '.$requestKey],true);
        $id=$response['id']??null;$approve=null;
        if(!is_string($id)||!preg_match('/^I-[A-Z0-9]{1,100}$/',$id)||!is_array($response['links']??null))throw new \RuntimeException('Invalid subscription response.');
        foreach($response['links'] as $link)if(is_array($link)&&in_array($link['rel']??'', ['approve','payer-action'],true)&&is_string($link['href']??null))$approve=$link['href'];
        $host=env('PAYPAL_MODE','sandbox')==='live'?'www.paypal.com':'www.sandbox.paypal.com';
        if(!$approve||parse_url($approve,PHP_URL_SCHEME)!=='https'||parse_url($approve,PHP_URL_HOST)!==$host||parse_url($approve,PHP_URL_USER)!==null||(parse_url($approve,PHP_URL_PORT)!==null&&parse_url($approve,PHP_URL_PORT)!==443)||preg_match('/[\r\n]/',$approve))throw new \RuntimeException('Invalid approval URL.');
        Database::transaction(function($pdo)use($userId,$id,$cycle){
            $ownerLock=$pdo->prepare('SELECT id FROM les_users WHERE id=?'.Database::forUpdate());$ownerLock->execute([$userId]);if(!$ownerLock->fetchColumn())throw new \RuntimeException('Unknown checkout account.');
            $stmt=$pdo->prepare('SELECT user_id FROM les_subscriptions WHERE paypal_subscription_id=?'.Database::forUpdate());$stmt->execute([$id]);$owner=$stmt->fetchColumn();
            if($owner!==false){if((int)$owner!==$userId)throw new \RuntimeException('Subscription ownership mismatch.');return;}
            $pdo->prepare("INSERT INTO les_subscriptions(user_id,paypal_subscription_id,plan,status,billing_cycle,created_at) VALUES (?,?,'managed','pending',?,?)")->execute([$userId,$id,$cycle,gmdate('Y-m-d H:i:s')]);
        });return ['id'=>$id,'approve_url'=>$approve];
    }
    public function processWebhook(string $raw,array $headers): string {
        if(!$this->configured()||!getenv('PAYPAL_WEBHOOK_ID'))throw new \RuntimeException('Webhook disabled.');
        if(strlen($raw)>262144)throw new \InvalidArgumentException('Payload too large.');
        $event=json_decode($raw,true,32,JSON_THROW_ON_ERROR);
        if(!is_array($event)||!is_string($event['id']??null)||!preg_match('/^[A-Za-z0-9-]{1,120}$/',$event['id'])||!is_string($event['event_type']??null)||strlen($event['event_type'])>120)throw new \InvalidArgumentException('Malformed event.');
        $h=array_change_key_case($headers,CASE_LOWER);
        foreach(['paypal-auth-algo','paypal-cert-url','paypal-transmission-id','paypal-transmission-sig','paypal-transmission-time'] as $key)if(!is_string($h[$key]??null)||strlen($h[$key])>4096||preg_match('/[\r\n]/',$h[$key]))throw new \InvalidArgumentException('Invalid webhook headers.');
        // Cert URL is sent to PayPal for verification, never fetched by this server.
        $verified=$this->request('POST','/v1/notifications/verify-webhook-signature',['auth_algo'=>$h['paypal-auth-algo'],'cert_url'=>$h['paypal-cert-url'],'transmission_id'=>$h['paypal-transmission-id'],'transmission_sig'=>$h['paypal-transmission-sig'],'transmission_time'=>$h['paypal-transmission-time'],'webhook_id'=>getenv('PAYPAL_WEBHOOK_ID'),'webhook_event'=>$event],[],true);
        if(($verified['verification_status']??'')!=='SUCCESS')throw new \RuntimeException('Invalid webhook signature.');
        $type=$event['event_type'];$id=$event['id'];
        $supported=['BILLING.SUBSCRIPTION.ACTIVATED','BILLING.SUBSCRIPTION.RE-ACTIVATED','BILLING.SUBSCRIPTION.UPDATED','BILLING.SUBSCRIPTION.SUSPENDED','BILLING.SUBSCRIPTION.CANCELLED','BILLING.SUBSCRIPTION.EXPIRED'];
        $snapshot=null;$subId=null;
        if(in_array($type,$supported,true)){
            $subId=$event['resource']['id']??null;
            if(!is_string($subId)||!preg_match('/^I-[A-Z0-9]{1,100}$/',$subId))throw new \InvalidArgumentException('Invalid subscription ID.');
            $snapshot=$this->request('GET','/v1/billing/subscriptions/'.rawurlencode($subId));
        }
        // Event insertion and entitlement change commit together. Unique key resolves duplicate races.
        try {
            Database::transaction(function($pdo)use($event,$id,$type,$subId,$snapshot){
                $stmt=$pdo->prepare('SELECT id FROM les_subscription_events WHERE paypal_event_id=?');$stmt->execute([$id]);if($stmt->fetchColumn())return;
                $pdo->prepare('INSERT INTO les_subscription_events(paypal_event_id,event_type,paypal_subscription_id,payload,received_at) VALUES (?,?,?,?,?)')->execute([$id,$type,$subId,json_encode(['id'=>$id,'event_type'=>$type,'subscription_id'=>$subId],JSON_THROW_ON_ERROR),gmdate('Y-m-d H:i:s')]);
                if(!$snapshot)return; // Unknown/sale events never change privileges.
                $q=$pdo->prepare('SELECT * FROM les_subscriptions WHERE paypal_subscription_id=?');$q->execute([$subId]);$sub=$q->fetch();
                if(!$sub)throw new \RuntimeException('No locally owned subscription. Retry after checkout persistence.');
                $lock=$pdo->prepare('SELECT id FROM les_users WHERE id=?'.Database::forUpdate());$lock->execute([$sub['user_id']]);$lock->fetch();
                $q=$pdo->prepare('SELECT * FROM les_subscriptions WHERE id=?'.Database::forUpdate());$q->execute([$sub['id']]);$sub=$q->fetch();
                if(($snapshot['id']??null)!==$subId || ($snapshot['plan_id']??null)!==$this->planId($sub['billing_cycle']) || (!is_string($snapshot['custom_id']??null) || $snapshot['custom_id']!==(string)$sub['user_id']))throw new \RuntimeException('Verified subscription binding mismatch.');
                $when=$snapshot['update_time']??($event['create_time']??null);
                if(!is_string($when)||strtotime($when)===false)throw new \InvalidArgumentException('Missing event time.');
                $when=(new \DateTimeImmutable($when))->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s.u');
                if($sub['provider_event_at'] && strcmp($when,$sub['provider_event_at'])<0)return;
                $map=['ACTIVE'=>'active','SUSPENDED'=>'suspended','CANCELLED'=>'cancelled','EXPIRED'=>'expired','APPROVAL_PENDING'=>'pending','APPROVED'=>'pending'];
                $resourceStatus=is_string($snapshot['status']??null)?$snapshot['status']:'';
                $status=$map[$resourceStatus]??null;if(!$status)throw new \RuntimeException('Unknown provider status.');
                if($sub['provider_event_at']===$when && $status==='active' && in_array($sub['status'],['cancelled','expired','suspended'],true))return;
                $end=$snapshot['billing_info']['next_billing_time']??null;$end=is_string($end)&&strtotime($end)!==false?gmdate('Y-m-d H:i:s',strtotime($end)):null;
                $pdo->prepare('UPDATE les_subscriptions SET status=?,current_period_end=?,updated_at=?,provider_event_at=? WHERE id=?')->execute([$status,$end,gmdate('Y-m-d H:i:s'),$when,$sub['id']]);
                $q=$pdo->prepare("SELECT id FROM les_subscriptions WHERE user_id=? AND plan='managed' AND status='active' LIMIT 1".Database::forUpdate());$q->execute([$sub['user_id']]);$plan=$q->fetchColumn()?'managed':'free';
                $pdo->prepare("UPDATE les_users SET plan=? WHERE id=? AND plan IN ('free','managed')")->execute([$plan,$sub['user_id']]);
            });
        }catch(\PDOException $e){if(!Database::duplicate($e))throw $e;}
        return $id;
    }
    private function token(): string {
        if($this->accessToken!==null)return $this->accessToken;
        $data=$this->http->request('POST',$this->baseUrl().'/v1/oauth2/token','grant_type=client_credentials',['Content-Type: application/x-www-form-urlencoded'],getenv('PAYPAL_CLIENT_ID').':'.getenv('PAYPAL_CLIENT_SECRET'));
        if(!is_string($data['access_token']??null)||preg_match('/[\r\n]/',$data['access_token'])||strlen($data['access_token'])>4096)throw new \RuntimeException('Invalid provider token.');
        return $this->accessToken=$data['access_token'];
    }
    private function request(string $method,string $path,array $body=[],array $extra=[],bool $safe=false): array {
        return $this->http->request($method,$this->baseUrl().$path,$body?json_encode($body,JSON_THROW_ON_ERROR):null,array_merge(['Authorization: Bearer '.$this->token(),'Content-Type: application/json'],$extra),null,$safe);
    }
}
