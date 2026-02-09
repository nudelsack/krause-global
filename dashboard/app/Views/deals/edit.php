<div class="page-header">
    <h1>Edit Deal</h1>
    <a href="/dashboard/deals/<?php echo $deal['id']; ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="section">
    <form method="POST" action="/dashboard/deals/<?php echo $deal['id']; ?>/update" class="deal-form">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        
        <div class="form-group">
            <label for="title">Deal Title *</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($deal['title']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="status">Status *</label>
            <select id="status" name="status" required>
                <?php 
                $statuses = $deal_types[$deal['deal_type']]['statuses'] ?? [];
                foreach ($statuses as $key => $label): 
                ?>
                    <option value="<?php echo $key; ?>" <?php echo $deal['status'] === $key ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($label); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="quantity">Quantity</label>
                <input type="number" step="0.01" id="quantity" name="quantity" value="<?php echo htmlspecialchars($deal['quantity'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="quantity_unit">Unit</label>
                <input type="text" id="quantity_unit" name="quantity_unit" value="<?php echo htmlspecialchars($deal['quantity_unit'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" step="0.01" id="price" name="price" value="<?php echo htmlspecialchars($deal['price'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="currency">Currency</label>
                <select id="currency" name="currency">
                    <option value="USD" <?php echo $deal['currency'] === 'USD' ? 'selected' : ''; ?>>USD</option>
                    <option value="EUR" <?php echo $deal['currency'] === 'EUR' ? 'selected' : ''; ?>>EUR</option>
                    <option value="GBP" <?php echo $deal['currency'] === 'GBP' ? 'selected' : ''; ?>>GBP</option>
                </select>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="incoterms">Incoterms</label>
                <input type="text" id="incoterms" name="incoterms" value="<?php echo htmlspecialchars($deal['incoterms'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="origin">Origin</label>
                <input type="text" id="origin" name="origin" value="<?php echo htmlspecialchars($deal['origin'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="destination">Destination</label>
                <input type="text" id="destination" name="destination" value="<?php echo htmlspecialchars($deal['destination'] ?? ''); ?>">
            </div>
        </div>
        
        <div class="form-group">
            <label for="reference_no">Reference Number</label>
            <input type="text" id="reference_no" name="reference_no" value="<?php echo htmlspecialchars($deal['reference_no'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label for="notes">Notes</label>
            <textarea id="notes" name="notes" rows="4"><?php echo htmlspecialchars($deal['notes'] ?? ''); ?></textarea>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-large">
                <i class="fas fa-save"></i> Save Changes
            </button>
            
            <button type="button" class="btn btn-danger" data-confirm="Archive this deal?" onclick="archiveDeal()">
                <i class="fas fa-archive"></i> Archive Deal
            </button>
        </div>
    </form>
    
    <form id="archiveForm" method="POST" action="/dashboard/deals/<?php echo $deal['id']; ?>/archive" style="display: none;">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
    </form>
</div>

<script>
function archiveDeal() {
    if (confirm('Are you sure you want to archive this deal?')) {
        document.getElementById('archiveForm').submit();
    }
}
</script>

<style>
.deal-form { max-width: 900px; }
.form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
.form-group { margin-bottom: 20px; }
.form-group label { display: block; margin-bottom: 8px; font-weight: 600; }
.form-group input, .form-group select, .form-group textarea {
    width: 100%;
    padding: 10px 14px;
    border: 2px solid var(--border-color);
    border-radius: 6px;
    font-size: 1rem;
}
.form-actions { display: flex; gap: 15px; margin-top: 30px; }
.btn-large { padding: 14px 32px; font-size: 1.1rem; }
.btn-danger { background: var(--error); color: white; }
.btn-danger:hover { background: #DC2626; }
</style>
