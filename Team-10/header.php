<?php
// includes/header.php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/auth.php';

$currentUser = getCurrentUser();
$pageTitle = $pageTitle ?? 'EcoLens — Five Lenses. One Sustainable Choice.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= sanitize($pageTitle) ?></title>
  <meta name="description" content="EcoLens — Compare products based on lifecycle environmental factors, Thinai ecological landscapes, and location context.">
  
  <!-- Favicon -->
  <link rel="icon" type="image/png" href="<?= APP_URL ?>/assets/images/logo.png">
  
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- FontAwesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <!-- Leaflet CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  
  <!-- Custom EcoLens Styles -->
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
  
  <script>
    const APP_URL = "<?= APP_URL ?>";
  </script>
</head>
<body>
<?php require_once __DIR__ . '/navbar.php'; ?>
<main class="flex-grow-1">
