<div class="page-header">
    <h1>📊 Dashboard</h1>
    <p class="welcome-text">Willkommen zurück, <?php echo htmlspecialchars($user['username']); ?>!</p>
</div>

<!-- Quick Actions -->
<div class="quick-actions">
    <a href="/dashboard/loi/incoming" class="action-card incoming">
        <div class="action-icon">
            <i class="fas fa-arrow-down"></i>
        </div>
        <div class="action-content">
            <h3>LOI Eingang</h3>
            <p>Absichtserklärungen von Käufern</p>
        </div>
    </a>
    
    <a href="/dashboard/loi/outgoing" class="action-card outgoing">
        <div class="action-icon">
            <i class="fas fa-arrow-up"></i>
        </div>
        <div class="action-content">
            <h3>LOI Ausgang</h3>
            <p>Absichtserklärungen an Lieferanten</p>
        </div>
    </a>
    
    <a href="/dashboard/offers/received" class="action-card" style="border-color: #10B981; background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.05) 100%);">
        <div class="action-icon" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%);">
            <i class="fas fa-file-invoice-dollar"></i>
        </div>
        <div class="action-content">
            <h3>Angebote erhalten</h3>
            <p>Konkrete Angebote von Lieferanten</p>
        </div>
    </a>
    
    <a href="/dashboard/offers/sent" class="action-card" style="border-color: #8B5CF6; background: linear-gradient(135deg, rgba(139, 92, 246, 0.1) 0%, rgba(124, 58, 237, 0.05) 100%);">
        <div class="action-icon" style="background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%);">
            <i class="fas fa-paper-plane"></i>
        </div>
        <div class="action-content">
            <h3>Angebote gesendet</h3>
            <p>Konkrete Angebote an Käufer</p>
        </div>
    </a>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-briefcase"></i>
        </div>
        <div class="stat-content">
            <h3><?php echo $stats['total_deals']; ?></h3>
            <p>Gesamt Deals</p>
        </div>
    </div>
    
    <?php foreach ($stats['by_type'] as $type => $count): ?>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $count; ?></h3>
                <p><?php echo ucfirst(str_replace('_', ' ', $type)); ?></p>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Expiring Deals Alert -->
<?php if (!empty($stats['expiring']['expired']) || !empty($stats['expiring']['today']) || !empty($stats['expiring']['this_week'])): ?>
    <div class="alert-section" style="background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%); border-left: 4px solid #F59E0B; border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem;">
        <h3 style="color: #92400E; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
            ⚠️ Ablaufende Deals
        </h3>
        
        <?php if (!empty($stats['expiring']['expired'])): ?>
            <div style="background: #FEE2E2; border-left: 3px solid #EF4444; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                <strong style="color: #991B1B;">🔴 Abgelaufen (<?php echo count($stats['expiring']['expired']); ?>)</strong>
                <ul style="margin: 0.5rem 0 0 1.5rem; color: #7F1D1D;">
                    <?php foreach ($stats['expiring']['expired'] as $deal): ?>
                        <li>
                            <a href="/dashboard/deals/<?php echo $deal['id']; ?>" style="color: #991B1B; text-decoration: underline;">
                                <?php echo htmlspecialchars($deal['title']); ?>
                            </a>
                            - Abgelaufen am <?php echo date('d.m.Y', strtotime($deal['expiry_date'])); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($stats['expiring']['today'])): ?>
            <div style="background: #FEE2E2; border-left: 3px solid #F59E0B; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                <strong style="color: #92400E;">🟠 Läuft heute ab (<?php echo count($stats['expiring']['today']); ?>)</strong>
                <ul style="margin: 0.5rem 0 0 1.5rem; color: #78350F;">
                    <?php foreach ($stats['expiring']['today'] as $deal): ?>
                        <li>
                            <a href="/dashboard/deals/<?php echo $deal['id']; ?>" style="color: #92400E; text-decoration: underline;">
                                <?php echo htmlspecialchars($deal['title']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($stats['expiring']['this_week'])): ?>
            <div style="background: #FFFBEB; border-left: 3px solid #FBBF24; padding: 1rem; border-radius: 8px;">
                <strong style="color: #92400E;">🟡 Läuft diese Woche ab (<?php echo count($stats['expiring']['this_week']); ?>)</strong>
                <ul style="margin: 0.5rem 0 0 1.5rem; color: #78350F;">
                    <?php foreach ($stats['expiring']['this_week'] as $deal): ?>
                        <li>
                            <a href="/dashboard/deals/<?php echo $deal['id']; ?>" style="color: #92400E; text-decoration: underline;">
                                <?php echo htmlspecialchars($deal['title']); ?>
                            </a>
                            - Läuft ab am <?php echo date('d.m.Y', strtotime($deal['expiry_date'])); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<div class="section">
    <div class="section-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h2>🔥 Aktuelle Deals</h2>
        <a href="/dashboard/deals/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Neuer Deal
        </a>
    </div>
    
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Deal Code</th>
                    <th>Titel</th>
                    <th>Typ</th>
                    <th>Status</th>
                    <th>Aktualisiert</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stats['recent_deals'] as $deal): ?>
                    <tr>
                        <td><code><?php echo htmlspecialchars($deal['deal_code']); ?></code></td>
                        <td><?php echo htmlspecialchars($deal['title']); ?></td>
                        <td><?php echo htmlspecialchars($deal['deal_type']); ?></td>
                        <td><span class="badge badge-info"><?php echo htmlspecialchars($deal['status']); ?></span></td>
                        <td><?php echo date('d M Y', strtotime($deal['updated_at'])); ?></td>
                        <td>
                            <a href="/dashboard/deals/<?php echo $deal['id']; ?>" class="btn btn-sm btn-secondary">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
