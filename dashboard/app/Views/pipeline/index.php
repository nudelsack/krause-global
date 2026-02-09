<div class="page-header">
    <h1>📊 Deal Pipeline</h1>
    <p class="welcome-text">Visueller Überblick über alle Deals</p>
</div>

<div class="pipeline-container">
    <?php foreach ($stages as $stageKey => $stage): ?>
        <div class="pipeline-column" data-stage="<?php echo $stageKey; ?>">
            <div class="pipeline-header" style="border-color: <?php echo $stage['color']; ?>;">
                <div class="pipeline-header-content">
                    <span class="pipeline-icon"><?php echo $stage['icon']; ?></span>
                    <h3><?php echo htmlspecialchars($stage['title']); ?></h3>
                </div>
                <span class="pipeline-count"><?php echo count($stage['deals']); ?></span>
            </div>
            
            <div class="pipeline-cards">
                <?php if (empty($stage['deals'])): ?>
                    <div class="pipeline-empty">
                        <p style="color: #94A3B8; font-size: 14px; text-align: center; padding: 20px;">
                            Keine Deals in dieser Phase
                        </p>
                    </div>
                <?php else: ?>
                    <?php foreach ($stage['deals'] as $deal): ?>
                        <div class="pipeline-card" data-deal-id="<?php echo $deal['id']; ?>" draggable="true">
                            <!-- Expiry Warning Badge -->
                            <?php if (!empty($deal['expiry_date'])): 
                                $today = date('Y-m-d');
                                $daysUntilExpiry = (strtotime($deal['expiry_date']) - strtotime($today)) / (60 * 60 * 24);
                                
                                if ($daysUntilExpiry < 0): ?>
                                    <div style="background: #FEE2E2; border-left: 3px solid #EF4444; padding: 0.5rem; margin-bottom: 0.75rem; border-radius: 6px;">
                                        <span style="color: #991B1B; font-size: 12px; font-weight: 600;">
                                            🔴 ABGELAUFEN - <?php echo date('d.m.Y', strtotime($deal['expiry_date'])); ?>
                                        </span>
                                    </div>
                                <?php elseif ($daysUntilExpiry == 0): ?>
                                    <div style="background: #FFEDD5; border-left: 3px solid #F59E0B; padding: 0.5rem; margin-bottom: 0.75rem; border-radius: 6px;">
                                        <span style="color: #92400E; font-size: 12px; font-weight: 600;">
                                            🟠 LÄUFT HEUTE AB
                                        </span>
                                    </div>
                                <?php elseif ($daysUntilExpiry <= 7): ?>
                                    <div style="background: #FFFBEB; border-left: 3px solid #FBBF24; padding: 0.5rem; margin-bottom: 0.75rem; border-radius: 6px;">
                                        <span style="color: #78350F; font-size: 12px; font-weight: 600;">
                                            🟡 Läuft in <?php echo round($daysUntilExpiry); ?> Tag(en) ab
                                        </span>
                                    </div>
                                <?php endif;
                            endif; ?>
                            
                            <div class="pipeline-card-header">
                                <strong style="color: var(--text-primary);">
                                    <?php echo htmlspecialchars($deal['title']); ?>
                                </strong>
                                <code style="font-size: 11px; color: var(--text-muted);">
                                    <?php echo htmlspecialchars($deal['deal_code']); ?>
                                </code>
                            </div>
                            
                            <div class="pipeline-card-body">
                                <div style="font-size: 13px; color: var(--text-secondary); margin-bottom: 8px;">
                                    <?php echo htmlspecialchars($deal['deal_type']); ?>
                                </div>
                                
                                <?php if ($deal['quantity'] && $deal['quantity_unit']): ?>
                                    <div style="font-size: 13px; color: var(--text-secondary);">
                                        📦 <?php echo number_format($deal['quantity'], 0); ?> <?php echo htmlspecialchars($deal['quantity_unit']); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($deal['price']): ?>
                                    <div style="font-size: 14px; font-weight: 600; color: #10B981; margin-top: 8px;">
                                        💰 <?php echo number_format($deal['price'], 2); ?> <?php echo htmlspecialchars($deal['currency'] ?? 'USD'); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($deal['commission_value']): ?>
                                    <div style="font-size: 12px; color: #8B5CF6; margin-top: 4px;">
                                        💵 Provision: <?php echo number_format($deal['commission_value'], 2); ?> <?php echo htmlspecialchars($deal['commission_unit'] ?? '%'); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="pipeline-card-footer">
                                <span style="font-size: 11px; color: var(--text-muted);">
                                    <?php echo date('d.m.Y', strtotime($deal['updated_at'])); ?>
                                </span>
                                <a href="/dashboard/deals/<?php echo $deal['id']; ?>" class="btn btn-sm btn-secondary" style="font-size: 11px; padding: 4px 8px;">
                                    Details →
                                </a>
                            </div>
                            
                            <!-- Quick Status Change -->
                            <div class="pipeline-card-actions">
                                <select class="status-select" onchange="updateDealStatus(<?php echo $deal['id']; ?>, this.value)">
                                    <?php foreach ($stages as $sKey => $s): ?>
                                        <option value="<?php echo $sKey; ?>" <?php echo $sKey === $stageKey ? 'selected' : ''; ?>>
                                            <?php echo $s['icon']; ?> <?php echo $s['title']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
function updateDealStatus(dealId, newStatus) {
    fetch('/dashboard/pipeline/update-status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'deal_id=' + dealId + '&status=' + newStatus
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Fehler beim Aktualisieren des Status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Fehler beim Aktualisieren des Status');
    });
}

// Drag and Drop functionality
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.pipeline-card');
    const columns = document.querySelectorAll('.pipeline-column');
    
    cards.forEach(card => {
        card.addEventListener('dragstart', function(e) {
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/html', this.innerHTML);
            this.classList.add('dragging');
        });
        
        card.addEventListener('dragend', function(e) {
            this.classList.remove('dragging');
        });
    });
    
    columns.forEach(column => {
        column.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            this.classList.add('drag-over');
        });
        
        column.addEventListener('dragleave', function(e) {
            this.classList.remove('drag-over');
        });
        
        column.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
            
            const card = document.querySelector('.dragging');
            if (card) {
                const dealId = card.getAttribute('data-deal-id');
                const newStatus = this.getAttribute('data-stage');
                
                // Update on server
                updateDealStatus(dealId, newStatus);
            }
        });
    });
});
</script>
