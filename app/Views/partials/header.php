<?php
use App\Models\AcademicYear;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Use the global PDO connection
global $pdo;

// Initialize AcademicYear model with database connection
$academicYearModel = new AcademicYear($pdo);
$activeYear = $academicYearModel->getActive();
?>
<!DOCTYPE html>
<html class="h-100" data-bs-theme="light">
<head>
    <title>Daily Notes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/rowreorder/1.4.1/css/rowReorder.bootstrap5.min.css">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">

</head>
<body class="d-flex flex-column h-100">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">Daily Notes</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/admin/dashboard">Admin</a>
                            </li>
                        <?php endif; ?>
                   
                        <li class="nav-item">
                            <a class="nav-link" href="/logout">Logout</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <div class="d-flex align-items-center">
                    <!-- Warsaw Time -->
                    <?php 
                    date_default_timezone_set('Europe/Warsaw');
                    ?>
                    <span class="navbar-text text-white-50 small me-3" data-bs-toggle="tooltip" title="Warsaw, Poland Time">
                        <i class="bi bi-clock me-1"></i>
                        <?= date('M j, g:i A') ?>
                    </span>
                    
                    <!-- Dark Mode Toggle -->
                    <button class="btn btn-outline-light btn-sm me-3" id="darkModeToggle" type="button" title="Toggle Dark Mode">
                        <i class="bi bi-moon-fill" id="darkModeIcon"></i>
                    </button>
                    <?php if ($activeYear && isset($activeYear['name'])): ?>
                        <span class="navbar-text text-white" data-bs-toggle="tooltip" title="Current Academic Year">
                            <i class="bi bi-calendar3 me-1"></i>
                            <?= htmlspecialchars($activeYear['name']) ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Dark mode functionality
document.addEventListener('DOMContentLoaded', function() {
    const darkModeToggle = document.getElementById('darkModeToggle');
    const darkModeIcon = document.getElementById('darkModeIcon');
    const html = document.documentElement;
    
    // Check for saved theme preference or default to light mode
    const currentTheme = localStorage.getItem('theme') || 'light';
    html.setAttribute('data-bs-theme', currentTheme);
    updateIcon(currentTheme);
    
    // Toggle dark mode
    darkModeToggle.addEventListener('click', function() {
        const currentTheme = html.getAttribute('data-bs-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        html.setAttribute('data-bs-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        updateIcon(newTheme);
    });
    
    function updateIcon(theme) {
        if (theme === 'dark') {
            darkModeIcon.className = 'bi bi-sun-fill';
            darkModeToggle.title = 'Switch to Light Mode';
        } else {
            darkModeIcon.className = 'bi bi-moon-fill';
            darkModeToggle.title = 'Switch to Dark Mode';
        }
    }
});
</script>
