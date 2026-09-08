<?php
// business/dashboard.php
$pageTitle = "Business Portal — EcoLens";
require_once __DIR__ . '/../includes/header.php';
requireBusiness();

$user = getCurrentUser();
$db = Database::getInstance();

$stmtBiz = $db->prepare("SELECT * FROM businesses WHERE user_id = ? LIMIT 1");
$stmtBiz->execute([$user['id']]);
$business = $stmtBiz->fetch();

$companyName = $business['company_name'] ?? 'EcoBrand Producer';
$isVerified = !empty($business['is_verified']);

require_once __DIR__ . '/../classes/ProductEngine.php';
$productEngine = new ProductEngine();
$myProducts = $productEngine->searchProducts('', '', null, 10);
?>

<section class="py-4 bg-surface border-bottom">
  <div class="container max-width-1200">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <div class="small text-muted font-weight-bold text-uppercase mb-1">
          <i class="fas fa-building text-success me-1"></i> Business Portal
          <?php if ($isVerified): ?>
            <span class="badge bg-success text-white ms-2"><i class="fas fa-check-circle me-1"></i> Verified Producer</span>
          <?php endif; ?>
        </div>
        <h1 class="h2 font-weight-bold text-navy mb-0"><?= sanitize($companyName) ?></h1>
      </div>

      <div class="d-flex gap-2">
        <a href="<?= APP_URL ?>/business/add-product.php" class="btn btn-ecolens-primary"><i class="fas fa-plus me-1"></i> Add Product</a>
        <a href="<?= APP_URL ?>/business/simulator.php" class="btn btn-ecolens-secondary"><i class="fas fa-sliders me-1"></i> Impact Simulator</a>
      </div>
    </div>
  </div>
</section>

<section class="py-5">
  <div class="container max-width-1200">
    <!-- Stat Header -->
    <div class="row g-4 mb-5">
      <div class="col-md-3">
        <div class="ecolens-card p-4 text-center">
          <div class="small text-muted font-weight-bold text-uppercase mb-1">Products</div>
          <div class="display-5 font-weight-bold text-navy mb-1">48</div>
          <div class="small text-muted">In portfolio</div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="ecolens-card p-4 text-center">
          <div class="small text-muted font-weight-bold text-uppercase mb-1">Average Eco Score</div>
          <div class="display-5 font-weight-bold text-success mb-1">74</div>
          <div class="small text-muted">/ 100 &bull; Good</div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="ecolens-card p-4 text-center">
          <div class="small text-muted font-weight-bold text-uppercase mb-1">Verified Products</div>
          <div class="display-5 font-weight-bold text-teal mb-1">16</div>
          <div class="small text-muted">ISO 14040 verified</div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="ecolens-card p-4 text-center">
          <div class="small text-muted font-weight-bold text-uppercase mb-1">Improvement Opportunities</div>
          <div class="display-5 font-weight-bold text-primary mb-1">23</div>
          <div class="small text-muted">Identified by audit</div>
        </div>
      </div>
    </div>

    <!-- Product Portfolio Table -->
    <div class="ecolens-card p-4 mb-5">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="font-weight-bold text-navy mb-0">Product Portfolio</h4>
        <a href="<?= APP_URL ?>/business/add-product.php" class="btn btn-sm btn-ecolens-primary">+ Add New Product</a>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Product</th>
              <th>Category</th>
              <th>Material</th>
              <th>Eco Score</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($myProducts as $p): ?>
              <tr>
                <td class="font-weight-bold text-navy">
                  <img src="<?= APP_URL ?>/<?= sanitize($p['image']) ?>" style="height: 35px; width: 35px; object-fit: contain;" class="me-2">
                  <?= sanitize($p['name']) ?>
                </td>
                <td><?= sanitize($p['category']) ?></td>
                <td><?= sanitize($p['material']) ?></td>
                <td>
                  <span class="badge badge-tag <?= $p['rating_badge']['class'] ?>">
                    <?= floatval($p['overall_score']) ?> / 100
                  </span>
                </td>
                <td>
                  <?php if (!empty($p['is_verified'])): ?>
                    <span class="badge bg-success text-white"><i class="fas fa-check me-1"></i> Verified</span>
                  <?php else: ?>
                    <span class="badge bg-secondary">Unverified</span>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="btn-group btn-group-sm">
                    <a href="<?= APP_URL ?>/business/audit.php?product_id=<?= $p['id'] ?>" class="btn btn-outline-success" title="Audit"><i class="fas fa-magnifying-glass-chart"></i> Audit</a>
                    <a href="<?= APP_URL ?>/business/simulator.php?product_id=<?= $p['id'] ?>" class="btn btn-outline-primary" title="Simulate"><i class="fas fa-sliders"></i> Simulate</a>
                    <a href="<?= APP_URL ?>/business/verification.php?product_id=<?= $p['id'] ?>" class="btn btn-outline-teal" title="Verify"><i class="fas fa-certificate"></i> Verify</a>
                  </div>
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
