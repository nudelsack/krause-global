#!/bin/bash
# ============================================
# Bild-Optimierungs-Skript für Krause Global
# ============================================
# 
# Dieses Skript komprimiert alle JPG und PNG Bilder
# und erstellt optional WebP-Versionen.
#
# Voraussetzung: ImageMagick installieren
#   brew install imagemagick
#
# Verwendung:
#   ./tools/optimize-images.sh
#
# ============================================

set -e

# Farben für Output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Konfiguration
IMAGES_DIR="images"
BACKUP_DIR="images_backup_$(date +%Y%m%d_%H%M%S)"
MAX_WIDTH=1920
JPEG_QUALITY=82
PNG_QUALITY=82
CREATE_WEBP=true

# Prüfen ob ImageMagick installiert ist
if ! command -v convert &> /dev/null; then
    echo -e "${RED}Fehler: ImageMagick ist nicht installiert.${NC}"
    echo "Bitte installieren mit: brew install imagemagick"
    exit 1
fi

# Zum Projektverzeichnis wechseln
cd "$(dirname "$0")/.."

echo -e "${YELLOW}======================================${NC}"
echo -e "${YELLOW}  Bild-Optimierung für Krause Global${NC}"
echo -e "${YELLOW}======================================${NC}"
echo ""

# Statistik vor Optimierung
TOTAL_SIZE_BEFORE=$(du -sh "$IMAGES_DIR" | cut -f1)
TOTAL_FILES=$(find "$IMAGES_DIR" -type f \( -name "*.jpg" -o -name "*.jpeg" -o -name "*.png" \) | wc -l | tr -d ' ')

echo -e "📁 Bilder-Ordner: ${GREEN}$IMAGES_DIR${NC}"
echo -e "📊 Aktuelle Größe: ${RED}$TOTAL_SIZE_BEFORE${NC}"
echo -e "🖼️  Anzahl Bilder: ${YELLOW}$TOTAL_FILES${NC}"
echo ""

# Backup erstellen
echo -e "${YELLOW}Erstelle Backup...${NC}"
cp -r "$IMAGES_DIR" "$BACKUP_DIR"
echo -e "✅ Backup erstellt: ${GREEN}$BACKUP_DIR${NC}"
echo ""

# Zähler
OPTIMIZED=0
SKIPPED=0
WEBP_CREATED=0

echo -e "${YELLOW}Optimiere Bilder...${NC}"
echo ""

# JPG/JPEG Bilder optimieren
while IFS= read -r file; do
    if [ -f "$file" ]; then
        ORIGINAL_SIZE=$(stat -f%z "$file" 2>/dev/null || stat -c%s "$file" 2>/dev/null)
        FILENAME=$(basename "$file")
        
        # Optimieren: Größe anpassen und Qualität reduzieren
        convert "$file" \
            -resize "${MAX_WIDTH}x${MAX_WIDTH}>" \
            -quality $JPEG_QUALITY \
            -strip \
            -interlace Plane \
            "$file"
        
        NEW_SIZE=$(stat -f%z "$file" 2>/dev/null || stat -c%s "$file" 2>/dev/null)
        SAVED=$((ORIGINAL_SIZE - NEW_SIZE))
        SAVED_KB=$((SAVED / 1024))
        
        if [ $SAVED -gt 0 ]; then
            echo -e "  ✅ ${FILENAME}: ${GREEN}-${SAVED_KB}KB${NC}"
            ((OPTIMIZED++))
        else
            echo -e "  ⏭️  ${FILENAME}: bereits optimiert"
            ((SKIPPED++))
        fi
        
        # WebP Version erstellen
        if [ "$CREATE_WEBP" = true ]; then
            WEBP_FILE="${file%.*}.webp"
            if [ ! -f "$WEBP_FILE" ]; then
                convert "$file" -quality 80 "$WEBP_FILE"
                ((WEBP_CREATED++))
            fi
        fi
    fi
done < <(find "$IMAGES_DIR" -type f \( -name "*.jpg" -o -name "*.jpeg" \))

# PNG Bilder optimieren
while IFS= read -r file; do
    if [ -f "$file" ]; then
        ORIGINAL_SIZE=$(stat -f%z "$file" 2>/dev/null || stat -c%s "$file" 2>/dev/null)
        FILENAME=$(basename "$file")
        
        # PNG optimieren
        convert "$file" \
            -resize "${MAX_WIDTH}x${MAX_WIDTH}>" \
            -quality $PNG_QUALITY \
            -strip \
            "$file"
        
        NEW_SIZE=$(stat -f%z "$file" 2>/dev/null || stat -c%s "$file" 2>/dev/null)
        SAVED=$((ORIGINAL_SIZE - NEW_SIZE))
        SAVED_KB=$((SAVED / 1024))
        
        if [ $SAVED -gt 0 ]; then
            echo -e "  ✅ ${FILENAME}: ${GREEN}-${SAVED_KB}KB${NC}"
            ((OPTIMIZED++))
        else
            echo -e "  ⏭️  ${FILENAME}: bereits optimiert"
            ((SKIPPED++))
        fi
        
        # WebP Version erstellen
        if [ "$CREATE_WEBP" = true ]; then
            WEBP_FILE="${file%.*}.webp"
            if [ ! -f "$WEBP_FILE" ]; then
                convert "$file" -quality 80 "$WEBP_FILE"
                ((WEBP_CREATED++))
            fi
        fi
    fi
done < <(find "$IMAGES_DIR" -type f -name "*.png")

echo ""
echo -e "${YELLOW}======================================${NC}"
echo -e "${GREEN}  Optimierung abgeschlossen!${NC}"
echo -e "${YELLOW}======================================${NC}"
echo ""

# Statistik nach Optimierung
TOTAL_SIZE_AFTER=$(du -sh "$IMAGES_DIR" | cut -f1)

echo -e "📊 Vorher:     ${RED}$TOTAL_SIZE_BEFORE${NC}"
echo -e "📊 Nachher:    ${GREEN}$TOTAL_SIZE_AFTER${NC}"
echo -e "✅ Optimiert:  ${GREEN}$OPTIMIZED${NC} Bilder"
echo -e "⏭️  Übersprungen: $SKIPPED Bilder"
if [ "$CREATE_WEBP" = true ]; then
    echo -e "🌐 WebP erstellt: ${GREEN}$WEBP_CREATED${NC} Dateien"
fi
echo ""
echo -e "💾 Backup gespeichert in: ${YELLOW}$BACKUP_DIR${NC}"
echo -e "   (Kann gelöscht werden wenn alles funktioniert)"
echo ""
echo -e "${YELLOW}Nächste Schritte:${NC}"
echo "1. Webseite testen ob alle Bilder korrekt angezeigt werden"
echo "2. Backup-Ordner löschen: rm -rf $BACKUP_DIR"
echo "3. Optional: WebP in HTML einbinden mit <picture> Element"
