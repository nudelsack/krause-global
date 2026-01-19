from __future__ import annotations

from pathlib import Path


def apply_replacements(text: str) -> str:
    replacements: list[tuple[str, str]] = [
        ("<html lang=\"de\">", "<html lang=\"en\">"),
        (
            "<title>Fisch & Meeresfrüchte - B2B Seafood Portfolio | Krause Global Resources</title>",
            "<title>Fish & Seafood - B2B Seafood Portfolio | Krause Global Resources</title>",
        ),
        (
            "<meta name=\"description\" content=\"Professionelles B2B Seafood Portfolio: Delikatessen, Weißfisch, Lachsarten, Meeresfrüchte. Über 30 Jahre Erfahrung im Fisch- und Meeresfrüchtehandel.\">",
            "<meta name=\"description\" content=\"Professional B2B seafood portfolio: delicacies & preserves, whitefish, salmon species, seafood. Over 30 years of experience in seafood trading.\">",
        ),
        ("<link rel=\"stylesheet\" href=\"css/style.css\">", "<link rel=\"stylesheet\" href=\"../css/style.css\">"),
        ("<link rel=\"stylesheet\" href=\"css/pages.css\">", "<link rel=\"stylesheet\" href=\"../css/pages.css\">"),
        ("<script src=\"js/main.js\"></script>", "<script src=\"../js/main.js\"></script>"),
        # Nav labels
        (">Über uns<", ">About Us<"),
        (">Leistungen<", ">Services<"),
        (">Rohstoffe & Waren<", ">Commodities<"),
        (">Globale Präsenz<", ">Global Presence<"),
        (">Unsere Werte<", ">Our Values<"),
        (">Kontakt<", ">Contact<"),
        (">Fisch & Seafood<", ">Fish & Seafood<"),
        # Language switcher
        ("<option value=\"fish.html\" selected>DE</option>", "<option value=\"../fish.html\">DE</option>"),
        ("<option value=\"en/fish.html\">EN</option>", "<option value=\"fish.html\" selected>EN</option>"),
        # Page header
        ("<h1><i class=\"fas fa-fish\"></i> Fisch & Meeresfrüchte</h1>", "<h1><i class=\"fas fa-fish\"></i> Fish & Seafood</h1>"),
        ("<p class=\"breadcrumb\"><a href=\"index.html\">Home</a> / Fisch & Meeresfrüchte</p>", "<p class=\"breadcrumb\"><a href=\"index.html\">Home</a> / Fish & Seafood</p>"),
        ("B2B Seafood Portfolio für Gastronomie, Einzelhandel und Verarbeitung.", "B2B seafood portfolio for food service, retail, and processing."),
        ("Mehr als 30 Jahre Erfahrung im Fisch- und Meeresfrüchtehandel.", "Over 30 years of experience in seafood trading."),
        # Intro
        (
            "<h2 class=\"section-title\" style=\"margin-bottom: 2rem;\">Professionelles Seafood Portfolio</h2>",
            "<h2 class=\"section-title\" style=\"margin-bottom: 2rem;\">Professional Seafood Portfolio</h2>",
        ),
        (
            "Wir liefern Fisch und Meeresfrüchte für B2B-Anwendungen, Gastronomie, Einzelhandel und Verarbeitung.",
            "We supply fish and seafood for B2B applications, food service, retail, and processing.",
        ),
        (
            "Das Portfolio umfasst Konservenprodukte, Weißfisch, Lachsarten, Makrelenartige, Plattfische sowie ausgewählte Meeresfrüchte.",
            "The portfolio includes preserved products, whitefish, salmon species, mackerel species, flatfish, and selected seafood.",
        ),
        (
            "Produktformen, Fangmethoden, Fanggebiete und Verpackungen sind je Artikel dokumentiert.",
            "Product forms, catch methods, fishing areas, and packaging are documented per item.",
        ),
        ("<strong style=\"color: var(--primary-navy);\">Preishinweis:</strong>", "<strong style=\"color: var(--primary-navy);\">Price note:</strong>"),
        (
            "Preise auf der Website sind aktuell als Einkaufspreis-Orientierung hinterlegt und werden im Angebot final bestätigt.",
            "Prices on this website are provided as purchase-price guidance and are finally confirmed in the quotation.",
        ),
        # Categories overview
        ("<h2 class=\"section-title\" style=\"margin-bottom: 3rem;\">Produktkategorien</h2>", "<h2 class=\"section-title\" style=\"margin-bottom: 3rem;\">Product Categories</h2>"),
        ("Delikatessen & Konserven", "Delicacies & Preserves"),
        ("Details ansehen", "View details"),
        ("Fisch - Norwegisches Meer", "Fish - Norwegian Sea"),
        ("Fisch - Pazifik (FAO 61)", "Fish - Pacific (FAO 61)"),
        ("Lachsarten", "Salmon Species"),
        ("Meeresfrüchte", "Seafood"),
        # Common labels
        ("Verfügbare Gebinde:", "Available pack sizes:"),
        ("Orientierungspreis:", "Reference price:"),
        ("* Einkaufspreis, finale Preise im Angebot", "* Purchase price, final prices confirmed in quotation"),
        ("* Einkaufspreise als Orientierung", "* Purchase prices as guidance"),
        ("Weitere Störarten verfügbar:", "Other sturgeon varieties available:"),
        ("Zusätzliche Informationen", "Additional information"),
        ("Verfügbare Formen:", "Available forms:"),
        ("Verfügbare Zuschnitte:", "Available cuts:"),
        ("Verfügbare Zustände:", "Available states:"),
        ("Fanggebiet & Methoden:", "Fishing area & methods:"),
        ("Fanggebiet:", "Fishing area:"),
        ("Fangmethode:", "Catch method:"),
        ("Größenbereich:", "Size range:"),
        ("Größenbereiche:", "Size ranges:"),
        ("Herkunft:", "Origin:"),
        ("Primär:", "Primary:"),
        ("Nutzung:", "Usage:"),
        ("Wildfang", "Wild-caught"),
        ("Nachhaltig", "Sustainable"),
        ("MSC verfügbar", "MSC available"),
        # Quality & docs
        ("Qualität & Dokumente", "Quality & Documentation"),
        ("Wichtiger Hinweis zu Verfügbarkeit & Dokumentation", "Important note on availability & documentation"),
        ("Qualität aus Erfahrung", "Quality through experience"),
        ("Gesundheitszertifikat", "Health Certificate"),
        ("Fangbescheinigung", "Catch Certificate"),
        ("Ursprungszertifikat", "Certificate of Origin"),
        ("CITES für Kaviar", "CITES for Caviar"),
        ("Nachhaltigkeitszertifikate", "Sustainability Certificates"),
        # Logistics
        ("Logistik & Verpackung", "Logistics & Packaging"),
        ("Temperaturführung & Produktzustände", "Temperature Control & Product States"),
        ("Verpackungsoptionen", "Packaging Options"),
        ("Kühlketten-Logistik & Lieferbedingungen", "Cold-chain logistics & delivery terms"),
        ("Temperaturüberwachung", "Temperature monitoring"),
        ("Lieferoptionen", "Delivery options"),
        ("Lieferzeiten", "Lead times"),
        ("MOQ & Mindestbestellmengen", "MOQ & minimum order quantities"),
        # Form
        ("B2B Anfrage stellen", "Submit a B2B Inquiry"),
        ("Füllen Sie das folgende Formular aus, um eine individuelle Anfrage für Seafood-Produkte zu stellen.", "Fill out the form below to request a tailored offer for seafood products."),
        ("Wir erstellen Ihnen ein maßgeschneidertes Angebot mit Preisen, Verfügbarkeit und Lieferbedingungen.", "We will prepare a customized quotation including pricing, availability, and delivery terms."),
        ("Unternehmensdaten", "Company Details"),
        ("Firmenname", "Company name"),
        ("Ansprechpartner", "Contact person"),
        ("E-Mail", "Email"),
        ("Telefon", "Phone"),
        ("Zielmarkt & Logistik", "Destination & Logistics"),
        ("Zielland", "Destination country"),
        ("Bitte wählen...", "Please select..."),
        ("Deutschland", "Germany"),
        ("Österreich", "Austria"),
        ("Schweiz", "Switzerland"),
        ("Frankreich", "France"),
        ("Niederlande", "Netherlands"),
        ("Belgien", "Belgium"),
        ("Polen", "Poland"),
        ("Tschechien", "Czech Republic"),
        ("Italien", "Italy"),
        ("Spanien", "Spain"),
        ("Vereinigtes Königreich", "United Kingdom"),
        ("Anderes Land", "Other country"),
        ("Zielhafen / Stadt", "Destination port / city"),
        ("Produktspezifikation", "Product Specification"),
        ("Produktkategorie", "Product category"),
        ("Spezifisches Produkt", "Specific product"),
        ("Zuschnitt / Form", "Cut / form"),
        ("Temperaturführung", "Temperature control"),
        ("Frisch gekühlt", "Chilled (fresh)"),
        ("Konserven", "Canned"),
        ("Verpackung", "Packaging"),
        ("Gewünschte Menge", "Requested quantity"),
        ("Dokumente & Verwendungszweck", "Documents & Use Case"),
        ("Benötigte Dokumente", "Required documents"),
        ("Zielsegment", "Target segment"),
        ("Retail / Einzelhandel", "Retail"),
        ("HoReCa / Gastronomie", "HoReCa / Food service"),
        ("Food Processing / Verarbeitung", "Food processing"),
        ("Wholesale / Großhandel", "Wholesale"),
        ("Zusätzliche Informationen / Spezifikationen", "Additional information / specifications"),
        ("Spezifikationen / Dokumente hochladen (optional)", "Upload specifications / documents (optional)"),
        ("Anfrage absenden", "Submit inquiry"),
        ("Wir bearbeiten Ihre Anfrage innerhalb von 24-48 Stunden und senden Ihnen ein detailliertes Angebot.", "We process your inquiry within 24–48 hours and will send you a detailed quotation."),
        # CTA
        ("Interesse an unserem Seafood Portfolio?", "Interested in our seafood portfolio?"),
        ("Kontaktieren Sie uns für detaillierte Produktspezifikationen, Verfügbarkeit und individuelle Angebote.", "Contact us for detailed specifications, availability, and tailored quotations."),
        ("Anfrage senden", "Send inquiry"),
        # Footer
        ("Unternehmen", "Company"),
        ("Rechtliches", "Legal"),
        ("Impressum", "Imprint"),
        ("Datenschutz", "Privacy Policy"),
        ("Alle Rechte vorbehalten.", "All rights reserved."),
    ]

    for old, new in replacements:
        text = text.replace(old, new)

    return text


def main() -> None:
    path = Path(__file__).resolve().parents[1] / 'en' / 'fish.html'
    original = path.read_text(encoding='utf-8')
    updated = apply_replacements(original)
    path.write_text(updated, encoding='utf-8')
    print(f'Updated {path}')


if __name__ == '__main__':
    main()
