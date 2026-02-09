<div class="page-header">
    <div>
        <h1><?php echo htmlspecialchars($deal['title']); ?></h1>
        <p class="text-muted"><code><?php echo htmlspecialchars($deal['deal_code']); ?></code></p>
    </div>
    <a href="/dashboard/deals/<?php echo $deal['id']; ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Deal
    </a>
</div>

<div class="tabs">
    <a href="/dashboard/deals/<?php echo $deal['id']; ?>" class="tab-item">Overview</a>
    <a href="/dashboard/deals/<?php echo $deal['id']; ?>/documents" class="tab-item">Documents</a>
    <a href="/dashboard/deals/<?php echo $deal['id']; ?>/workflow" class="tab-item active">Workflow</a>
</div>

<div class="section">
    <h2><i class="fas fa-tasks"></i> Required Documents</h2>
    <p class="text-muted">Checklist of required documents for this deal type</p>
    
    <div class="workflow-checklist">
        <?php foreach ($required_docs as $key => $label): ?>
            <div class="workflow-item">
                <div class="workflow-checkbox">
                    <i class="fas fa-square"></i>
                </div>
                <div class="workflow-info">
                    <h3><?php echo htmlspecialchars($label); ?></h3>
                    <p class="text-muted"><?php echo htmlspecialchars($key); ?></p>
                </div>
                <div class="workflow-status">
                    <span class="badge badge-warning">Pending</span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div style="margin-top: 30px; padding: 20px; background: var(--bg-light); border-radius: 8px;">
        <p><i class="fas fa-info-circle"></i> <strong>Note:</strong> This is a simplified workflow view. Full workflow management with step tracking will be implemented in the next phase.</p>
        <p class="text-muted" style="margin-top: 10px;">
            To complete this deal, upload the required documents via the 
            <a href="/dashboard/deals/<?php echo $deal['id']; ?>/documents">Documents tab</a>.
        </p>
    </div>
</div>

<style>
.workflow-checklist {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-top: 20px;
}
.workflow-item {
    display: flex;
    align-items: center;
    gap: 20px;
    background: var(--bg-white);
    padding: 20px;
    border: 2px solid var(--border-color);
    border-radius: 8px;
    transition: all 0.3s;
}
.workflow-item:hover {
    border-color: var(--accent);
}
.workflow-checkbox {
    font-size: 1.5rem;
    color: var(--text-light);
}
.workflow-info {
    flex: 1;
}
.workflow-info h3 {
    margin-bottom: 4px;
    font-size: 1.1rem;
}
.workflow-status {
    text-align: right;
}
.tabs {
    display: flex;
    gap: 0;
    background: var(--bg-white);
    border-radius: 8px 8px 0 0;
    overflow: hidden;
    margin-top: 20px;
}
.tab-item {
    padding: 15px 24px;
    border-bottom: 3px solid transparent;
    text-decoration: none;
    color: var(--text-light);
    transition: all 0.3s;
}
.tab-item.active {
    border-bottom-color: var(--accent);
    color: var(--text-dark);
    font-weight: 600;
}
</style>
