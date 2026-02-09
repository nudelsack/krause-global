<div class="page-header">
    <h1><i class="fas fa-search"></i> Search Documents</h1>
</div>

<div class="section">
    <form method="GET" action="/dashboard/search" class="search-form-full">
        <div class="search-input-group">
            <input type="text" name="q" placeholder="Search in documents..." value="<?php echo htmlspecialchars($query); ?>" autofocus>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Search
            </button>
        </div>
        
        <div class="search-filters">
            <select name="deal_type">
                <option value="">All Deal Types</option>
                <?php foreach ($deal_types as $key => $type): ?>
                    <option value="<?php echo $key; ?>" <?php echo $deal_type === $key ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($type['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <select name="category">
                <option value="">All Categories</option>
                <option value="loi_in" <?php echo $category === 'loi_in' ? 'selected' : ''; ?>>LOI Incoming</option>
                <option value="loi_out" <?php echo $category === 'loi_out' ? 'selected' : ''; ?>>LOI Outgoing</option>
                <option value="icpo" <?php echo $category === 'icpo' ? 'selected' : ''; ?>>ICPO</option>
                <option value="sco" <?php echo $category === 'sco' ? 'selected' : ''; ?>>SCO</option>
                <option value="invoice" <?php echo $category === 'invoice' ? 'selected' : ''; ?>>Invoice</option>
            </select>
        </div>
    </form>
</div>

<?php if ($query): ?>
<div class="section">
    <h2>Search Results (<?php echo count($results); ?>)</h2>
    
    <?php if (empty($results)): ?>
        <div style="text-align: center; padding: 60px;">
            <i class="fas fa-search" style="font-size: 3rem; color: var(--text-light); margin-bottom: 20px;"></i>
            <p class="text-muted">No results found for "<?php echo htmlspecialchars($query); ?>"</p>
        </div>
    <?php else: ?>
        <div class="search-results">
            <?php foreach ($results as $result): ?>
                <div class="search-result-item">
                    <div class="result-header">
                        <h3>
                            <i class="fas fa-file-alt"></i>
                            <?php echo htmlspecialchars($result['title']); ?>
                        </h3>
                        <span class="badge badge-secondary"><?php echo htmlspecialchars($result['category']); ?></span>
                    </div>
                    <p class="text-muted">
                        Deal: <a href="/dashboard/deals/<?php echo $result['deal_id']; ?>">
                            <code><?php echo htmlspecialchars($result['deal_code']); ?></code> 
                            <?php echo htmlspecialchars($result['deal_title']); ?>
                        </a>
                    </p>
                    <div class="result-actions">
                        <a href="/dashboard/documents/<?php echo $result['id']; ?>/preview" class="btn btn-sm btn-secondary">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="/dashboard/documents/<?php echo $result['id']; ?>/download" class="btn btn-sm btn-primary">
                            <i class="fas fa-download"></i> Download
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<style>
.search-form-full { }
.search-input-group {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
}
.search-input-group input {
    flex: 1;
    padding: 14px 20px;
    border: 2px solid var(--border-color);
    border-radius: 8px;
    font-size: 1.1rem;
}
.search-input-group input:focus {
    outline: none;
    border-color: var(--accent);
}
.search-filters {
    display: flex;
    gap: 15px;
}
.search-filters select {
    flex: 1;
    padding: 10px 14px;
    border: 2px solid var(--border-color);
    border-radius: 6px;
}
.search-results {
    display: flex;
    flex-direction: column;
    gap: 20px;
    margin-top: 20px;
}
.search-result-item {
    background: var(--bg-light);
    padding: 20px;
    border-radius: 8px;
    border-left: 4px solid var(--accent);
}
.result-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}
.result-header h3 {
    font-size: 1.2rem;
    margin: 0;
}
.result-actions {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}
</style>
