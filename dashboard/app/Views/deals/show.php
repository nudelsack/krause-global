<div class="page-header">
    <div>
        <h1><?php echo htmlspecialchars($deal['title']); ?></h1>
        <p class="text-muted">
            <code><?php echo htmlspecialchars($deal['deal_code']); ?></code> • 
            <?php echo htmlspecialchars($deal['deal_type']); ?>
            <?php if ($deal['deal_subtype']): ?>
                • <?php echo htmlspecialchars($deal['deal_subtype']); ?>
            <?php endif; ?>
        </p>
    </div>
    <div>
        <a href="/dashboard/deals/<?php echo $deal['id']; ?>/edit" class="btn btn-secondary">
            <i class="fas fa-edit"></i> Edit
        </a>
        <form method="POST" action="/dashboard/deals/<?php echo $deal['id']; ?>/export" style="display: inline;">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-download"></i> Export
            </button>
        </form>
    </div>
</div>

<!-- Deal Tabs -->
<div class="tabs">
    <a href="/dashboard/deals/<?php echo $deal['id']; ?>" class="tab-item active">Overview</a>
    <a href="/dashboard/deals/<?php echo $deal['id']; ?>/documents" class="tab-item">Documents (<?php echo $document_count; ?>)</a>
    <a href="/dashboard/deals/<?php echo $deal['id']; ?>/workflow" class="tab-item">Workflow</a>
</div>

<!-- Deal Info -->
<div class="section">
    <h2>Deal Information</h2>
    <div class="info-grid">
        <div class="info-item">
            <label>Status</label>
            <span class="badge badge-info"><?php echo htmlspecialchars($deal['status']); ?></span>
        </div>
        <div class="info-item">
            <label>Quantity</label>
            <span><?php echo htmlspecialchars($deal['quantity'] ?? '-'); ?> <?php echo htmlspecialchars($deal['quantity_unit'] ?? ''); ?></span>
        </div>
        <div class="info-item">
            <label>Price</label>
            <span><?php echo htmlspecialchars($deal['price'] ?? '-'); ?> <?php echo htmlspecialchars($deal['currency']); ?></span>
        </div>
        <div class="info-item">
            <label>Incoterms</label>
            <span><?php echo htmlspecialchars($deal['incoterms'] ?? '-'); ?></span>
        </div>
        <div class="info-item">
            <label>Origin</label>
            <span><?php echo htmlspecialchars($deal['origin'] ?? '-'); ?></span>
        </div>
        <div class="info-item">
            <label>Destination</label>
            <span><?php echo htmlspecialchars($deal['destination'] ?? '-'); ?></span>
        </div>
    </div>
    
    <?php if ($deal['notes']): ?>
        <div style="margin-top: 20px;">
            <label>Notes</label>
            <p><?php echo nl2br(htmlspecialchars($deal['notes'])); ?></p>
        </div>
    <?php endif; ?>
</div>

<!-- Parties -->
<?php if (!empty($parties)): ?>
<div class="section">
    <h2>Parties</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th>Role</th>
                <th>Company</th>
                <th>Country</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($parties as $party): ?>
                <tr>
                    <td><span class="badge badge-secondary"><?php echo htmlspecialchars($party['role_in_deal']); ?></span></td>
                    <td><a href="/dashboard/parties/<?php echo $party['id']; ?>"><?php echo htmlspecialchars($party['company_name']); ?></a></td>
                    <td><?php echo htmlspecialchars($party['country'] ?? '-'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<!-- Recent Activity -->
<?php if (!empty($recent_activity)): ?>
<div class="section">
    <h2>Recent Activity</h2>
    <div class="activity-log">
        <?php foreach ($recent_activity as $log): ?>
            <div class="activity-item">
                <i class="fas fa-circle"></i>
                <div>
                    <strong><?php echo htmlspecialchars($log['username'] ?? 'System'); ?></strong>
                    <?php echo htmlspecialchars($log['action']); ?>
                    <span class="text-muted"><?php echo date('d M Y H:i', strtotime($log['created_at'])); ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<style>
.info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 20px; }
.info-item label { display: block; font-weight: 600; margin-bottom: 4px; color: var(--text-light); font-size: 0.9rem; }
.info-item span { font-size: 1.1rem; }
.tabs { display: flex; gap: 0; background: var(--bg-white); border-radius: 8px 8px 0 0; overflow: hidden; margin-top: 20px; }
.tab-item { padding: 15px 24px; border-bottom: 3px solid transparent; text-decoration: none; color: var(--text-light); transition: all 0.3s; }
.tab-item.active { border-bottom-color: var(--accent); color: var(--text-dark); font-weight: 600; }
.tab-item:hover { background: var(--bg-light); }
.activity-log { display: flex; flex-direction: column; gap: 12px; }
.activity-item { display: flex; align-items: start; gap: 12px; padding: 8px 0; }
.activity-item i { color: var(--accent); font-size: 0.5rem; margin-top: 8px; }
</style>
