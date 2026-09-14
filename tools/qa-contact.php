<?php
/** Non-destructive CLI validation tests. php tools/qa-contact.php */
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require dirname(__DIR__) . '/src/bootstrap.php';
use App\ContactInquiry as Inquiry;
function check(bool $ok, string $label): void { if (!$ok) throw new RuntimeException($label); echo "PASS $label\n"; }
$base = ['name'=>'QA Example','email'=>'qa@example.test','subject'=>'General question','message'=>'Please explain the available setup options.'];
[$data,$errors,$quote] = Inquiry::validate($base);
check(!$errors && !$quote && Inquiry::message($data,$quote)===$base['message'], 'general enquiry needs no quote fields');
$request = array_merge($base,['subject'=>'One-Time Setup','use_case'=>'Clients / agency','account_count'=>'8','api_status'=>'I have some, but need help','platforms'=>['Instagram','LinkedIn'],'services'=>['Hosted workspace onboarding'],'organization'=>'QA Brand','website_url'=>'https://example.test','current_tool'=>'Spreadsheets','timeline'=>'Within a month','budget'=>'Not sure yet','timezone'=>'Asia/Karachi','phone'=>'+92 000 0000000']);
[$data,$errors,$quote] = Inquiry::validate($request);
check(!$errors && $quote, 'valid setup brief accepted');
$text = Inquiry::message($data,$quote);
foreach (['Instagram, LinkedIn','Hosted workspace onboarding','QA Brand','Spreadsheets','8','Not sure yet','Asia/Karachi'] as $part) check(str_contains($text,$part),'serialized '.$part);
foreach ([['account_count'=>'0'],['account_count'=>'2.5'],['account_count'=>'1'],['account_count'=>'10000'],['platforms'=>['Unknown']],['platforms'=>'Instagram'],['platforms'=>[['Instagram']]],['services'=>[]],['services'=>['Fabricated service']],['api_status'=>'invalid'],['website_url'=>'javascript:alert(1)'],['organization'=>str_repeat('a',121)],['name'=>['a']],['subject'=>'Unlisted'],['message'=>str_repeat('x',5001)],['email'=>"a@example.test\ncc: x@example.test"]] as $bad) {
 [, $errors] = Inquiry::validate(array_merge($request,$bad)); check((bool)$errors,'reject malformed '.array_key_first($bad));
}
[, $errors] = Inquiry::validate(array_merge($base,['subject'=>'Request a quote']));
foreach (['use_case','account_count','api_status','platforms','services'] as $key) check(isset($errors[$key]),'required quote field '.$key);
[, $errors] = Inquiry::validate(array_merge($base,['subject'=>'Managed subscription (September)']));check(!$errors,'legacy Managed preselection accepted');
[$data,$errors] = Inquiry::validate(array_merge($request,['organization'=>'<script>alert(1)</script>']));check(!$errors && str_contains(e($data['organization']),'&lt;script&gt;'),'free text escaped for HTML rendering');
check(count(Inquiry::platforms())===13,'13 available platform options, no planned connections');
