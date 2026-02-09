# Deal Desk Dashboard - Installationsanleitung

## System-Übersicht

Das Deal Desk Dashboard ist eine interne Webanwendung zur Verwaltung von Geschäftsabschlüssen (Deals) bei Krause Global Resources & Trade. Es wird im geschützten Bereich der Website unter `/dashboard` installiert.

## Voraussetzungen

- PHP 8.0 oder höher
- MySQL 5.7 oder MariaDB 10.3+
- Apache mit mod_rewrite
- Composer (für Abhängigkeiten)
- Optional: `pdftotext` (für schnellere PDF-Extraktion)

## Installation auf All-Inkl

### Schritt 1: Ordnerstruktur vorbereiten

Auf deinem All-Inkl Account sollte die Struktur so aussehen:

```
/www/htdocs/[username]/
├── httpdocs/                 ← Webroot (öffentlich)
│   ├── dashboard/           ← Dashboard-Installation
│   │   └── public/          ← Öffentliche Dateien
│   │       ├── index.php
│   │       └── assets/
│   └── [restliche Website]
└── storage/                  ← Außerhalb Webroot (WICHTIG!)
    ├── uploads/
    ├── extracted/
    ├── exports/
    ├── sessions/
    └── logs/
```

### Schritt 2: Dateien hochladen

1. **Lokale Vorbereitung:**
   ```bash
   cd dashboard
   composer install --no-dev --optimize-autoloader
   ```
   
   Falls Composer nicht installiert ist:
   - Download: https://getcomposer.org/download/
   - Alternative: Verwende die bereits generierten Dateien und lade vendor/ mit hoch

2. **FTP Upload:**
   - Ordner `dashboard/` nach `/httpdocs/dashboard/` hochladen
   - Ordner `storage/` nach `/storage/` (außerhalb httpdocs) hochladen

3. **Berechtigungen setzen (via SSH oder File Manager):**
   ```bash
   chmod -R 755 /storage
   chmod -R 777 /storage/uploads
   chmod -R 777 /storage/extracted
   chmod -R 777 /storage/exports
   chmod -R 777 /storage/sessions
   chmod -R 777 /storage/logs
   ```

### Schritt 3: Datenbank einrichten

1. **Datenbank erstellen** (via KAS - Kundenverwaltung):
   - Login: https://kas.all-inkl.com/
   - "Datenbanken" → "Neue Datenbank anlegen"
   - Name: z.B. `krause_deals`
   - Notiere: Datenbankname, Benutzername, Passwort, Host

2. **Datenbank importieren**:
   - Im KAS: "Datenbanken" → "phpMyAdmin" öffnen
   - Datenbank `krause_deals` auswählen
   - "Importieren" → Datei `database.sql` hochladen
   - "OK" klicken

3. **Standard-Admin-Zugang**:
   - Username: `admin`
   - Password: `ChangeMe2026!` (SOFORT ÄNDERN!)

### Schritt 4: Konfiguration

1. **.env Datei erstellen**:
   ```bash
   cp .env.example .env
   ```

2. **.env bearbeiten** mit deinen Datenbank-Zugangsdaten:
   ```env
   # Datenbank
   DB_HOST=localhost
   DB_NAME=krause_deals
   DB_USER=dein_db_benutzer
   DB_PASS=dein_db_passwort
   
   # Pfade (WICHTIG!)
   STORAGE_PATH=/www/htdocs/[username]/storage
   
   # Session
   SESSION_LIFETIME=3600
   CSRF_TOKEN_NAME=dashboard_csrf
   
   # Upload
   MAX_UPLOAD_SIZE=52428800
   ALLOWED_MIME_TYPES=application/pdf,image/png,image/jpeg
   
   # PDF Extraktion
   PDF_EXTRACTION_METHOD=auto
   ```

3. **Pfade prüfen** in `config/config.php`:
   - Stelle sicher, dass `STORAGE_PATH` korrekt gesetzt ist
   - Absoluter Pfad: `/www/htdocs/[dein-username]/storage`

### Schritt 5: .htaccess für Hauptverzeichnis

Erstelle `.htaccess` in `/httpdocs/dashboard/`:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^$ public/ [L]
    RewriteRule (.*) public/$1 [L]
