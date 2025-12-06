<?php
requireAdminLogin();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Admin Dashboard'; ?> - Starling Coffee</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --sidebar-width: 250px;
        }
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0;
            z-index: 1000;
            overflow-y: auto;
        }
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar-menu li a {
            display: block;
            padding: 1rem 1.5rem;
            color: white;
            text-decoration: none;
            transition: background 0.3s;
        }
        .sidebar-menu li a:hover,
        .sidebar-menu li a.active {
            background: rgba(255,255,255,0.1);
        }
        .sidebar-menu li a i {
            width: 20px;
            margin-right: 10px;
        }
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
        }
        .top-bar {
            background: white;
            padding: 1rem 2rem;
            margin: -2rem -2rem 2rem -2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .card {
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s;
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h4 class="mb-0">Starling Coffee</h4>
            <small class="text-white-50">Admin Panel</small>
        </div>
        <ul class="sidebar-menu">
            <li><a href="index.php" class="<?php echo $currentPage == 'index' ? 'active' : ''; ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a></li>
            <li><a href="orders.php" class="<?php echo $currentPage == 'orders' ? 'active' : ''; ?>">
                <i class="bi bi-cart-check"></i> Orders
            </a></li>
            <li><a href="menu.php" class="<?php echo $currentPage == 'menu' ? 'active' : ''; ?>">
                <i class="bi bi-menu-button-wide"></i> Menu Items
            </a></li>
            <li><a href="categories.php" class="<?php echo $currentPage == 'categories' ? 'active' : ''; ?>">
                <i class="bi bi-tags"></i> Categories
            </a></li>
            <li><a href="users.php" class="<?php echo $currentPage == 'users' ? 'active' : ''; ?>">
                <i class="bi bi-people"></i> Users
            </a></li>
            <li><a href="rewards.php" class="<?php echo $currentPage == 'rewards' ? 'active' : ''; ?>">
                <i class="bi bi-star"></i> Rewards
            </a></li>
            <li><a href="settings.php" class="<?php echo $currentPage == 'settings' ? 'active' : ''; ?>">
                <i class="bi bi-gear"></i> Settings
            </a></li>
            <li><a href="../logout.php">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a></li>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="top-bar">
            <div>
                <button class="btn btn-link d-md-none" onclick="document.getElementById('sidebar').classList.toggle('show')">
                    <i class="bi bi-list"></i>
                </button>
                <h5 class="d-inline ms-2"><?php echo isset($pageTitle) ? $pageTitle : 'Dashboard'; ?></h5>
            </div>
            <div>
                <span class="text-muted">Welcome, <strong><?php echo htmlspecialchars(getCurrentAdminName() ?? 'Admin'); ?></strong></span>
                <a href="../index.php" class="btn btn-sm btn-outline-secondary ms-2" target="_blank">
                    <i class="bi bi-box-arrow-up-right"></i> View Site
                </a>
                <a href="../logout.php" class="btn btn-sm btn-outline-danger ms-2">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>

