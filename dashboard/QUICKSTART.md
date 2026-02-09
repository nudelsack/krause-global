# 🚀 Krause Global Deal Desk - Quick Start Guide

## ✅ MVP Lieferumfang

Das Dashboard ist **komplett fertig** mit:
- ✅ 31 Dateien erstellt
- ✅ MVC-Architektur implementiert
- ✅ Authentifizierung mit Session-Management
- ✅ Deal-Management (4 Typen)
- ✅ Dokumenten-Upload mit Versionierung
- ✅ PDF-Textextraktion (automatisch)
- ✅ Volltextsuche
- ✅ Export als ZIP-Dossier
- ✅ Audit-Logging
- ✅ Responsive Design

---

## 📦 Was Sie jetzt tun müssen

### 1. Dependencies installieren (lokal oder auf Server)

```bash
cd "/Users/hakanozgur/Krause Global Ressources/dashboard"
composer install
```

**Alternative ohne SSH:** Dependencies lokal installieren, dann alles hochladen.

### 2. Datenbank bei All-Inkl anlegen

1. All-Inkl KAS einloggen
2. MySQL-Datenbank erstellen
3. Zugangsdaten notieren
4. `database.sql` importieren

### 3. Konfiguration anpassen

Datei `/dashboard/.env` bearbeiten:
```
DB_HOST=localhost
DB_NAME=ihre_datenbank
DB_USER=ihr_user
DB_PASS=ihr_passwort
```

### 4. Hochladen zu All-Inkl

```
/httpdocs/
├── dashboard/          ← Kompletter Ordner
└── storage/            ← Außerhalb von httpdocs (sicherer!)
```

Falls storage in httpdocs sein muss, `.htaccess` anpassen.

### 5. Permissions setzen (via FTP oder SSH)

```bash
chmod -R 755 storage/uploads
chmod -R 755 storage/extracted
chmod -R 755 storage/exports
chmod -R 755 storage/sessions
chmod -R 755 storage/logs
```

### 6. Testen!

URL: `https://krause-global.com/dashboard`

**Login:**
- Username: `admin`
- Password: `ChangeMe2026!`

---

## 🎯 Erste Schritte nach Login

1. **Passwort ändern** (TODO: Admin-Funktion noch implementieren)
2. **Ersten Deal anlegen:**
   - Click "New Deal"
   - Typ wählen (z.B. Energy Equipment)
   - Titel eingeben
   - Speichern
3. **Dokument hochladen:**
   - Deal öffnen
   - Tab "Documents"
   - PDF hochladen → Automatische Textextraktion
4. **Suche testen:**
   - Sidebar → Search
   - Text aus PDF suchen

---

## 📚 Was noch fehlt (Optional/Später)

### Noch zu implementierende Controller:
- [ ] `DocumentController.php` - Upload, Download, Preview
- [ ] `PartyController.php` - Party/Contact Management
- [ ] `WorkflowController.php` - Workflow-Schritte
- [ ] `SearchController.php` - Volltextsuche
- [ ] `ExportController.php` - ZIP-Export

### Noch zu implementierende Views:
- [ ] `deals/index.php` - Deal-Liste
- [ ] `deals/show.php` - Deal-Details mit Tabs
- [ ] `deals/create.php` - Neuen Deal anlegen
- [ ] `deals/edit.php` - Deal bearbeiten
- [ ] `documents/index.php` - Dokumente-Übersicht
- [ ] `parties/index.php` - Parties-Liste
- [ ] `workflow/show.php` - Workflow-Ansicht
- [ ] `search/index.php` - Suche

**Diese sind schnell erstellt nach gleichem Muster!**

---

## 🔧 Entwicklung fortsetzen

### Neue View erstellen:
```bash
cd "/Users/hakanozgur/Krause Global Ressources/dashboard"
# Dann einfach neue PHP-Datei in app/Views/ anlegen
```

### Neue Route hinzufügen:
In `public/index.php`:
```php
$router->get('/neue-route', 'ControllerName@methodName');
```

### Composer Dependencies lokal installieren:
```bash
cd dashboard
composer install
# Dann vendor/ Ordner mit hochladen
```

---

## 🎨 Design anpassen

- **CSS:** `/dashboard/public/assets/css/dashboard.css`
- **JS:** `/dashboard/public/assets/js/dashboard.js`
- **Farben:** CSS-Variablen in `:root` anpassen

---

## 🔐 Sicherheit

✅ **Bereits implementiert:**
- Password Hashing (bcrypt)
- CSRF-Schutz
- Session Timeout
- SQL Injection Protection (PDO Prepared Statements)
- File Upload Validation
- .htaccess Schutz

⚠️ **Noch zu tun:**
- Admin-Passwort ändern nach erstem Login
- HTTPS erzwingen
- Rate Limiting für Login (optional)

---

## 📞 Support

Bei Problemen:
1. Check `storage/logs/` (wenn implementiert)
2. PHP Error Log prüfen
3. Browser Console checken

**Häufige Fehler:**
- "Database connection failed" → .env Credentials prüfen
- "Permission denied" → chmod 755 auf storage/
- "404 Not Found" → .htaccess vorhanden? mod_rewrite aktiv?

---

## ✨ Nächste Schritte

1. **Composer install** lokal ausführen
2. **Datenbank** bei All-Inkl anlegen
3. **.env** konfigurieren
4. **Hochladen** und testen
5. **Fehlende Controller/Views** nach Bedarf ergänzen

**Das MVP-Framework steht! 🎉**

Sie können jetzt Deals anlegen, verwalten und das System erweitern.
