<?php
// includes/navbar.php
$activeLocation = $_SESSION['user_location'] ?? 'Chennai';
?>
<nav class="navbar navbar-expand-lg navbar-ecolens">
  <div class="container-fluid max-width-1200">
    <a class="navbar-brand d-flex align-items-center" href="<?= APP_URL ?>/index.php">
      <img src="<?= APP_URL ?>/assets/images/logo.png" alt="EcoLens Logo" class="brand-logo-img">
    </a>

    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
        <li class="nav-item">
          <a class="nav-link nav-link-ecolens" href="<?= APP_URL ?>/index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link nav-link-ecolens" href="<?= APP_URL ?>/products.php">Explore</a>
        </li>
        <li class="nav-item">
          <a class="nav-link nav-link-ecolens" href="<?= APP_URL ?>/compare.php">Compare</a>
        </li>
        <li class="nav-item">
          <a class="nav-link nav-link-ecolens" href="<?= APP_URL ?>/ecoswap.php">EcoSwap</a>
        </li>
        <li class="nav-item">
          <a class="nav-link nav-link-ecolens font-weight-bold text-success" href="<?= APP_URL ?>/add-product.php">
            <i class="fas fa-plus-circle me-1"></i> Add Product Details
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link nav-link-ecolens btn-allow-location" href="javascript:void(0)" title="Detect Location">
            <i class="fas fa-location-dot text-success me-1"></i> <?= sanitize($activeLocation) ?>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link nav-link-ecolens" href="<?= APP_URL ?>/methodology.php">Methodology</a>
        </li>
      </ul>

      <div class="d-flex align-items-center gap-2">
        <a href="<?= APP_URL ?>/business/dashboard.php" class="btn btn-sm btn-ecolens-secondary me-2">
          <i class="fas fa-building me-1"></i> Business
        </a>

        <?php if ($currentUser): ?>
          <div class="dropdown">
            <button class="btn btn-sm btn-ecolens-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-user-circle me-1"></i> <?= sanitize($currentUser['name']) ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
              <li><a class="dropdown-item" href="<?= APP_URL ?>/dashboard.php"><i class="fas fa-tachometer-alt me-2"></i> User Dashboard</a></li>
              <?php if ($currentUser['role'] === 'business' || $currentUser['role'] === 'admin'): ?>
                <li><a class="dropdown-item" href="<?= APP_URL ?>/business/dashboard.php"><i class="fas fa-chart-line me-2"></i> Business Portal</a></li>
              <?php endif; ?>
              <?php if ($currentUser['role'] === 'admin'): ?>
                <li><a class="dropdown-item" href="<?= APP_URL ?>/admin/dashboard.php"><i class="fas fa-user-shield me-2"></i> Admin Panel</a></li>
              <?php endif; ?>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-danger" href="<?= APP_URL ?>/api/auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
            </ul>
          </div>
        <?php else: ?>
          <a href="<?= APP_URL ?>/login.php" class="btn btn-sm btn-link text-decoration-none font-weight-bold text-navy me-2">Login</a>
          <a href="<?= APP_URL ?>/register.php" class="btn btn-sm btn-ecolens-primary">Get Started</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>
