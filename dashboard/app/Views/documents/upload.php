<?php $title = $title ?? 'Dokument hochladen'; ?>

<div class="page-header">
    <h1>📤 Dokument hochladen</h1>
    <p>Laden Sie LOIs, Verträge oder andere wichtige Dokumente hoch</p>
</div>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-error">
        <?= htmlspecialchars($_SESSION['error']) ?>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<div class="card">
    <form action="/dashboard/documents/store" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Dokumenttitel *</label>
            <input type="text" id="title" name="title" class="form-control" required placeholder="z.B. LOI Siemens SGT-600">
        </div>
        
        <div class="form-group">
            <label for="category">Kategorie *</label>
            <select id="category" name="category" class="form-select" required>
                <option value="">Bitte wählen...</option>
                <optgroup label="LOIs (Absichtserklärungen)">
                    <option value="loi_incoming">📥 LOI Eingang</option>
                    <option value="loi_outgoing">📤 LOI Ausgang</option>
                </optgroup>
                <optgroup label="Angebote (Konkrete Ware)">
                    <option value="offer_received">📩 Angebot erhalten</option>
                    <option value="offer_sent">📨 Angebot gesendet</option>
                </optgroup>
                <optgroup label="Sonstiges">
                    <option value="contract">📄 Vertrag</option>
                    <option value="invoice">💰 Rechnung</option>
                    <option value="certificate">🏆 Zertifikat</option>
                    <option value="specification">📋 Spezifikation</option>
                    <option value="other">📁 Sonstiges</option>
                </optgroup>
            </select>
        </div>
        
        <div class="form-group">
            <label for="description">Beschreibung</label>
            <textarea id="description" name="description" class="form-control" rows="4" placeholder="Optional: Zusätzliche Informationen zum Dokument"></textarea>
        </div>
        
        <div class="form-group">
            <label for="document">Datei auswählen *</label>
            <input type="file" id="document" name="document" class="form-control" required accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
            <small style="color: var(--text-muted); display: block; margin-top: 0.5rem;">
                Erlaubte Formate: PDF, Word, Excel, JPG, PNG (max. 10 MB)
            </small>
        </div>
        
        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button type="submit" class="btn btn-primary">
                📤 Hochladen
            </button>
            <a href="/dashboard/documents" class="btn btn-secondary">
                Abbrechen
            </a>
        </div>
    </form>
</div>

<style>
.form-control[type="file"] {
    padding: 0.75rem;
}
</style>
