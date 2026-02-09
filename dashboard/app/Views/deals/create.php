<div class="page-header">
    <h1>Create New Deal</h1>
    <a href="/dashboard/deals" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="section">
    <form method="POST" action="/dashboard/deals/store" class="deal-form">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        
        <div class="form-group">
            <label for="title">Deal Title *</label>
            <input type="text" id="title" name="title" required>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="deal_type">Deal Type *</label>
                <select id="deal_type" name="deal_type" required>
                    <option value="">Select Type</option>
                    <?php foreach ($deal_types as $key => $type): ?>
                        <option value="<?php echo $key; ?>"><?php echo htmlspecialchars($type['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="deal_subtype">Subtype</label>
                <input type="text" id="deal_subtype" name="deal_subtype">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="quantity">Quantity</label>
                <input type="number" step="0.01" id="quantity" name="quantity">
            </div>
            
            <div class="form-group">
                <label for="quantity_unit">Unit</label>
                <input type="text" id="quantity_unit" name="quantity_unit" placeholder="MT, BBL, etc">
            </div>
            
            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" step="0.01" id="price" name="price">
            </div>
            
            <div class="form-group">
                <label for="currency">Currency</label>
                <select id="currency" name="currency">
                    <option value="USD">USD</option>
                    <option value="EUR">EUR</option>
                    <option value="GBP">GBP</option>
                </select>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="incoterms">Incoterms</label>
                <select id="incoterms" name="incoterms">
                    <option value="">Select</option>
                    <option value="FOB">FOB</option>
                    <option value="CIF">CIF</option>
                    <option value="CFR">CFR</option>
                    <option value="EXW">EXW</option>
                    <option value="FCA">FCA</option>
                    <option value="DAP">DAP</option>
                    <option value="DDP">DDP</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="origin">Origin</label>
                <input type="text" id="origin" name="origin">
            </div>
            
            <div class="form-group">
                <label for="destination">Destination</label>
                <input type="text" id="destination" name="destination">
            </div>
        </div>
        
        <div class="form-group">
            <label for="reference_no">Reference Number</label>
            <input type="text" id="reference_no" name="reference_no">
        </div>
        
        <div class="form-group">
            <label for="expiry_date">⏰ Ablaufdatum</label>
            <input type="date" id="expiry_date" name="expiry_date" class="form-control">
            <small style="color: var(--text-muted); display: block; margin-top: 0.25rem;">
                Optional: Datum bis wann LOI/Angebot gültig ist
            </small>
        </div>
        
        <div class="form-group">
            <label for="notes">Notes</label>
            <textarea id="notes" name="notes" rows="4"></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary btn-large">
            <i class="fas fa-save"></i> Create Deal
        </button>
    </form>
</div>

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
.form-group input:focus, .form-group select:focus, .form-group textarea:focus {
    outline: none;
    border-color: var(--accent);
}
.btn-large { padding: 14px 32px; font-size: 1.1rem; }
</style>
