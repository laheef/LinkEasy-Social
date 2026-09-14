<?php
/** Original policy drafts. Review against the deployed app and applicable law before publication. */
return [
    'privacy' => [
        'title' => 'Privacy Policy', 'category' => 'Your data', 'icon' => 'shield',
        'summary' => 'What information is involved in using LinkEasy Social, why it is needed, and how to contact us about it.',
        'review' => true,
        'sections' => [
            ['Who this policy covers', 'This policy covers the LinkEasy Social website and hosted workspace, including Bring Your Own API and One-Time Setup. Supplying your own API credentials does not make your workspace self-hosted. Contact the support address below for privacy questions. The operator’s legal identity and jurisdiction must be confirmed before this draft is adopted.'],
            ['Information you provide', 'Account registration involves your name, email address and a hashed password, or an identifier from your chosen sign-in provider. Support requests include the contact details and message you submit. Setup quote requests can also include a business name, public website, account and platform needs, API readiness, requested services and optional budget, timing, time-zone or phone details. These are used to assess the requested scope and respond to your enquiry. When you use workspace features, content may include captions, media, schedules, project details and connected-account identifiers. Do not send passwords or API secrets through the public contact form.'],
            ['Connected platforms and credentials', 'Connecting a platform can involve authorization tokens, account identifiers and permission scopes. BYO API customers provide their own developer credentials through the workspace’s credential setup, where available. The information a platform returns depends on the permissions you approve and the features you use. Only authorize accounts you are entitled to manage. Revoking access at the provider prevents future authorized requests but does not automatically erase existing records here.'],
            ['Why information is used', 'Information is used to authenticate accounts, provide requested workspace functions, handle support and billing, and investigate security or service issues. Where data-protection law requires a legal basis, these purposes may involve performance of a contract, legitimate interests, legal obligations or consent. The applicable basis must be assessed for each deployed feature and jurisdiction.'],
            ['Providers and payments', 'Hosting and transactional-email providers process information needed to operate the service. Google processes its own sign-in flow when you choose it. PayPal processes subscription checkout; this website stores subscription references and event records rather than payment-card numbers. Connected social platforms receive the content and requests you instruct the service to send. Their own policies govern their processing. Any international-transfer safeguards and a complete vendor list need operator review.'],
            ['Retention and requests', 'Information is retained for service operation and applicable billing, security and legal obligations. Exact retention periods, backup deletion and vendor retention must be confirmed for the production environment. You can request access, correction, export or deletion through our Data Deletion page or support address. Identity checks may be needed before a request is fulfilled; applicable legal exceptions may limit a request.'],
            ['Security and your choices', 'Website passwords are hashed, database queries use prepared statements, and sessions use HttpOnly and SameSite cookies with Secure enabled over HTTPS. These controls do not guarantee absolute security. Review connected-app permissions regularly and revoke access you no longer need. Do not assume a feature or certification not expressly documented on our Security page.'],
            ['Children, changes and questions', 'The service is intended for people permitted to manage the social accounts they connect. Do not submit a child’s personal information without a lawful basis. Questions about age eligibility, privacy rights or changes to this policy should be directed to support. Material changes should be communicated when required by applicable law.'],
        ],
    ],
    'terms' => [
        'title' => 'Terms of Service', 'category' => 'Using the platform', 'icon' => 'file-text',
        'summary' => 'The rules for your hosted workspace, connected accounts and paid services.', 'review' => true,
        'sections' => [
            ['The service', 'LinkEasy Social brings social account management, content planning, publishing and reporting into a hosted workspace. Features vary by plan and connected platform. A roadmap label or “Coming Soon” label is not a promise that a feature is currently available. These draft terms require the operator’s legal identity, governing law and dispute provisions to be reviewed before adoption.'],
            ['Your account and authority', 'Provide accurate account details, protect your credentials and only connect accounts you have permission to manage. You are responsible for the instructions and content submitted through your account. Contact support if you suspect unauthorized access.'],
            ['Bring Your Own API', 'This option runs on our platform, not on your server. You supply social-platform API credentials and authorize your accounts. BYO API has unlimited connected social accounts and no LinkEasy Social daily, weekly or monthly posting quota. Provider rate limits, usage quotas, API charges, review requirements and acceptable-use rules still apply. Unlimited is not a right to overload infrastructure or bypass a provider’s controls.'],
            ['One-Time Setup', 'We agree a custom quote and onboarding scope before work begins. Setup covers help configuring your hosted BYO API workspace, API credentials and account connections. After setup, no recurring platform fee applies for as long as the app operates. This is not a guarantee of perpetual availability or permanent support; external API fees and requirements remain your responsibility.'],
            ['Free and Managed plans', 'Free and Managed allowances are shown in the pricing table. Managed subscriptions are billed through PayPal on the selected monthly or annual cycle. Review the amount, billing interval and authorization before confirming checkout. Cancel recurring billing through available account controls or your PayPal automatic-payment settings; contact support if you need assistance. See Billing & Refunds for requests and setup-scope questions.'],
            ['Your content and permissions', 'You retain rights in content you own. You authorize the processing and transmission needed to carry out your instructions. You must have the rights and permissions needed to publish uploaded material, including images, music and information about other people. Follow our Acceptable Use guidance and the terms of each connected network.'],
            ['Third-party platforms', 'Social networks control eligibility, authentication, content rules, available endpoints and rate limits. Publishing and analytics may be delayed or unavailable because of platform changes, account restrictions or outages. We cannot guarantee approval of developer applications or uninterrupted access to a third-party API.'],
            ['Suspension, availability and liability', 'Access may be restricted to address abuse, security risks, unpaid charges or legal requirements. Services are supplied subject to applicable law; nothing here removes non-waivable consumer rights. Availability promises, liability provisions and termination notice obligations require legal review before this draft is adopted.'],
        ],
    ],
    'cookies' => [
        'title' => 'Cookie Policy', 'category' => 'Your browser', 'icon' => 'globe',
        'summary' => 'A plain-language guide to the cookies used by this website.', 'review' => false,
        'sections' => [
            ['The essential session cookie', 'The first-party les_session cookie associates your browser with a server-side session. It supports login, CSRF protection, temporary form messages and OAuth state. It is HttpOnly, uses SameSite=Lax and receives the Secure flag over HTTPS. It is configured as a browser-session cookie; browser session-restoration settings can affect when it is cleared.'],
            ['Session timeout', 'The website has a one-hour idle-session timeout. This is a server-side access control, not a separate tracking cookie. Signing out invalidates your login session. OAuth state is stored in that session rather than in a separate OAuth cookie.'],
            ['Analytics and preferences', 'This public website does not currently include advertising or third-party analytics scripts. Its pricing toggle is an on-page selection. The les-motion localStorage preference remembers whether you paused decorative website animations. It stays in your browser until you clear site data or change it; it is not sent as a tracking identifier. If tracking or persistent preference storage is added, this policy and any required consent controls must be updated before those tools are enabled.'],
            ['External sign-in and checkout', 'When you leave this website for Google sign-in or PayPal checkout, those providers can set cookies under their own policies. Their cookies are not described as LinkEasy Social’s first-party cookies.'],
            ['Browser controls', 'You can clear cookies or block them in your browser settings. Blocking the session cookie can prevent login and protected form submissions from working. Contact support if you have questions about a cookie associated with this website.'],
        ],
    ],
    'acceptable-use' => [
        'title' => 'Acceptable Use', 'category' => 'Platform rules', 'icon' => 'check-circle',
        'summary' => 'Use automation responsibly. Respect your audience, other creators and the platforms you connect.', 'review' => true,
        'sections' => [
            ['Publish with permission', 'Only connect accounts you own or are authorized to manage. Publish material you have permission to use and obtain any required consent for personal information, endorsements or regulated promotions.'],
            ['No spam or deceptive activity', 'Do not use the service for unsolicited bulk spam, impersonation, fraudulent offers, coordinated fake engagement or misleading account activity. Follow the rules of each connected platform, including its requirements for automation and disclosure.'],
            ['Respect systems and quotas', 'Do not bypass API limits, rotate credentials to evade restrictions, attack infrastructure or access another customer’s data. Unlimited connected accounts on BYO API and One-Time Setup do not override provider quotas or authorize abusive usage.'],
            ['Harmful and unlawful material', 'Do not publish unlawful content, threats, harassment, child sexual exploitation material, malware, or content that violates another person’s intellectual-property or privacy rights.'],
            ['Report misuse', 'Send a description of the issue, relevant URLs and supporting context through Contact Support. Do not include other people’s private credentials. Reports may lead to investigation or restrictions where necessary to protect users and the service.'],
        ],
    ],
    'copyright' => [
        'title' => 'Copyright & Takedown Requests', 'category' => 'Creator rights', 'icon' => 'file-text',
        'summary' => 'How to report material you believe infringes your copyright, including information commonly needed for a DMCA notice.', 'review' => true,
        'sections' => [
            ['Before making a report', 'Make sure you own the relevant rights or are authorized to act for the rights holder. Consider whether the use is licensed or permitted by law. This page provides a reporting route, not a determination that a particular use is infringing.'],
            ['Information to include', 'Provide your name, contact information, a description of the copyrighted work, the exact location of the material at issue, and enough detail to identify it. For a notice under the U.S. DMCA, include a good-faith statement that the use is not authorized by the owner, its agent or the law; a statement, under penalty of perjury, that the information is accurate and you are authorized to act; and your physical or electronic signature.'],
            ['Where to send your report', 'Use our support email with the subject “Copyright report” or the contact form. This support route is not represented as a registered DMCA designated agent. If the operator relies on a formal statutory agent process, its verified name, address and registration details must be added before adoption. Do not submit highly sensitive documents through an ordinary support form.'],
            ['What happens next', 'Reports can require clarification, review and communication with the account holder. Access to material may be restricted where appropriate. A report does not guarantee removal, and material published on a third-party social network may also need to be reported directly to that network.'],
            ['Disputes and counter-notices', 'If you believe a restriction was mistaken, contact support with the relevant notice reference and explanation. Formal counter-notice requirements vary by jurisdiction and may involve consent to jurisdiction and disclosure of contact details. Seek independent advice before submitting a statutory counter-notice.'],
        ],
    ],
    'disclosures' => [
        'title' => 'Platform Disclosures', 'category' => 'Clear expectations', 'icon' => 'info',
        'summary' => 'How to read our integration labels, product illustrations and pricing promises.', 'review' => false,
        'sections' => [
            ['Independent product', 'LinkEasy Social is an independent service. Platform names, logos and trademarks belong to their owners and identify integrations; their display does not imply sponsorship, endorsement or affiliation.'],
            ['Available versus planned', 'Connections marked available are distinct from those marked Soon or planned. The actions supported by each connection depend on the platform’s API, account eligibility and your plan. AI features marked Coming Soon are not included as currently available capabilities.'],
            ['Illustrative product previews', 'Landing-page workspace cards, calendars, media and charts illustrate product workflows. Sample handles, content and visualizations are not customer testimonials, verified customer outcomes or promises of reach, engagement or follower growth.'],
            ['Unlimited accounts, not unlimited provider access', 'BYO API and One-Time Setup have no LinkEasy Social account-count or posting quota. Networks can impose their own daily or other quotas, charge API fees, restrict content or require app review. Connecting multiple channels still requires authorization for each channel. Free and Managed retain their published limits.'],
            ['Lifetime wording', 'For One-Time Setup, continued use without a recurring platform fee lasts while the app operates. It is not a perpetual-service guarantee and does not include third-party API charges. The setup scope and one-time price are agreed in a custom quote.'],
            ['Commercial relationships', 'Any paid endorsement, sponsored placement or affiliate relationship must be identified alongside the relevant recommendation. Integration logos alone should not be read as a commercial recommendation or partnership.'],
        ],
    ],
    'data-deletion' => [
        'title' => 'Data Access & Deletion', 'category' => 'Your choices', 'icon' => 'database',
        'summary' => 'Request a copy of your account data, a correction or deletion—and revoke connected-app access.', 'review' => false,
        'sections' => [
            ['1. Send your request', 'Contact support from the email address associated with your account. Use “Privacy & data deletion” as the subject and say whether you want access, correction, export or deletion. Identify the relevant workspace or connected platform without including passwords, tokens or API keys.'],
            ['2. Verify ownership', 'We may ask for information needed to confirm account ownership or authority to act for a workspace. This helps prevent someone else from obtaining or deleting your data. The form submission acknowledges receipt; it is not an automatic deletion action.'],
            ['3. Review and confirmation', 'Support will review the request, explain any required clarification and respond within applicable legal timeframes. Billing records, security records and information required by law may need to be retained. Deletion from backups and service providers must follow their applicable retention processes.'],
            ['4. Revoke connected access', 'You can also revoke LinkEasy Social access in each platform’s connected-app or security settings. For Google, review your Google Account’s third-party connections; for Meta, review the relevant Apps and Websites or Business Integrations settings. Revocation and deletion are separate actions: request deletion here if you want records removed from LinkEasy Social.'],
            ['Content already published elsewhere', 'Deleting a LinkEasy Social account does not necessarily delete posts already delivered to another platform. Manage or delete those posts on the destination platform. Cancel any recurring subscription separately before closing your account.'],
        ],
    ],
    'refunds' => [
        'title' => 'Billing & Refunds', 'category' => 'Payments', 'icon' => 'credit-card',
        'summary' => 'Understand subscription cancellation, one-time setup quotes and how to raise a billing question.', 'review' => true,
        'sections' => [
            ['Managed subscriptions', 'Managed is billed through PayPal at the interval you confirm during checkout. The yearly price is an annual charge, even when the pricing card also shows a monthly equivalent. Keep your transaction and subscription references for support.'],
            ['Cancelling renewal', 'Use the subscription controls available in your account or PayPal’s automatic-payment settings to cancel future renewal. Contact support if you cannot locate the subscription. Cancellation and a refund request are separate actions; cancelling does not by itself refund a prior payment.'],
            ['One-Time Setup quotes', 'The setup fee and scope are agreed before work begins. Ask for the deliverables, prerequisites, estimated timeline and cancellation terms in the written quote. The continuing BYO API workspace has no recurring platform fee while the app operates; provider fees are separate.'],
            ['Requesting a review', 'Send the account email, transaction reference, payment date and a short explanation to support with the subject “Billing & refunds”. Do not send card details or PayPal passwords. Billing errors, duplicate charges, service concerns and any statutory withdrawal rights should be reviewed individually.'],
            ['Your legal rights', 'Nothing on this page excludes rights that cannot lawfully be excluded. A specific voluntary refund window and setup cancellation schedule have not yet been adopted in this draft; the operator must confirm them before publishing a final policy.'],
        ],
    ],
    'security' => [
        'title' => 'Security & Responsible Disclosure', 'category' => 'Trust', 'icon' => 'lock',
        'summary' => 'Practical account protection and a clear way to report a security concern.', 'review' => false,
        'sections' => [
            ['Website safeguards', 'The public account scaffold hashes passwords, uses prepared database queries, validates CSRF tokens on browser forms and regenerates the session on login. Session cookies use HttpOnly and SameSite=Lax, with Secure enabled over HTTPS. Google login validates OAuth state. PayPal webhooks are signature-checked before subscription updates.'],
            ['Shared responsibility', 'Use strong, unique credentials and multi-factor authentication on the social and developer accounts you connect. Grant only necessary API permissions, remove unused connections and rotate a credential if you believe it was exposed. Never send an API secret in a contact message or place it in a public URL.'],
            ['Report a vulnerability privately', 'Contact support with the subject “Security report”. Include the affected URL, a safe reproduction, expected behavior and potential impact. Use a test account you control. Do not access other users’ data, disrupt the service, exfiltrate credentials or publish sensitive details before coordination.'],
            ['Scope and expectations', 'This page is not a bug-bounty offer, a security certification or an authorization to attack third-party networks. Token-storage controls and other safeguards in an existing connected application must be assessed separately from this website scaffold. No system is guaranteed to be completely secure.'],
        ],
    ],
];
