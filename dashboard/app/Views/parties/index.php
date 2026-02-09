<div class="page-header">
    <h1>Parties & Contacts</h1>
    <button onclick="showAddPartyModal()" class="btn btn-primary">
        <i class="fas fa-plus"></i> New Party
    </button>
</div>

<!-- Search -->
<div class="section">
    <form method="GET" action="/dashboard/parties" class="search-form">
        <input type="text" name="search" placeholder="Search parties..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn btn-secondary">
            <i class="fas fa-search"></i> Search
        </button>
    </form>
</div>

<!-- Parties List -->
<div class="section">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Company Name</th>
                    <th>Country</th>
                    <th>Website</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($parties)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px;">
                            No parties found.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($parties as $party): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($party['company_name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($party['country'] ?? '-'); ?></td>
                            <td>
                                <?php if ($party['website']): ?>
                                    <a href="<?php echo htmlspecialchars($party['website']); ?>" target="_blank">
                                        <i class="fas fa-external-link-alt"></i> Visit
                                    </a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('d M Y', strtotime($party['created_at'])); ?></td>
                            <td>
                                <a href="/dashboard/parties/<?php echo $party['id']; ?>" class="btn btn-sm btn-secondary">
                                    View
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Party Modal -->
<div id="addPartyModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Add New Party</h2>
            <button onclick="closeAddPartyModal()" class="modal-close">&times;</button>
        </div>
        <form method="POST" action="/dashboard/parties/store">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            
            <div class="form-group">
                <label for="company_name">Company Name *</label>
                <input type="text" id="company_name" name="company_name" required>
            </div>
            
            <div class="form-group">
                <label for="country">Country</label>
                <input type="text" id="country" name="country">
            </div>
            
            <div class="form-group">
                <label for="website">Website</label>
                <input type="url" id="website" name="website" placeholder="https://">
            </div>
            
            <div class="form-group">
                <label for="address_text">Address</label>
                <textarea id="address_text" name="address_text" rows="3"></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">
                <i class="fas fa-save"></i> Create Party
            </button>
        </form>
    </div>
</div>

<script>
function showAddPartyModal() {
    document.getElementById('addPartyModal').style.display = 'flex';
}
function closeAddPartyModal() {
    document.getElementById('addPartyModal').style.display = 'none';
}
</script>

<style>
.search-form { display: flex; gap: 15px; }
.search-form input { flex: 1; padding: 10px 14px; border: 2px solid var(--border-color); border-radius: 6px; }
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    align-items: center;
    justify-content: center;
    z-index: 1000;
}
.modal-content {
    background: white;
    border-radius: 12px;
    padding: 30px;
    max-width: 600px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
}
.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
.modal-close {
    background: none;
    border: none;
    font-size: 2rem;
    cursor: pointer;
    color: var(--text-light);
}
.form-group { margin-bottom: 20px; }
.form-group label { display: block; margin-bottom: 8px; font-weight: 600; }
.form-group input, .form-group textarea {
    width: 100%;
    padding: 10px 14px;
    border: 2px solid var(--border-color);
    border-radius: 6px;
}
</style>
