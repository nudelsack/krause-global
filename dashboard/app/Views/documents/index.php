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
    <a href="/dashboard/deals/<?php echo $deal['id']; ?>/documents" class="tab-item active">Documents</a>
    <a href="/dashboard/deals/<?php echo $deal['id']; ?>/workflow" class="tab-item">Workflow</a>
</div>

<!-- Upload Form -->
<div class="section">
    <h2><i class="fas fa-upload"></i> Upload Document</h2>
    <form method="POST" action="/dashboard/deals/<?php echo $deal['id']; ?>/documents/upload" enctype="multipart/form-data" class="upload-form">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        
        <div class="form-row">
            <div class="form-group">
                <label for="category">Category *</label>
                <select id="category" name="category" required>
                    <?php foreach ($categories as $key => $label): ?>
                        <option value="<?php echo $key; ?>"><?php echo htmlspecialchars($label); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="title">Document Title *</label>
                <input type="text" id="title" name="title" required>
            </div>
            
            <div class="form-group">
                <label for="source_type">Source Type</label>
                <select id="source_type" name="source_type">
                    <option value="incoming">Incoming</option>
                    <option value="outgoing">Outgoing</option>
                    <option value="internal">Internal</option>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label for="file">File (PDF, PNG, JPG) *</label>
            <input type="file" id="file" name="file" accept=".pdf,.png,.jpg,.jpeg" required>
            <small class="text-muted">Max 50MB</small>
        </div>
        
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-cloud-upload-alt"></i> Upload
        </button>
    </form>
</div>

<!-- Documents List -->
<div class="section">
    <h2><i class="fas fa-file-alt"></i> Documents</h2>
    
    <?php if (empty($documents)): ?>
        <p style="text-align: center; padding: 40px; color: var(--text-light);">
            No documents uploaded yet.
        </p>
    <?php else: ?>
        <div class="documents-grid">
            <?php foreach ($documents as $doc): ?>
                <div class="document-card">
                    <div class="document-icon">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <div class="document-info">
                        <h3><?php echo htmlspecialchars($doc['title']); ?></h3>
                        <p class="text-muted">
                            <span class="badge badge-secondary"><?php echo htmlspecialchars($doc['category']); ?></span>
                            <?php echo $doc['version_count']; ?> version(s)
                        </p>
                        <p class="text-muted" style="font-size: 0.85rem;">
                            Last uploaded: <?php echo date('d M Y H:i', strtotime($doc['last_uploaded'])); ?>
                        </p>
                    </div>
                    <div class="document-actions">
                        <a href="/dashboard/documents/<?php echo $doc['id']; ?>/preview" class="btn btn-sm btn-secondary">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="/dashboard/documents/<?php echo $doc['id']; ?>/download" class="btn btn-sm btn-primary">
                            <i class="fas fa-download"></i> Download
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.upload-form { background: var(--bg-light); padding: 20px; border-radius: 8px; }
.documents-grid { display: grid; gap: 20px; margin-top: 20px; }
.document-card {
    background: var(--bg-white);
    border: 2px solid var(--border-color);
    border-radius: 8px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 20px;
    transition: all 0.3s;
}
.document-card:hover { border-color: var(--accent); transform: translateY(-2px); }
.document-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, var(--accent) 0%, var(--secondary) 100%);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    color: white;
}
.document-info { flex: 1; }
.document-info h3 { margin-bottom: 8px; font-size: 1.1rem; }
.document-actions { display: flex; gap: 10px; }
.tabs { display: flex; gap: 0; background: var(--bg-white); border-radius: 8px 8px 0 0; overflow: hidden; margin-top: 20px; }
.tab-item { padding: 15px 24px; border-bottom: 3px solid transparent; text-decoration: none; color: var(--text-light); transition: all 0.3s; }
.tab-item.active { border-bottom-color: var(--accent); color: var(--text-dark); font-weight: 600; }
</style>
