<div class="page-header">
    <h1><?php echo htmlspecialchars($document['title']); ?></h1>
    <div>
        <a href="/dashboard/documents/<?php echo $document['id']; ?>/download" class="btn btn-primary">
            <i class="fas fa-download"></i> Download
        </a>
        <button onclick="history.back()" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </button>
    </div>
</div>

<div class="section">
    <h2>Document Details</h2>
    <div class="info-grid">
        <div class="info-item">
            <label>Category</label>
            <span class="badge badge-secondary"><?php echo htmlspecialchars($document['category']); ?></span>
        </div>
        <div class="info-item">
            <label>Source Type</label>
            <span><?php echo htmlspecialchars($document['source_type']); ?></span>
        </div>
        <div class="info-item">
            <label>File Type</label>
            <span><?php echo htmlspecialchars($version['mime_type']); ?></span>
        </div>
        <div class="info-item">
            <label>File Size</label>
            <span><?php echo number_format($version['file_size'] / 1024 / 1024, 2); ?> MB</span>
        </div>
        <div class="info-item">
            <label>Version</label>
            <span><?php echo $version['version_no']; ?></span>
        </div>
        <div class="info-item">
            <label>Uploaded</label>
            <span><?php echo date('d M Y H:i', strtotime($version['uploaded_at'])); ?></span>
        </div>
    </div>
</div>

<?php if ($extracted_text && $extracted_text['extracted_text']): ?>
<div class="section">
    <h2><i class="fas fa-file-alt"></i> Extracted Text</h2>
    <p class="text-muted">
        Extraction method: <?php echo htmlspecialchars($extracted_text['extraction_method']); ?> 
        • <?php echo date('d M Y H:i', strtotime($extracted_text['extracted_at'])); ?>
    </p>
    <div class="extracted-text">
        <?php echo nl2br(htmlspecialchars($extracted_text['extracted_text'])); ?>
    </div>
</div>
<?php elseif ($version['mime_type'] === 'application/pdf'): ?>
<div class="section">
    <p class="text-muted">
        <i class="fas fa-info-circle"></i> No extracted text available. The document may be scanned or image-based.
    </p>
</div>
<?php endif; ?>

<style>
.info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 20px; }
.info-item label { display: block; font-weight: 600; margin-bottom: 4px; color: var(--text-light); font-size: 0.9rem; }
.info-item span { font-size: 1.1rem; }
.extracted-text {
    background: var(--bg-light);
    padding: 20px;
    border-radius: 8px;
    margin-top: 20px;
    max-height: 600px;
    overflow-y: auto;
    font-family: 'Courier New', monospace;
    font-size: 0.9rem;
    line-height: 1.6;
}
</style>
