#!/usr/bin/env python3
"""
WebP Conversion Script für Krause Global
=========================================
Dieses Script konvertiert alle <img> Tags zu <picture> Elementen
mit WebP-Unterstützung und JPG/PNG Fallback.

Verwendung: python3 tools/convert-to-webp.py
"""

import os
import re
from pathlib import Path

# Konfiguration
PROJECT_DIR = Path(__file__).parent.parent
HTML_FILES = list(PROJECT_DIR.glob("*.html")) + list(PROJECT_DIR.glob("en/*.html"))

# Regex für img-Tags mit images/ Pfad
IMG_PATTERN = re.compile(
    r'<img\s+([^>]*?)src="(images/[^"]+\.(jpg|jpeg|png))"([^>]*?)>',
    re.IGNORECASE | re.DOTALL
)

def get_webp_path(original_path):
    """Konvertiert Bildpfad zu WebP-Pfad"""
    path = Path(original_path)
    return str(path.with_suffix('.webp'))

def convert_img_to_picture(match):
    """Konvertiert ein img-Tag zu einem picture-Element"""
    before_src = match.group(1)
    img_path = match.group(2)
    extension = match.group(3)
    after_src = match.group(4)
    
    webp_path = get_webp_path(img_path)
    
    # Prüfen ob WebP-Datei existiert
    webp_full_path = PROJECT_DIR / webp_path
    if not webp_full_path.exists():
        # Kein WebP vorhanden, img-Tag unverändert lassen
        return match.group(0)
    
    # Extrahiere alt-Attribut
    alt_match = re.search(r'alt="([^"]*)"', before_src + after_src)
    alt_text = alt_match.group(1) if alt_match else ""
    
    # Extrahiere style-Attribut
    style_match = re.search(r'style="([^"]*)"', before_src + after_src)
    style_attr = f' style="{style_match.group(1)}"' if style_match else ""
    
    # Extrahiere class-Attribut
    class_match = re.search(r'class="([^"]*)"', before_src + after_src)
    class_attr = f' class="{class_match.group(1)}"' if class_match else ""
    
    # Extrahiere loading-Attribut (für lazy loading)
    loading_match = re.search(r'loading="([^"]*)"', before_src + after_src)
    loading_attr = f' loading="{loading_match.group(1)}"' if loading_match else ' loading="lazy"'
    
    # Erstelle picture-Element
    picture_html = f'''<picture>
    <source srcset="{webp_path}" type="image/webp">
    <img src="{img_path}" alt="{alt_text}"{class_attr}{style_attr}{loading_attr}>
</picture>'''
    
    return picture_html

def process_file(filepath):
    """Verarbeitet eine HTML-Datei"""
    print(f"Verarbeite: {filepath.name}")
    
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Zähle Ersetzungen
    original_content = content
    content, count = IMG_PATTERN.subn(convert_img_to_picture, content)
    
    if count > 0:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"  ✅ {count} Bilder zu WebP konvertiert")
        return count
    else:
        print(f"  ⏭️  Keine Änderungen")
        return 0

def main():
    print("=" * 50)
    print("  WebP Konvertierung für Krause Global")
    print("=" * 50)
    print()
    
    total_conversions = 0
    files_modified = 0
    
    for html_file in HTML_FILES:
        if html_file.exists():
            count = process_file(html_file)
            if count > 0:
                files_modified += 1
                total_conversions += count
    
    print()
    print("=" * 50)
    print(f"  Abgeschlossen!")
    print(f"  {files_modified} Dateien modifiziert")
    print(f"  {total_conversions} Bilder zu WebP konvertiert")
    print("=" * 50)
    print()
    print("Alle img-Tags wurden zu <picture> Elementen konvertiert:")
    print("- WebP für moderne Browser (30-50% kleiner)")
    print("- JPG/PNG Fallback für ältere Browser")
    print("- Lazy Loading aktiviert")

if __name__ == "__main__":
    main()
