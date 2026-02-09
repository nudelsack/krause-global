<?php $title = $title ?? 'Alle Dokumente'; ?>

<div class="page-header">
    <h1>📁 Alle Dokumente</h1>
    <p>Übersicht aller hochgeladenen Dokumente und Dateien</p>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert" style="background: var(--success-bg); color: #065F46; border: 1px solid #A7F3D0; padding: 1rem 1.25rem; border-radius: 12px; margin-bottom: 1.5rem; font-weight: 500;">
        ✅ <?= htmlspecialchars($_SESSION['success']) ?>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<div style="margin-bottom: 2rem;">
    <a href="/dashboard/documents/upload" class="btn btn-primary">
        📤 Neues Dokument hochladen
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Dokumentenliste</h2>
        <span style="color: var(--text-muted);"><?= count($documents ?? []) ?> Dokumente</span>
    </div>
    
    <?php if (empty($documents)): ?>
        <div style="text-align: center; padding: 4rem 2rem; color: var(--text-muted);">
            <div style="font-size: 4rem; margin-bottom: 1.5rem;">📄</div>
            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-dark);">Noch keine Dokumente</h3>
            <p style="font-size: 1rem; margin-bottom: 2rem;">Laden Sie Ihr erstes Dokument hoch, um loszulegen.</p>
            <a href="/dashboard/documents/upload" class="btn btn-primary">
                📤 Erstes Dokument hochladen
            </a>
        </div>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Titel</th>
                    <th>Kategorie</th>
                    <th>Dateiname</th>
                    <th>Größe</th>
                    <th>Hochgeladen</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($documents as $doc): ?>
                    <tr>
                        <td>
                            <strong style="color: var(--text-dark);"><?= htmlspecialchars($doc['title']) ?></strong>
                            <?php if (!empty($doc['description'])): ?>
                                <br><small style="color: var(--text-muted); font-size: 0.875rem;"><?= htmlspecialchars(substr($doc['description'], 0, 100)) ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            $categoryLabels = [
                                'loi_incoming' => '📥 Eingehende LOI',
                                'loi_outgoing' => '📤 Ausgehende LOI',
                                'contract' => '📜 Vertrag',
                                'invoice' => '🧾 Rechnung',
                                'certificate' => '🏅 Zertifikat',
                                'specification' => '📋 Spezifikation',
                                'other' => '📄 Sonstiges'
                            ];
                            echo $categoryLabels[$doc['category'] ?? 'other'] ?? $doc['category'];
                            ?>
                        </td>
                        <td>
                            <code style="font-size: 0.875rem; color: var(--text-muted);"><?= htmlspecialchars($doc['file_name']) ?></code>
                        </td>
                        <td><?= number_format($doc['file_size'] / 1024, 1) ?> KB</td>
                        <td>
                            <?= date('d.m.Y H:i', strtotime($doc['created_at'])) ?><br>
                            <small style="color: var(--text-muted); font-size: 0.8125rem;">von <?= htmlspecialchars($doc['uploader_name'] ?? 'System') ?></small>
                        </td>
                        <td>
                            <a href="/dashboard/documents/download/<?= $doc['id'] ?>" class="btn btn-sm btn-primary" style="white-space: nowrap;">
                                ⬇️ Download
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
