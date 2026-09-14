<?php
return [
    'subjects' => ['General question', 'One-Time Setup', 'Request a quote', 'Bring Your Own API', 'Managed subscription', 'Support', 'Privacy & data deletion', 'Copyright report', 'Security report', 'Billing & refunds'],
    'quote_subjects' => ['One-Time Setup', 'Request a quote'],
    'fields' => [
        'organization' => ['label' => 'Business or brand name', 'type' => 'text', 'max' => 120, 'required' => false],
        'website_url' => ['label' => 'Website or public profile URL', 'type' => 'url', 'max' => 300, 'required' => false],
        'use_case' => ['label' => 'Who are you setting up for?', 'type' => 'select', 'required' => true, 'options' => ['Myself / creator', 'My business', 'Clients / agency', 'An internal marketing team', 'Other']],
        'account_count' => ['label' => 'Total social accounts or channels', 'type' => 'number', 'required' => true, 'max' => 9999],
        'api_status' => ['label' => 'Where are you with API credentials?', 'type' => 'select', 'required' => true, 'options' => ['I have the credentials ready', 'I have some, but need help', 'I need help with all of them', 'I am not sure yet']],
        'current_tool' => ['label' => 'Current tool or workflow', 'type' => 'text', 'max' => 160, 'required' => false],
        'timeline' => ['label' => 'Preferred start time', 'type' => 'select', 'required' => false, 'options' => ['As soon as practical', 'Within a month', 'In 1–3 months', 'Just exploring']],
        'budget' => ['label' => 'Indicative one-time budget and currency', 'type' => 'text', 'max' => 100, 'required' => false],
        'timezone' => ['label' => 'Country / time zone', 'type' => 'text', 'max' => 100, 'required' => false],
        'phone' => ['label' => 'Phone / WhatsApp, including country code', 'type' => 'tel', 'max' => 50, 'required' => false],
    ],
    'services' => ['Hosted workspace onboarding', 'API application / credential guidance', 'Connecting multiple accounts', 'Content / workflow migration guidance', 'Team walkthrough / training', 'Help choosing the right plan'],
];
