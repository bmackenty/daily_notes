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
<html class="h-100">
<head>
    <title>Daily Notes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
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
                <?php if ($activeYear && isset($activeYear['name'])): ?>
                    <span class="navbar-text text-white" data-bs-toggle="tooltip" title="Current Academic Year">
                        <i class="bi bi-calendar3 me-1"></i>
                        <?= htmlspecialchars($activeYear['name']) ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </nav>