</IfModule>
```

### Schritt 6: Testen

1. **Dashboard aufrufen**: https://krause-global.com/dashboard

2. **Login testen**:
   - Username: `admin`
   - Password: `ChangeMe2026!`

3. **Funktionen prüfen**:
   - Deal erstellen
   - Dokument hochladen
   - PDF-Extraktion testen
   - Suche ausprobieren

### Schritt 7: Sicherheit (WICHTIG!)

1. **Admin-Passwort ändern**:
   ```sql
   -- Via phpMyAdmin ausführen:
   UPDATE users 
   SET password = '$2y$10$[neues-bcrypt-hash]' 
   WHERE username = 'admin';
   ```
   
   Hash generieren mit PHP:
   ```php
   echo password_hash('DeinNeuesPasswort', PASSWORD_BCRYPT);
   ```

2. **.env schützen** (sollte bereits via .htaccess geschützt sein):
   - Datei darf NICHT öffentlich zugänglich sein
   - Prüfen: https://krause-global.com/dashboard/.env (sollte 403/404 zeigen)

3. **Storage schützen**:
   - Ordner ist außerhalb Webroot → sicher
   - Falls doch in httpdocs: .htaccess mit `Deny from all` erstellen

## Troubleshooting

### PDF-Extraktion funktioniert nicht
- **Problem**: pdftotext nicht verfügbar
- **Lösung**: System verwendet automatisch PHP-Fallback (smalot/pdfparser)
- Check: `dashboard/logs/extraction.log`

### Datei-Upload schlägt fehl
- **Problem**: Berechtigungen falsch
- **Lösung**: `chmod 777` für `/storage/uploads/`
- Check: PHP error log in KAS

### "Database connection failed"
- **Problem**: Falsche DB-Credentials in .env
- **Lösung**: Credentials aus KAS prüfen
- Host ist meist `localhost`

### Session Timeout zu kurz
- **Problem**: Nutzer werden zu schnell ausgeloggt
- **Lösung**: In .env `SESSION_LIFETIME=7200` (2 Stunden) setzen

### Composer-Fehler
- **Problem**: "Class not found" Fehler
- **Lösung**: `composer install` erneut ausführen oder vendor/ manuell hochladen

## Features

### Vollständig implementiert ✅
- ✅ Authentifizierung mit Session-Management
- ✅ Deal-Verwaltung (CRUD)
- ✅ Dokument-Upload mit Versionierung
- ✅ Automatische PDF-Text-Extraktion
- ✅ Volltextsuche in Dokumenten
- ✅ Party/Kontakt-Verwaltung
- ✅ Workflow-Checklisten
- ✅ Export (ZIP-Dossiers)
- ✅ Audit-Logging
- ✅ Responsive Design

### Optional/Erweiterbar
- ⏳ Mehrere Benutzer (derzeit nur Admin)
- ⏳ E-Mail-Benachrichtigungen
- ⏳ Erweiterte Workflow-Automatisierung
- ⏳ API für externe Integrationen

## Deal-Typen

Das System unterstützt 4 Deal-Typen:

1. **Energy Equipment** (Gas Turbines, Generator Sets)
2. **Energy Commodities** (EN590 ULSD 10ppm)
3. **Food** (Grain, Fish, Meat, Poultry)
4. **Fertilizer** (Urea 46%, NPK)

Jeder Typ hat eigene:
- Status-Pipeline
- Dokument-Kategorien (20+ pro Typ)
- Workflow-Templates

## Dokumenten-Management

### Unterstützte Dateiformate
- PDF (mit automatischer Text-Extraktion)
- PNG, JPG (Bilder)
- Max. Größe: 50MB (konfigurierbar)

### Kategorien (Auswahl)
- LOI Incoming / Outgoing
- ICPO, SCO, FCO
- POP (Proof of Product)
- POF (Proof of Funds)
- Contracts, Invoices
- Certificates, Licenses
- ...und 10+ weitere

## Support

Bei Problemen:
1. Logs prüfen: `/storage/logs/`
2. PHP Error Log im KAS prüfen
3. Browser Console für JS-Fehler

---

**Entwickelt für Krause Global Resources & Trade**  
Version: 1.0 MVP  
Datum: Januar 2025
