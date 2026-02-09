<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $config['app']['name']; ?> - Anmeldung</title>
    <link rel="stylesheet" href="/assets/css/dashboard.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <h1>Krause Global</h1>
            <p>Deal Desk Dashboard</p>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    ⚠️ <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="/login">
                <div class="form-group">
                    <label for="username">Benutzername</label>
                    <input type="text" id="username" name="username" required autofocus placeholder="admin">
                </div>
                
                <div class="form-group">
                    <label for="password">Passwort</label>
                    <input type="password" id="password" name="password" required placeholder="••••••••">
                </div>
                
                <button type="submit" class="btn-login">🔐 Anmelden</button>
            </form>
            
            <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border); text-align: center;">
                <p style="color: var(--text-muted); font-size: 0.875rem;">
                    Standard-Zugangsdaten: <code style="background: var(--bg-gray); padding: 0.25rem 0.5rem; border-radius: 4px;">admin / admin123</code>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
