<?php
// admin/products.php
$pageTitle = "Product Moderation — Admin Portal";
require_once __DIR__ . '/../includes/header.php';
requireAdmin();

require_once __DIR__ . '/../classes/ProductEngine.php';
$productEngine = new ProductEngine();
$products = $productEngine->searchProducts('', '', null, 100);
?>

<section class="py-4 bg-surface border-bottom">
  <div class="container max-width-1200">
    <h1 class="h2 font-weight-bold text-navy mb-1">Product Catalog Moderation</h1>
    <p class="text-muted small mb-0">Review platform product listings and verified status badges.</p>
  </div>
</section>

<section class="py-5">
  <div class="container max-width-1200">
    <div class="ecolens-card p-4">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>Image</th>
              <th>Product Name</th>
              <th>Brand</th>
              <th>Category</th>
              <th>Eco Score</th>
              <th>Verified</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products as $p): ?>
              <tr>
                <td>#<?= $p['id'] ?></td>
                <td>
                  <img src="<?= APP_URL ?>/<?= sanitize($p['image']) ?>" alt="<?= sanitize($p['name']) ?>" style="height: 40px; width: 40px; object-fit: contain;" class="rounded border p-1 bg-white">
                </td>
                <td class="font-weight-bold text-navy"><?= sanitize($p['name']) ?></td>
                <td><?= sanitize($p['brand']) ?></td>
                <td><?= sanitize($p['category']) ?></td>
                <td><span class="badge badge-tag <?= $p['rating_badge']['class'] ?>"><?= floatval($p['overall_score']) ?> / 100</span></td>
                <td><?= !empty($p['is_verified']) ? '<span class="badge bg-success">Verified</span>' : '<span class="badge bg-secondary">Unverified</span>' ?></td>
                <td>
                  <a href="<?= APP_URL ?>/product.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-ecolens-primary" target="_blank">View Details</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
