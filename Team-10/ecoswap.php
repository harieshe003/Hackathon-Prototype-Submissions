<?php
// ecoswap.php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/classes/ProductEngine.php';
require_once __DIR__ . '/classes/EcoSwapEngine.php';

$productId = intval($_GET['product_id'] ?? 1); // Default to plastic bottle
$city = sanitize($_SESSION['user_location'] ?? 'Chennai');

$productEngine = new ProductEngine();
$analysis = $productEngine->analyzeProduct($productId, $city);

$allProducts = $productEngine->searchProducts('', '', null, 50);

$ecoSwapEngine = new EcoSwapEngine();
$swapData = $analysis ? $ecoSwapEngine->recommendSwap($analysis['product'], $analysis['factorScores'], 4) : ['recommendations' => []];

$pageTitle = "EcoSwap — Find Greener Alternatives";
require_once __DIR__ . '/includes/header.php';
?>

<section class="py-4 bg-surface border-bottom">
  <div class="container max-width-1200">
    <div class="row align-items-center g-3">
      <div class="col-md-7">
        <h1 class="h2 font-weight-bold text-navy mb-1">🌱 EcoSwap Alternative Finder</h1>
        <p class="text-muted small mb-0">Discover eco-friendly product swaps that significantly reduce lifecycle environmental impact.</p>
      </div>

      <div class="col-md-5">
        <form action="<?= APP_URL ?>/ecoswap.php" method="GET" class="d-flex gap-2">
          <select name="product_id" class="form-select" onchange="this.form.submit()">
            <?php foreach ($allProducts as $p): ?>
              <option value="<?= $p['id'] ?>" <?= $p['id'] === $productId ? 'selected' : '' ?>>
                <?= sanitize($p['name']) ?> (<?= floatval($p['overall_score']) ?>/100)
              </option>
            <?php endforeach; ?>
          </select>
        </form>
      </div>
    </div>
  </div>
</section>

<section class="py-5">
  <div class="container max-width-1200">
    <?php if ($analysis): $curr = $analysis['product']; ?>
      <!-- Current Product Card -->
      <div class="ecolens-card p-4 mb-5 border-warning bg-light">
        <div class="row align-items-center g-4">
          <div class="col-md-2 text-center">
            <img src="<?= APP_URL ?>/<?= sanitize($curr['image']) ?>" style="height: 90px; object-fit: contain;">
          </div>
          <div class="col-md-7">
            <div class="badge bg-warning text-dark mb-1">CURRENT PRODUCT ANALYSIS</div>
            <h4 class="font-weight-bold text-navy mb-1"><?= sanitize($curr['name']) ?></h4>
            <p class="small text-muted mb-0"><?= sanitize($curr['brand']) ?> &bull; Material: <?= sanitize($curr['material']) ?> &bull; Packaging: <?= sanitize($curr['packaging_material']) ?></p>
          </div>
          <div class="col-md-3 text-center">
            <div class="h2 font-weight-bold text-navy mb-0"><?= floatval($analysis['contextualEcoScore']['contextual_score']) ?> / 100</div>
            <span class="badge badge-tag" style="background-color: <?= $analysis['contextualEcoScore']['color'] ?>; color: #fff;">
              <?= sanitize($analysis['contextualEcoScore']['classification']) ?>
            </span>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3 class="font-weight-bold text-navy mb-0">Recommended Greener Alternatives</h3>
      <span class="badge bg-light text-dark border">Based on the available environmental data.</span>
    </div>

    <?php if (empty($swapData['recommendations'])): ?>
      <div class="ecolens-card text-center p-5">
        <div class="h1 text-success mb-2"><i class="fas fa-circle-check"></i></div>
        <h4 class="font-weight-bold text-navy mb-1">This product already has an Exceptional Eco Score!</h4>
        <p class="text-muted small">Based on available environmental data, no higher-scoring alternative is currently needed.</p>
      </div>
    <?php else: ?>
      <div class="row g-4">
        <?php foreach ($swapData['recommendations'] as $swap): $alt = $swap['alternative']; ?>
          <div class="col-md-6">
            <div class="ecolens-card p-4 h-100 border-success d-flex flex-column">
              <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="d-flex align-items-center gap-3">
                  <img src="<?= APP_URL ?>/<?= sanitize($alt['image']) ?>" style="height: 70px; object-fit: contain;">
                  <div>
                    <h5 class="font-weight-bold text-navy mb-1"><?= sanitize($alt['name']) ?></h5>
                    <div class="small text-muted"><?= sanitize($alt['brand']) ?> &bull; <?= sanitize($alt['material']) ?></div>
                  </div>
                </div>
                <div class="text-end">
                  <div class="h3 font-weight-bold text-success mb-0"><?= floatval($alt['overall_score']) ?></div>
                  <span class="badge bg-success text-white"><?= $swap['score_improvement'] ?></span>
                </div>
              </div>

              <div class="small text-dark bg-surface p-3 rounded mb-3">
                <ul class="ps-3 mb-0">
                  <?php foreach ($swap['reasons'] as $r): ?>
                    <li><?= sanitize($r) ?></li>
                  <?php endforeach; ?>
                </ul>
              </div>

              <div class="mt-auto pt-2 border-top border-light d-flex justify-content-between align-items-center">
                <a href="<?= APP_URL ?>/product.php?id=<?= $alt['id'] ?>" class="btn btn-sm btn-ecolens-primary">View Product Specs</a>
                <a href="<?= APP_URL ?>/compare.php?ids=<?= $productId ?>,<?= $alt['id'] ?>" class="btn btn-sm btn-ecolens-secondary">Compare Matrix</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
