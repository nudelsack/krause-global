<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $config['app']['name']; ?></title>
    <link rel="stylesheet" href="/assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h1>Krause Global</h1>
                <p>Deal Desk</p>
            </div>
            
            <nav class="sidebar-nav">
                <a href="/dashboard" class="nav-item <?php echo $_SERVER['REQUEST_URI'] === '/dashboard' || $_SERVER['REQUEST_URI'] === '/' ? 'active' : ''; ?>">
                    📊 Dashboard
                </a>
                <a href="/dashboard/pipeline" class="nav-item <?php echo strpos($_SERVER['REQUEST_URI'], '/pipeline') !== false ? 'active' : ''; ?>">
                    🎯 Pipeline
                </a>
                <a href="/dashboard/deals" class="nav-item <?php echo strpos($_SERVER['REQUEST_URI'], '/deals') !== false ? 'active' : ''; ?>">
                    🤝 Alle Deals
                </a>
                
                <div class="nav-section">
                    <div class="nav-section-title">LOIs & Angebote</div>
                    <a href="/dashboard/loi/incoming" class="nav-item <?php echo strpos($_SERVER['REQUEST_URI'], '/loi/incoming') !== false ? 'active' : ''; ?>">
                        📥 LOI Eingang
                    </a>
                    <a href="/dashboard/loi/outgoing" class="nav-item <?php echo strpos($_SERVER['REQUEST_URI'], '/loi/outgoing') !== false ? 'active' : ''; ?>">
                        📤 LOI Ausgang
                    </a>
                    <a href="/dashboard/offers/received" class="nav-item <?php echo strpos($_SERVER['REQUEST_URI'], '/offers/received') !== false ? 'active' : ''; ?>">
                        📩 Angebote erhalten
                    </a>
                    <a href="/dashboard/offers/sent" class="nav-item <?php echo strpos($_SERVER['REQUEST_URI'], '/offers/sent') !== false ? 'active' : ''; ?>">
                        📨 Angebote gesendet
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Dokumente</div>
                    <a href="/dashboard/documents" class="nav-item <?php echo strpos($_SERVER['REQUEST_URI'], '/documents') !== false && strpos($_SERVER['REQUEST_URI'], '/upload') === false ? 'active' : ''; ?>">
                        📁 Alle Dokumente
                    </a>
                    <a href="/dashboard/documents/upload" class="nav-item <?php echo strpos($_SERVER['REQUEST_URI'], '/upload') !== false ? 'active' : ''; ?>">
                        ☁️ Dokument hochladen
                    </a>
                </div>
                
                <a href="/dashboard/parties" class="nav-item <?php echo strpos($_SERVER['REQUEST_URI'], '/parties') !== false ? 'active' : ''; ?>">
                    👥 Kontakte
                </a>
                <a href="/dashboard/search" class="nav-item <?php echo strpos($_SERVER['REQUEST_URI'], '/search') !== false ? 'active' : ''; ?>">
                    🔍 Suche
                </a>
            </nav>
            
            <div class="sidebar-user">
                <div class="user-avatar">
                    <?= strtoupper(substr($user['username'] ?? 'A', 0, 1)) ?>
                </div>
                <div class="user-info">
                    <div class="user-name"><?= htmlspecialchars($user['username'] ?? 'Admin') ?></div>
                    <div class="user-role">Administrator</div>
                </div>
            </div>
            <form method="POST" action="/dashboard/logout" style="padding: 1rem 1.5rem;">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <button type="submit" class="btn btn-secondary" style="width: 100%;">
                    🚪 Abmelden
                </button>
            </form>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <div class="content-wrapper">
                <?php echo $content; ?>
            </div>
        </main>
    </div>
    
    <script src="/dashboard/assets/js/dashboard.js"></script>
</body>
</html>
