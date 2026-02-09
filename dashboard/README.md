# Krause Global Deal Desk Dashboard - README

## Overview
This is an internal Deal Desk Dashboard for Krause Global Resources & Trade. It manages end-to-end deal documentation, party management, document uploads with automatic PDF text extraction, full-text search, and export capabilities.

## Features
- ✅ Secure authentication with session management
- ✅ Deal management (Energy Equipment, Commodities, Food, Fertilizer)
- ✅ Party and contact management
- ✅ Document upload with versioning
- ✅ Automatic PDF text extraction
- ✅ Full-text search across documents
- ✅ Workflow tracking with configurable templates
- ✅ Export deals as ZIP dossiers
- ✅ Comprehensive audit logging

## Technology Stack
- **Backend:** PHP 8.0+
- **Database:** MySQL/MariaDB
- **PDF Extraction:** smalot/pdfparser (with pdftotext fallback)
- **Architecture:** MVC pattern with custom routing

## Quick Start
1. Upload to All-Inkl webspace
2. Run `composer install`
3. Configure `.env` with database credentials
4. Import `database.sql`
5. Set storage permissions
6. Login with admin/ChangeMe2026!

See `INSTALLATION.md` for detailed setup instructions.

## Default Credentials
**Username:** admin  
**Password:** ChangeMe2026!

**⚠️ Change this immediately after first login!**

## Project Structure
```
dashboard/
├── app/
│   ├── Controllers/     # Request handlers
│   ├── Models/          # Data models
│   ├── Services/        # Business logic
│   ├── Views/           # Templates
│   └── Core/            # Framework (Router, Auth, DB)
├── config/              # Configuration files
├── public/              # Web root
│   ├── index.php        # Front controller
│   └── assets/          # Static files
└── vendor/              # Dependencies
```

## Deal Types
1. **Energy Equipment** - Gas Turbines, Generator Sets
2. **Energy Commodities** - EN590 ULSD 10ppm
3. **Food** - Grain, Fish, Meat
4. **Fertilizer** - Urea, NPK

## Document Categories
- LOI (Incoming/Outgoing)
- ICPO, SCO
- Product Passport, TSR
- SGS Q&Q
- Invoices
- Contracts
- Payment Proofs
- And more...

## Security Features
- Password hashing (bcrypt)
- CSRF token protection
- Session timeout
- Secure file uploads (mime-type validation)
- SQL injection prevention (prepared statements)
- Access control (authentication required)

## License
Proprietary - Krause Global Resources & Trade

## Contact
For support or questions, contact your system administrator.
