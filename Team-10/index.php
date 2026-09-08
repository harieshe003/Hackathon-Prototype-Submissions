<?php
// index.php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = "EcoLens — Five Lenses. One Sustainable Choice.";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/classes/ProductEngine.php';

$productEngine = new ProductEngine();
$featuredProducts = $productEngine->searchProducts('', '', null, 4);
?>

<!-- Hero Section -->
<section class="hero-section text-center text-lg-start">
  <div class="container max-width-1200">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <div class="badge badge-tag badge-excellent mb-3">
          <i class="fas fa-leaf me-1"></i> Ecological Product Comparison Platform
        </div>
        <h1 class="hero-title mb-3">
          SEE BEYOND<br>
          THE PRODUCT<span>.</span>
        </h1>
        <p class="hero-subtitle mb-4">
          Compare products through five ecological lenses. Understand the environmental impact behind every raw material, factory, and packaging choice.
        </p>

        <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start">
          <a href="<?= APP_URL ?>/compare.php" class="btn btn-ecolens-primary btn-lg">
            <i class="fas fa-scale-balanced me-2"></i> Compare Products
          </a>
          <a href="<?= APP_URL ?>/products.php" class="btn btn-ecolens-secondary btn-lg">
            <i class="fas fa-compass me-2"></i> Explore EcoLens
          </a>
        </div>
      </div>

      <div class="col-lg-6 text-center">
        <!-- Interactive Globe & 5 Thinai Orbiting Lenses -->
        <div class="lens-container">
          <div class="lens-orbit-track"></div>
          
          <!-- 5 Thinai Orbiting Group -->
          <div class="thinai-orbit-group">
            <div class="thinai-orbit-slot slot-1">
              <div class="thinai-pill kurinji" title="Kurinji — Mountainous & Mining Ecosystem">⛰️ Kurinji</div>
            </div>
            <div class="thinai-orbit-slot slot-2">
              <div class="thinai-pill mullai" title="Mullai — Forest & Biodiversity Ecosystem">🌿 Mullai</div>
            </div>
            <div class="thinai-orbit-slot slot-3">
              <div class="thinai-pill marutham" title="Marutham — Agricultural & Soil Health">🌾 Marutham</div>
            </div>
            <div class="thinai-orbit-slot slot-4">
              <div class="thinai-pill neithal" title="Neithal — Coastal & Marine Ecosystem">🌊 Neithal</div>
            </div>
            <div class="thinai-orbit-slot slot-5">
              <div class="thinai-pill palai" title="Palai — Arid & Resource Stress Zone">☀️ Palai</div>
            </div>
          </div>

          <!-- Center Globe -->
          <div class="central-globe-wrapper">
            <img src="<?= APP_URL ?>/assets/images/globe.png" alt="EcoLens Global Intelligence" class="center-globe-img">
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Trust Metrics Section -->
<section class="py-5 bg-white border-bottom border-top border-light">
  <div class="container max-width-1200">
    <div class="row text-center g-4">
      <div class="col-6 col-md-3">
        <div class="h2 font-weight-bold text-navy mb-1 animate-counter" data-target="100" data-suffix="+">0</div>
        <div class="small text-muted font-weight-bold text-uppercase">Products Analysed</div>
      </div>
      <div class="col-6 col-md-3">
        <div class="h2 font-weight-bold text-green mb-1 animate-counter" data-target="25">0</div>
        <div class="small text-muted font-weight-bold text-uppercase">Environmental Factors</div>
      </div>
      <div class="col-6 col-md-3">
        <div class="h2 font-weight-bold text-teal mb-1 animate-counter" data-target="5">0</div>
        <div class="small text-muted font-weight-bold text-uppercase">Ecological Lenses</div>
      </div>
      <div class="col-6 col-md-3">
        <div class="h2 font-weight-bold text-navy mb-1 animate-counter" data-target="100">0</div>
        <div class="small text-muted font-weight-bold text-uppercase">Sustainability Score Scale</div>
      </div>
    </div>
  </div>
