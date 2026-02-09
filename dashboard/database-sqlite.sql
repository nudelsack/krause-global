-- SQLite version for local development
-- Simplified schema without MySQL-specific features

CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    email TEXT,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS deals (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    deal_code TEXT NOT NULL UNIQUE,
    title TEXT NOT NULL,
    deal_type TEXT NOT NULL,
    subtype TEXT,
    status TEXT DEFAULT 'draft',
    quantity REAL,
    unit TEXT,
    price REAL,
    currency TEXT DEFAULT 'USD',
    incoterms TEXT,
    origin_port TEXT,
    destination_port TEXT,
    reference TEXT,
    notes TEXT,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    updated_at TEXT DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS parties (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    company_name TEXT NOT NULL,
    country TEXT,
    website TEXT,
    address_text TEXT,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS contacts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    party_id INTEGER NOT NULL,
    full_name TEXT NOT NULL,
    position TEXT,
    email TEXT,
    phone TEXT,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (party_id) REFERENCES parties(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS deal_parties (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    deal_id INTEGER NOT NULL,
    party_id INTEGER NOT NULL,
    role TEXT NOT NULL,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (deal_id) REFERENCES deals(id) ON DELETE CASCADE,
    FOREIGN KEY (party_id) REFERENCES parties(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS documents (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    deal_id INTEGER NOT NULL,
    title TEXT NOT NULL,
    category TEXT NOT NULL,
    source_type TEXT DEFAULT 'incoming',
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (deal_id) REFERENCES deals(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS document_versions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    document_id INTEGER NOT NULL,
    version_no INTEGER DEFAULT 1,
    file_path TEXT NOT NULL,
    mime_type TEXT NOT NULL,
    file_size INTEGER NOT NULL,
    sha256_hash TEXT NOT NULL,
    uploaded_at TEXT DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (document_id) REFERENCES documents(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS extracted_texts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    document_version_id INTEGER NOT NULL,
    extracted_text TEXT,
    extraction_method TEXT,
    extracted_at TEXT DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (document_version_id) REFERENCES document_versions(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS audit_log (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    action TEXT NOT NULL,
    object_type TEXT NOT NULL,
    object_id INTEGER NOT NULL,
    payload TEXT,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert default admin user (password: ChangeMe2026!)
INSERT INTO users (username, password, email) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@krause-global.com');

-- Sample data for testing
INSERT INTO deals (deal_code, title, deal_type, subtype, status, quantity, unit, price, currency, incoterms, origin_port, destination_port) VALUES
('EE-2026-A1B2C3', 'Siemens SGT-600 Gas Turbine', 'energy_equipment', 'gas_turbines', 'lead', 50.0, 'MW', 25000000.00, 'USD', 'DDP', 'Hamburg, Germany', 'Abu Dhabi, UAE'),
('EC-2026-D4E5F6', 'EN590 ULSD 10ppm Supply', 'energy_commodities', 'en590_ulsd', 'negotiation', 100000.0, 'MT', 850.00, 'USD', 'FOB', 'Rotterdam', 'Singapore'),
('FD-2026-G7H8I9', 'Wheat Export', 'food', 'grain', 'contract', 50000.0, 'MT', 285.00, 'USD', 'CFR', 'Odessa, Ukraine', 'Lagos, Nigeria');
