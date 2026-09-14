<?php
/**
 * Central configuration for the LinkEasy Social public website.
 * Everything brand/pricing/platform/route related lives here so the move
 * from laheef.dev to linkeasysocial.com is a single APP_URL change.
 *
 * Returned array is exposed to views through config()/View::data().
 */

return [

    // -----------------------------------------------------------------
    // Brand
    // -----------------------------------------------------------------
    'brand' => [
        'name'       => env('APP_NAME', 'LinkEasy Social'),
        'short_name' => 'LinkEasy',
        'app_url'    => rtrim(env('APP_URL', 'https://laheef.dev'), '/'),
        'support_email' => env('SUPPORT_EMAIL', 'support@laheef.dev'),
        'github_url' => env('GITHUB_URL', ''),
        'logo'       => '/assets/img/logo-mark.png',
        'logo_light' => '/assets/img/logo-mark-light.png',
        'og_image'   => '/assets/img/og-image.png',
        'default_social_image' => '/assets/img/og-image.png',
        // Social profiles — leave empty to HIDE the icon (never fake URLs).
        'social' => [
            'instagram' => env('SOCIAL_INSTAGRAM_URL', ''),
            'facebook'  => env('SOCIAL_FACEBOOK_URL', ''),
            'tiktok'    => env('SOCIAL_TIKTOK_URL', ''),
            'linkedin'  => env('SOCIAL_LINKEDIN_URL', ''),
            'youtube'   => env('SOCIAL_YOUTUBE_URL', ''),
            'x'         => env('SOCIAL_X_URL', ''),
        ],
        'description' => 'Create, schedule, publish and analyze social media content for every platform — from one powerful workspace.',
        'tagline'     => 'Manage your social media workflow from one powerful workspace.',
    ],

    // -----------------------------------------------------------------
    // Central route map — change paths in one place only
    // -----------------------------------------------------------------
    'routes' => [
        'home'            => '/',
        'features'        => '/#features',
        'how_it_works'    => '/#how-it-works',
        'integrations'    => '/#integrations',
        'pricing'         => '/#pricing',
        'faq'             => '/#faq',
        'contact'         => '/contact',
        'about' => '/about',
        'help' => '/help',
        'legal' => '/legal',
        'security' => '/security',
        'acceptable_use' => '/acceptable-use',
        'copyright' => '/copyright',
        'disclosures' => '/disclosures',
        'data_deletion' => '/data-deletion',
        'refunds' => '/refunds',

        'login'           => '/login',
        'signup'          => '/signup',
        'logout'          => '/logout',
        'forgot_password' => '/forgot-password',
        'reset_password'  => '/reset-password',
        'dashboard'       => '/dashboard',
        'google_login'    => '/auth/google',
        'google_callback' => '/auth/google/callback',
        'paypal_create'   => '/billing/paypal/create',
        'paypal_webhook'  => '/billing/paypal/webhook',
        'paypal_return'   => '/billing/paypal/return',
        'paypal_cancel'   => '/billing/paypal/cancel',
        'privacy'         => '/privacy',
        'terms'           => '/terms',
        'cookies'         => '/cookies',
        'session_check'   => '/auth/session-check',
    ],

    // -----------------------------------------------------------------
    // Supported / planned social platforms
    // 'available' => false renders a subtle "planned" state instead of a promise
    // -----------------------------------------------------------------
    'platforms' => require __DIR__ . '/platforms.generated.php',

    // -----------------------------------------------------------------
    // Pricing — single source of truth. Limits double as the plan-gate
    // rules in src/PlanGate.php. Adjust numbers here, nowhere else.
    // -----------------------------------------------------------------
    'pricing' => [
        'currency'        => '$',
        'managed_monthly' => (int) env('PRICE_MANAGED_MONTHLY', 19),
        'managed_yearly'  => (int) env('PRICE_MANAGED_YEARLY', 190), // 2 months free
        'yearly_save_pct' => 17,
        'plans' => [
            // Legacy ID retained so existing plan assignments keep working.
            'opensource' => [
                'id' => 'opensource', 'name' => 'Bring Your Own API',
                'price' => 0, 'cadence' => '/month',
                'description' => 'Our hosted workspace. Your API credentials. Connect all your accounts without a monthly platform fee.',
                'cta' => 'Get BYO API access', 'cta_url_key' => 'contact', 'highlight' => false,
                'features' => [
                    'Hosted on LinkEasy Social — no server needed',
                    'Unlimited connected social accounts',
                    'Multiple channels per network, including YouTube',
                    'Your own social-platform API credentials',
                    'No platform-imposed daily, weekly or monthly posting quota',
                    'Provider rate limits, API fees and approval rules still apply',
                ],
                'limits' => ['max_social_accounts' => null, 'max_scheduled_posts' => null, 'max_projects' => null, 'analytics_level' => 'advanced'],
            ],
            'free' => [
                'id'       => 'free',
                'name'     => 'Free',
                'price'    => 0,
                'cadence'  => '/month',
                'description' => 'Everything you need to get started with organized social media — hosted and ready in minutes.',
                'cta'      => 'Start Free',
                'cta_url_key' => 'signup',
                'highlight' => false,
                'no_card'  => false,
                'features' => [
                    '3 connected social accounts',
                    '1 project workspace',
                    '30 scheduled posts in your queue',
                    'Visual calendar & post composer',
                    'Basic performance analytics',
                ],
                'limits' => [
                    'max_projects'       => 1,
                    'max_social_accounts' => 3,
                    'max_scheduled_posts' => 30,
                    'max_team_members'   => 1,
                    'analytics_level'    => 'basic',
                    'storage_bytes'      => 500 * 1024 * 1024,
                ],
            ],
            'setup' => [
                'id' => 'setup', 'name' => 'One-Time Setup', 'price' => null,
                'price_note' => 'Custom quote', 'cadence' => 'one time',
                'description' => 'Prefer a helping hand? We configure your hosted BYO API workspace and guide you through your first connections.',
                'cta' => 'Request a setup quote', 'cta_url_key' => 'contact', 'highlight' => false,
                'features' => [
                    'One-time paid onboarding, agreed before work begins',
                    'Your API credentials, configured with you',
                    'Unlimited connected social accounts',
                    'No platform-imposed posting quota',
                    'Guided connections and a workspace walkthrough',
                    'No recurring platform fee while the app operates',
                    'Provider API charges and limits remain your responsibility',
                ],
                'limits' => ['max_social_accounts' => null, 'max_scheduled_posts' => null, 'max_projects' => null, 'analytics_level' => 'advanced'],
            ],
            'managed' => [
                'id'       => 'managed',
                'name'     => 'Managed',
                'badge'    => 'Most popular',
                'price'    => (int) env('PRICE_MANAGED_MONTHLY', 19),
                'yearly_price' => (int) env('PRICE_MANAGED_YEARLY', 190),
                'cadence'  => '/month',
                'description' => 'You connect your accounts and create content — we handle hosting, API credentials, maintenance and updates.',
                'cta'      => 'Start Subscription',
                'cta_url_key' => 'paypal_create',
                'highlight' => true,
                'features' => [
                    'Everything in Free, plus:',
                    '25 connected social accounts',
                    '10 project workspaces',
                    'Unlimited scheduled posts',
                    'Advanced analytics & downloadable reports',
                    'API credentials managed for you',
                    'Priority support & automatic updates',
                ],
                'limits' => [
                    'max_projects'       => 10,
                    'max_social_accounts' => 25,
                    'max_scheduled_posts' => null, // unlimited
                    'max_team_members'   => 5,
                    'analytics_level'    => 'advanced',
                    'storage_bytes'      => 10 * 1024 * 1024 * 1024,
                ],
            ],
        ],
    ],

    // -----------------------------------------------------------------
    // Editable feature-comparison table. Values: true / false / string
    // -----------------------------------------------------------------
    'comparison' => [
        ['label' => 'Hosting & infrastructure', 'opensource' => 'Included', 'free' => 'Included', 'setup' => 'Included', 'managed' => 'Fully managed'],
        ['label' => 'Your own server needed', 'opensource' => false, 'free' => false, 'setup' => false, 'managed' => false],
        ['label' => 'Social accounts',           'opensource' => 'Unlimited', 'free' => '3', 'setup' => 'Unlimited', 'managed' => '25'],
        ['label' => 'Project workspaces',        'opensource' => 'Unlimited', 'free' => '1', 'setup' => 'Unlimited', 'managed' => '10'],
        ['label' => 'Scheduled posts',           'opensource' => 'Unlimited', 'free' => '30 in queue', 'setup' => 'Unlimited', 'managed' => 'Unlimited'],
        ['label' => 'Visual content calendar',   'opensource' => true, 'free' => true, 'setup' => true, 'managed' => true],
        ['label' => 'Multi-platform publishing', 'opensource' => true, 'free' => true, 'setup' => true, 'managed' => true],
        ['label' => 'Analytics',                 'opensource' => 'Advanced', 'free' => 'Basic', 'setup' => 'Advanced', 'managed' => 'Advanced'],
        ['label' => 'Downloadable reports',      'opensource' => true, 'free' => false, 'setup' => true, 'managed' => true],
        ['label' => 'Social API credentials',    'opensource' => 'You provide', 'free' => 'Guided setup', 'setup' => 'Configured for you', 'managed' => 'Managed by us'],
        ['label' => 'API onboarding',  'opensource' => 'You configure', 'free' => false, 'setup' => 'Included', 'managed' => 'Included'],
        ['label' => 'Maintenance & updates',     'opensource' => 'Automatic', 'free' => 'Automatic', 'setup' => 'Automatic', 'managed' => 'Automatic & priority'],
        ['label' => 'Support',                   'opensource' => 'Email', 'free' => 'Help center', 'setup' => 'Onboarding session', 'managed' => 'Priority email'],
        ['label' => 'Billing',                   'opensource' => 'Free', 'free' => 'Free', 'setup' => 'One-time quote', 'managed' => 'Monthly or yearly'],
    ],

    // -----------------------------------------------------------------
    // FAQ — feeds both the accordion and the FAQPage JSON-LD schema
    // -----------------------------------------------------------------
    'faqs' => [
        ['q' => 'What is LinkEasy Social?',
         'a' => 'LinkEasy Social is a social media management workspace that lets you create, schedule, publish and analyze content for multiple social platforms from one dashboard, instead of jumping between each network separately.'],
        ['q' => 'Which social platforms can I connect?',
         'a' => 'Thirteen connections are available today: Instagram, Facebook, TikTok, LinkedIn, YouTube, Pinterest, X, Threads, Google Business, Bluesky, WhatsApp, WordPress and Tumblr. More than forty additional networks — including Reddit, Telegram, Discord, Mastodon, Dailymotion and Medium — are on the roadmap and shown as “planned” in the integrations section. What each connection can do depends on that platform’s API access rules and your plan.'],
        ['q' => 'Is there a free version?',
         'a' => 'Yes. The hosted Free plan includes 3 connected social accounts, 1 project and a 30-post scheduled queue at no monthly cost. The hosted Bring Your Own API option has unlimited connected accounts and no platform posting quota; you supply your own API credentials.'],
        ['q' => 'Do I need my own API keys?',
         'a' => 'Yes for Bring Your Own API and One-Time Setup. Both run on our platform using your social-platform developer credentials. Provider quotas, approval requirements and any API charges still apply. On the Free and Managed plans the guided/managed setup handles platform configuration for you.'],
        ['q' => 'What is the One-Time Setup option?',
         'a' => 'It is a custom-quoted, one-time onboarding service for our hosted Bring Your Own API plan. We help configure your credentials, connect your accounts and show you the workspace. Afterwards there is no recurring platform fee for as long as the app operates. It is not a guarantee of perpetual service, and third-party API fees still apply.'],
        ['q' => 'What does the Managed subscription include?',
         'a' => 'Managed hosting with API credentials and infrastructure handled by our team, higher account and project limits, unlimited scheduled posts, advanced analytics, reports, automatic updates and priority support.'],
        ['q' => 'Can I manage multiple brands?',
         'a' => 'Yes. Work is organized into projects, so agencies and teams can group the social accounts of different brands or clients and switch between them from one workspace. Project limits depend on your plan.'],
        ['q' => 'Can I cancel my subscription?',
         'a' => 'Yes, you can cancel from your account at any time. Your Managed subscription remains active until the end of the current billing period, after which your account moves to the Free plan limits.'],
        ['q' => 'How does PayPal billing work?',
         'a' => 'When you start a Managed subscription you are redirected to PayPal’s secure checkout. PayPal confirms every payment and subscription change directly to our server over a verified webhook — access is always based on the verified server-side subscription state, never on a browser message.'],
        ['q' => 'Is my account secure?',
         'a' => 'Use official authorization flows where supported, keep your API credentials private and grant only the permissions you need. Website sessions use HttpOnly SameSite cookies and the Secure flag over HTTPS. PayPal handles checkout. See our security page for reporting guidance; no system can guarantee absolute security.'],
        ['q' => 'Can I export my data?',
         'a' => 'Yes. Your content and analytics belong to you; report exports are available on eligible plans, and you can contact support about access to or deletion of your account data.'],
        ['q' => 'Can I connect ten YouTube channels on BYO API?',
         'a' => 'Yes. Bring Your Own API and One-Time Setup allow unlimited connected social accounts, including multiple channels on the same network. Each channel must be authorized and eligible under the provider rules. There is no LinkEasy Social posting quota on these options, but provider daily quotas, rate limits and API charges still apply. Both options are hosted by us, not self-hosted.'],
    ],

    // -----------------------------------------------------------------
    // Use cases (interactive tabs)
    // -----------------------------------------------------------------
    'use_cases' => [
        ['slug' => 'creators', 'name' => 'Creators', 'icon' => 'sparkles',
         'title' => 'Spend your energy creating, not tab-switching',
         'description' => 'Draft once, tailor for each platform, and schedule weeks of content in one sitting — then watch which posts grow your audience.',
         'points' => ['Batch-create posts and reels', 'See your whole week at a glance', 'Learn what your audience responds to']],
        ['slug' => 'small-business', 'name' => 'Small Businesses', 'icon' => 'store',
         'title' => 'A consistent social presence without the overhead',
         'description' => 'Keep your business posting consistently across Instagram, Facebook and more — no agency, no scattered spreadsheets, no missed days.',
         'points' => ['Post consistently on a schedule', 'Reply from one shared inbox', 'Track reach and follower growth']],
        ['slug' => 'agencies', 'name' => 'Agencies', 'icon' => 'briefcase',
         'title' => 'Every client, organized into projects',
         'description' => 'Group accounts by client, plan content in shared calendars and report results with polished, export-ready reports.',
         'points' => ['One workspace per brand or client', 'Team roles and permissions', 'Client-ready performance reports']],
        ['slug' => 'teams', 'name' => 'Marketing Teams', 'icon' => 'users',
         'title' => 'Plan, publish and analyze together',
         'description' => 'Shared calendars, a media library and clear post statuses keep everyone aligned from idea to published post.',
         'points' => ['Collaborative content planning', 'Shared brand asset library', 'Approval-ready post statuses']],
        ['slug' => 'freelancers', 'name' => 'Freelancers', 'icon' => 'zap',
         'title' => 'Run every client account efficiently',
         'description' => 'Switch between client projects in seconds and reuse workflows, media and schedules instead of starting from scratch.',
         'points' => ['Quick project switching', 'Reusable captions and media', 'Simple reporting for clients']],
    ],

    'seo' => [
        'home' => [
            'title'       => 'LinkEasy Social — Social Media Management Made Simple',
            'description' => 'Create, schedule, publish and analyze social media content for every platform from one powerful workspace. Free plan available.',
        ],
    ],
];
