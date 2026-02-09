<div class="page-header">
    <h1><?php echo htmlspecialchars($party['company_name']); ?></h1>
    <button onclick="history.back()" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </button>
</div>

<div class="section">
    <h2>Company Information</h2>
    <div class="info-grid">
        <div class="info-item">
            <label>Company Name</label>
            <span><?php echo htmlspecialchars($party['company_name']); ?></span>
        </div>
        <div class="info-item">
            <label>Country</label>
            <span><?php echo htmlspecialchars($party['country'] ?? '-'); ?></span>
        </div>
        <div class="info-item">
            <label>Website</label>
            <?php if ($party['website']): ?>
                <a href="<?php echo htmlspecialchars($party['website']); ?>" target="_blank">
                    <?php echo htmlspecialchars($party['website']); ?> <i class="fas fa-external-link-alt"></i>
                </a>
            <?php else: ?>
                <span>-</span>
            <?php endif; ?>
        </div>
        <div class="info-item">
            <label>Created</label>
            <span><?php echo date('d M Y', strtotime($party['created_at'])); ?></span>
        </div>
    </div>
    
    <?php if ($party['address_text']): ?>
        <div style="margin-top: 20px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px;">Address</label>
            <div style="background: var(--bg-light); padding: 15px; border-radius: 8px;">
                <?php echo nl2br(htmlspecialchars($party['address_text'])); ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<div class="section">
    <h2><i class="fas fa-address-book"></i> Contacts</h2>
    <p class="text-muted">Contact persons for this party</p>
    
    <?php if (empty($contacts)): ?>
        <p style="text-align: center; padding: 40px; color: var(--text-light);">
            No contacts added yet.
        </p>
    <?php else: ?>
        <div class="contacts-list">
            <?php foreach ($contacts as $contact): ?>
                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="contact-info">
                        <h3><?php echo htmlspecialchars($contact['full_name']); ?></h3>
                        <?php if ($contact['position']): ?>
                            <p class="text-muted"><?php echo htmlspecialchars($contact['position']); ?></p>
                        <?php endif; ?>
                        <?php if ($contact['email']): ?>
                            <p><i class="fas fa-envelope"></i> 
                                <a href="mailto:<?php echo htmlspecialchars($contact['email']); ?>">
                                    <?php echo htmlspecialchars($contact['email']); ?>
                                </a>
                            </p>
                        <?php endif; ?>
                        <?php if ($contact['phone']): ?>
                            <p><i class="fas fa-phone"></i> 
                                <a href="tel:<?php echo htmlspecialchars($contact['phone']); ?>">
                                    <?php echo htmlspecialchars($contact['phone']); ?>
                                </a>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 20px;
}
.info-item label {
    display: block;
    font-weight: 600;
    margin-bottom: 4px;
    color: var(--text-light);
    font-size: 0.9rem;
}
.info-item span, .info-item a {
    font-size: 1.1rem;
}
.contacts-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 20px;
}
.contact-card {
    background: var(--bg-light);
    border: 2px solid var(--border-color);
    border-radius: 8px;
    padding: 20px;
    display: flex;
    gap: 15px;
}
.contact-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, var(--accent) 0%, var(--secondary) 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    flex-shrink: 0;
}
.contact-info {
    flex: 1;
}
.contact-info h3 {
    margin-bottom: 8px;
    font-size: 1.1rem;
}
.contact-info p {
    margin: 5px 0;
    font-size: 0.9rem;
}
</style>
