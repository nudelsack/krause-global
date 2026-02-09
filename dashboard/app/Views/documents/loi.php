<div class="page-header">
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <p class="welcome-text"><?php echo htmlspecialchars($subtitle); ?></p>
</div>

<div class="section">
    <div class="section-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2><?php echo count($documents); ?> Dokumente</h2>
        </div>
        <a href="/dashboard/documents/upload" class="btn btn-primary">
            <i class="fas fa-plus"></i> Neues Dokument hochladen
        </a>
    </div>

    <?php if (empty($documents)): ?>
        <div style="text-align: center; padding: 60px 20px; color: #94A3B8;">
            <div style="font-size: 48px; margin-bottom: 16px;">
                <?php echo $category === 'incoming' ? '📥' : '📤'; ?>
            </div>
            <h3 style="color: #475569; margin-bottom: 8px;">
                Noch keine <?php echo $category === 'incoming' ? 'eingehenden' : 'ausgehenden'; ?> LOIs
            </h3>
            <p>Laden Sie ein Dokument hoch, um zu beginnen.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Titel</th>
                        <th>Kategorie</th>
                        <th>Versionen</th>
                        <th>Hochgeladen</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($documents as $doc): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($doc['title']); ?></strong>
                                <?php if (!empty($doc['description'])): ?>
                                    <br>
                                    <small style="color: #64748B;"><?php echo htmlspecialchars($doc['description']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $categoryLabels = [
                                    'loi_incoming' => '📥 Eingehende LOI',
                                    'loi_outgoing' => '📤 Ausgehende LOI',
                                    'contract' => '📄 Vertrag',
                                    'invoice' => '💰 Rechnung',
                                    'other' => '📋 Sonstiges'
                                ];
                                echo $categoryLabels[$doc['category']] ?? htmlspecialchars($doc['category']);
                                ?>
                            </td>
                            <td>
                                <span class="badge badge-info">
                                    <?php echo $doc['version_count'] ?? 0; ?> Version(en)
                                </span>
                            </td>
                            <td><?php echo $doc['last_uploaded'] ? date('d M Y, H:i', strtotime($doc['last_uploaded'])) : '-'; ?></td>
                            <td>
                                <div class="btn-group">
                                    <a href="/dashboard/documents/<?php echo $doc['id']; ?>/preview" class="btn btn-sm btn-secondary" title="Vorschau">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="/dashboard/documents/<?php echo $doc['id']; ?>/download" class="btn btn-sm btn-primary" title="Herunterladen">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <form method="POST" action="/dashboard/documents/<?php echo $doc['id']; ?>/delete" style="display: inline;" onsubmit="return confirm('Dokument wirklich löschen?');">
                                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?? ''; ?>">
                                        <button type="submit" class="btn btn-sm" style="background: #EF4444; color: white;" title="Löschen">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
