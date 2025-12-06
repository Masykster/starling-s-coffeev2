<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title><?php echo isset($pageTitle) ? $pageTitle : "Starling's Coffee"; ?></title>
    <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
    <!-- Preconnect to external domains for faster loading -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    
    <!-- Google Fonts - Load with font-display swap for better performance -->
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Open+Sans:wght@400;700&family=Oswald:wght@700&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Open+Sans:wght@400;700&family=Oswald:wght@700&display=swap" rel="stylesheet"></noscript>
    
    <!-- Bootstrap Icons - Load asynchronously -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css"></noscript>
    
    <!-- CSS Styles -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Header -->
    <header class="main-header">
        <nav class="navbar navbar-expand-lg navbar-light shadow-sm sticky-top" style="background-color: var(--header-bg);">
            <div class="container">
                <a class="navbar-brand" href="index.php">
                    <img src="images/logo.png" alt="Logo Starling Coffee" class="logo" style="height: 50px;">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto align-items-center">
                        <li class="nav-item">
                            <a class="nav-link fw-bold text-uppercase <?php echo (getCurrentPage() == 'menu') ? 'active' : ''; ?>" href="menu.php">MENU</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fw-bold text-uppercase <?php echo (getCurrentPage() == 'about') ? 'active' : ''; ?>" href="about.php">ABOUT</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fw-bold text-uppercase <?php echo (getCurrentPage() == 'contact') ? 'active' : ''; ?>" href="contact.php">CONTACT</a>
                        </li>
                        <?php if(isLoggedIn() || isAdmin()): 
                            if(isLoggedIn() && !isAdmin()):
                                $userId = getCurrentUserId();
                                // Cache cart count to avoid repeated DB queries
                                $cartCount = $userId ? getCartCount($userId) : 0;
                        ?>
                        <li class="nav-item ms-2">
                            <a class="nav-link position-relative" href="cart.php" aria-label="Keranjang">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l1.313 7h8.17l1.313-7H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                                </svg>
                                <?php if($cartCount > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                                    <?php echo $cartCount; ?>
                                </span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <?php endif; ?>
                        <li class="nav-item dropdown ms-2">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
                                </svg>
                                <span class="d-none d-md-inline ms-1"><?php echo htmlspecialchars(getCurrentUserName() ?? 'User'); ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <?php if(isAdmin()): ?>
                                <li><a class="dropdown-item" href="admin/index.php">
                                    <i class="bi bi-speedometer2"></i> Admin Dashboard
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <?php endif; ?>
                                <?php if(isLoggedIn() && !isAdmin()): 
                                    $userId = getCurrentUserId();
                                    $userPoints = $userId ? getUserTotalPoints($userId) : 0;
                                ?>
                                <li><a class="dropdown-item" href="cart.php">
                                    <i class="bi bi-cart"></i> Keranjang Saya
                                </a></li>
                                <li><a class="dropdown-item" href="orders.php">
                                    <i class="bi bi-receipt"></i> Riwayat Pesanan
                                </a></li>
                                <li><a class="dropdown-item" href="rewards.php">
                                    <i class="bi bi-star"></i> Rewards & Poin
                                    <?php if($userPoints > 0): ?>
                                    <span class="badge bg-warning text-dark float-end"><?php echo $userPoints; ?></span>
                                    <?php endif; ?>
                                </a></li>
                                <li><a class="dropdown-item" href="settings.php">
                                    <i class="bi bi-gear"></i> Pengaturan
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        </li>
                        <?php else: ?>
                        <li class="nav-item ms-2">
                            <a class="nav-link fw-bold" href="login.php">LOGIN</a>
                        </li>
                        <?php endif; ?>
                        <li class="nav-item ms-2">
                            <button id="theme-toggle" class="btn btn-link theme-toggle-btn p-2" aria-label="Toggle dark mode">
                                <svg id="theme-icon-sun" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="display:none; width: 24px; height: 24px; fill: currentColor;"><path d="M12 7c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5zM2 13h2c.55 0 1-.45 1-1s-.45-1-1-1H2c-.55 0-1 .45-1 1s.45 1 1 1zm18 0h2c.55 0 1-.45 1-1s-.45-1-1-1h-2c-.55 0-1 .45-1 1s.45 1 1 1zM11 2v2c0 .55.45 1 1 1s1-.45 1-1V2c0-.55-.45-1-1-1s-1 .45-1 1zm0 18v2c0 .55.45 1 1 1s1-.45 1-1v-2c0-.55-.45-1-1-1s-1 .45-1 1zM5.64 5.64c.39.39 1.02.39 1.41 0s.39-1.02 0-1.41L5.64 2.81c-.39-.39-1.02-.39-1.41 0s-.39 1.02 0 1.41L5.64 5.64zm12.73 12.73c.39.39 1.02.39 1.41 0s.39-1.02 0-1.41l-1.41-1.41c-.39-.39-1.02-.39-1.41 0s-.39 1.02 0 1.41l1.41 1.41zM2.81 18.36c.39.39 1.02.39 1.41 0l1.41-1.41c.39-.39.39-1.02 0-1.41s-1.02-.39-1.41 0L2.81 16.95c-.39.39-.39 1.02 0 1.41zm12.73-12.73c.39.39 1.02.39 1.41 0l1.41-1.41c.39-.39.39-1.02 0-1.41s-1.02-.39-1.41 0l-1.41 1.41c-.39.39-.39 1.02 0 1.41z"/></svg>
                                <svg id="theme-icon-moon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width: 24px; height: 24px; fill: currentColor;"><path d="M9.37 5.51A7.35 7.35 0 009 6c0 4.42 3.58 8 8 8 .34 0 .68-.02 1.01-.07C15.93 18.6 12.69 21 9 21c-4.97 0-9-4.03-9-9s4.03-9 9-9c.69 0 1.36.08 2 .24-.55.94-1.2 2.06-2.63 4.27z"/></svg>
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