</section>

<!-- How EcoLens Works Section -->
<section class="py-5">
  <div class="container max-width-1200">
    <div class="text-center max-width-700 mx-auto mb-5">
      <h2 class="h1 font-weight-bold text-navy mb-2">How EcoLens Works</h2>
      <p class="text-muted">A 4-step data lifecycle journey from location intelligence to sustainable action.</p>
    </div>

    <div class="row g-4">
      <div class="col-md-3">
        <div class="ecolens-card text-center h-100 p-4">
          <div class="h1 text-success font-weight-bold mb-3">01</div>
          <h5 class="font-weight-bold text-navy mb-2">Select Location</h5>
          <p class="small text-muted mb-0">Capture local water stress, coastal vulnerability, and ecosystem context.</p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="ecolens-card text-center h-100 p-4">
          <div class="h1 text-teal font-weight-bold mb-3">02</div>
          <h5 class="font-weight-bold text-navy mb-2">Choose Products</h5>
          <p class="small text-muted mb-0">Search products, paste URLs, scan barcodes, or enter lifecycle parameters.</p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="ecolens-card text-center h-100 p-4">
          <div class="h1 text-primary font-weight-bold mb-3">03</div>
          <h5 class="font-weight-bold text-navy mb-2">Analyse Environmental Impact</h5>
          <p class="small text-muted mb-0">Evaluate 9 factor scores across manufacturing, packaging, transport, and usage.</p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="ecolens-card text-center h-100 p-4">
          <div class="h1 text-success font-weight-bold mb-3">04</div>
          <h5 class="font-weight-bold text-navy mb-2">Make a Better Choice</h5>
          <p class="small text-muted mb-0">Discover EcoSwap recommendations and upgrade to greener alternatives.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Featured Product Comparison Preview -->
<section class="py-5 bg-surface border-top border-light">
  <div class="container max-width-1200">
    <div class="d-flex justify-content-between align-items-end mb-4">
      <div>
        <h2 class="h2 font-weight-bold text-navy mb-1">Featured Sustainability Analyses</h2>
        <p class="text-muted mb-0">Explore verified lifecycle scores calculated across global products.</p>
      </div>
      <a href="<?= APP_URL ?>/products.php" class="btn btn-sm btn-ecolens-secondary d-none d-md-inline-flex">
        View Catalog <i class="fas fa-arrow-right ms-1"></i>
      </a>
    </div>

    <div class="row g-4">
      <?php foreach ($featuredProducts as $prod): ?>
        <div class="col-md-6 col-lg-3">
          <div class="ecolens-card h-100 d-flex flex-column">
            <div class="text-center p-3 mb-2 rounded bg-light position-relative">
              <img src="<?= APP_URL ?>/<?= sanitize($prod['image']) ?>" alt="<?= sanitize($prod['name']) ?>" style="height: 120px; object-fit: contain;">
              <span class="position-absolute top-0 end-0 m-2 badge badge-tag <?= $prod['rating_badge']['class'] ?>">
                <?= floatval($prod['overall_score']) ?> / 100
              </span>
            </div>
            <h6 class="font-weight-bold text-navy mb-1"><?= sanitize($prod['name']) ?></h6>
            <div class="small text-muted mb-2"><?= sanitize($prod['brand']) ?> &bull; <?= sanitize($prod['category']) ?></div>
            <div class="mt-auto pt-3 border-top border-light d-flex justify-content-between align-items-center">
              <span class="small font-weight-bold text-success"><?= sanitize($prod['rating']) ?></span>
              <div class="btn-group">
                <a href="<?= APP_URL ?>/product.php?id=<?= $prod['id'] ?>" class="btn btn-sm btn-ecolens-primary">Details</a>
                <a href="<?= APP_URL ?>/compare.php?add=<?= $prod['id'] ?>" class="btn btn-sm btn-ecolens-secondary" title="Add to Comparison"><i class="fas fa-plus"></i></a>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
