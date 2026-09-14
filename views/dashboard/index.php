<?php
/** @var array $user */
$seo = ['title' => 'Dashboard — ' . config('brand.name')];
require __DIR__ . '/../partials/head.php';
?>
<main class="app-shell" data-session-watch>
    <div class="container">
        <div class="app-welcome">
            <div>
                <h1>Welcome back, <?= e(explode(' ', $user['name'])[0]) ?></h1>
                <p>This is the authenticated area — the existing LinkEasy Social application mounts here.</p>
            </div>
            <a class="btn btn-outline" href="<?= e(url('home')) ?>"><svg class="icon"><use href="#i-home"/></svg> View website</a>
        </div>

        <div class="app-grid">
            <div class="app-card">
                <h3>Current plan</h3>
                <p class="app-plan-badge">
                    <span class="plan-pill <?= ($subscription['plan'] ?? 'free') === 'managed' ? 'plan-pill--managed' : '' ?>">
                        <?= e(ucfirst($subscription['plan'] ?? 'free')) ?>
                    </span>
                </p>
                <p style="font-size:.88rem;color:var(--muted);margin-top:10px">
                    Status: <strong style="color:var(--green)"><?= e(ucfirst($subscription['status'] ?? 'active')) ?></strong>
                    <?php if (!empty($subscription['current_period_end'])): ?>
                        <br>Next billing date: <?= e(date('M j, Y', strtotime($subscription['current_period_end']))) ?>
                    <?php endif; ?>
                </p>
                <?php if (($subscription['plan'] ?? 'free') !== 'managed'): ?>
                <a class="btn btn-primary btn-sm" style="margin-top:14px" href="<?= e(url('pricing')) ?>">
                    <svg class="icon"><use href="#i-zap"/></svg> Upgrade plan</a>
                <?php endif; ?>
            </div>
            <div class="app-card">
                <h3>Plan limits</h3>
                <?php $limits = \App\PlanGate::limits((string) ($subscription['plan'] ?? 'free'));
                foreach ([
                    'max_projects' => 'Project workspaces',
                    'max_social_accounts' => 'Social accounts',
                    'max_scheduled_posts' => 'Scheduled posts',
                    'max_team_members' => 'Team members',
                ] as $key => $label): ?>
                <p style="display:flex;justify-content:space-between;font-size:.9rem;padding:7px 0;border-bottom:1px solid var(--line)">
                    <span style="color:var(--muted)"><?= e($label) ?></span>
                    <strong style="color:var(--ink)"><?= ($limits[$key] ?? null) === null ? 'Unlimited' : e((string) $limits[$key]) ?></strong>
                </p>
                <?php endforeach; ?>
            </div>
            <div class="app-card">
                <h3>Account</h3>
                <p style="font-size:.9rem;color:var(--ink-2);margin-bottom:6px"><strong><?= e($user['email']) ?></strong></p>
                <p style="font-size:.84rem;color:var(--muted);margin-bottom:16px">Member since <?= e(date('M Y', strtotime($user['created_at']))) ?></p>
                <form method="post" action="<?= e(url('logout')) ?>">
                    <?= csrf_field() ?>
                    <button class="btn btn-outline btn-sm" type="submit"><svg class="icon"><use href="#i-log-out"/></svg> Log out</button>
                </form>
            </div>
        </div>

        <div class="app-note">
            <strong style="color:var(--ink)">Integration point:</strong>
            your existing application (posting, scheduling, analytics, settings…) renders inside
            <code>/dashboard</code>. Replace <code>views/dashboard/index.php</code> with the real app while
            keeping the shared bootstrap, config, sessions, authorization (<code>Auth::requireLogin()</code>)
            and plan gates (<code>PlanGate::allows()</code>) from this scaffold.
        </div>
    </div>
</main>
<?php require __DIR__ . '/../partials/footer.php'; ?>
