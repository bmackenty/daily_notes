<?php
$session = \App\Utils\SessionManager::getInstance();
$error = $session->getFlash('error');
$success = $session->getFlash('success');
$sessionExpired = isset($_GET['session']) && $_GET['session'] === 'expired';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Daily Notes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Login</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                        <?php endif; ?>
                        
                        <?php if ($sessionExpired): ?>
                            <div class="alert alert-warning">Your session has expired. Please login again.</div>
                        <?php endif; ?>
                        
                        <form method="POST" action="/login">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <small class="text-muted">
                                    After 5 failed attempts, your account will be locked for 15 minutes.
                                </small>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-3">
                            <a href="/register">Don't have an account? Register</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

