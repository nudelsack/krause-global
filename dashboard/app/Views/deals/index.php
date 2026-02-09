<div class="page-header">
    <h1>Deals</h1>
    <a href="/dashboard/deals/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> New Deal
    </a>
</div>

<!-- Filters -->
<div class="section">
    <form method="GET" action="/dashboard/deals" class="filters-form">
        <div class="form-row">
            <div class="form-group">
                <input type="text" name="search" placeholder="Search..." value="<?php echo htmlspecialchars($filters['search']); ?>">
            </div>
            <div class="form-group">
                <select name="deal_type">
                    <option value="">All Types</option>
                    <?php foreach ($deal_types as $key => $type): ?>
                        <option value="<?php echo $key; ?>" <?php echo $filters['deal_type'] === $key ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($type['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-secondary">Filter</button>
        </div>
    </form>
</div>

<!-- Deals Table -->
<div class="section">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Deal Code</th>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Updated</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($deals)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px;">
                            No deals found. <a href="/dashboard/deals/create">Create your first deal</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($deals as $deal): ?>
                        <tr>
                            <td><code><?php echo htmlspecialchars($deal['deal_code']); ?></code></td>
                            <td><strong><?php echo htmlspecialchars($deal['title']); ?></strong></td>
                            <td><?php echo htmlspecialchars($deal['deal_type']); ?></td>
                            <td><span class="badge badge-info"><?php echo htmlspecialchars($deal['status']); ?></span></td>
                            <td><?php echo htmlspecialchars($deal['quantity'] ?? '-'); ?> <?php echo htmlspecialchars($deal['quantity_unit'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($deal['price'] ?? '-'); ?> <?php echo htmlspecialchars($deal['currency'] ?? ''); ?></td>
                            <td><?php echo date('d M Y', strtotime($deal['updated_at'])); ?></td>
                            <td>
                                <a href="/dashboard/deals/<?php echo $deal['id']; ?>" class="btn btn-sm btn-secondary">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.filters-form { margin-bottom: 0; }
.form-row { display: flex; gap: 15px; align-items: flex-end; }
.form-group { flex: 1; }
.form-group input, .form-group select {
    width: 100%;
    padding: 10px 14px;
    border: 2px solid var(--border-color);
    border-radius: 6px;
}
</style>
