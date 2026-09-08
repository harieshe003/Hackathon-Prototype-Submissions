<?php
// business/simulator.php
$pageTitle = "Impact Simulator — Business Portal";
require_once __DIR__ . '/../includes/header.php';
requireBusiness();

$productId = intval($_GET['product_id'] ?? 1);
require_once __DIR__ . '/../classes/ProductEngine.php';
$productEngine = new ProductEngine();
$product = $productEngine->getProductById($productId);
$factors = $product['factors'] ?? [];
?>

<section class="py-4 bg-surface border-bottom">
  <div class="container max-width-1200">
    <h1 class="h2 font-weight-bold text-navy mb-1">🎛 Sustainability Impact Simulator</h1>
    <p class="text-muted small mb-0">Simulate alternative material, packaging, and lifecycle changes to preview score improvement in real-time.</p>
  </div>
</section>

<section class="py-5">
  <div class="container max-width-1200">
    <div class="row g-4">
      <!-- Sliders Column -->
      <div class="col-lg-7">
        <div class="ecolens-card p-4">
          <h5 class="font-weight-bold text-navy mb-4"><i class="fas fa-sliders text-success me-2"></i> Adjust Environmental Parameters</h5>
          
          <form id="sim-form">
            <div class="mb-3">
              <label class="form-label font-weight-bold small text-navy d-flex justify-content-between">
                <span>Carbon Footprint Score (0-10)</span>
                <span id="val-carbon"><?= floatval($factors['carbon_score'] ?? 5) ?></span>
              </label>
              <input type="range" min="1" max="10" step="0.5" class="form-range sim-slider" name="carbon_score" value="<?= floatval($factors['carbon_score'] ?? 5) ?>">
            </div>

            <div class="mb-3">
              <label class="form-label font-weight-bold small text-navy d-flex justify-content-between">
                <span>Material Intensity Score (0-10)</span>
                <span id="val-material"><?= floatval($factors['material_score'] ?? 5) ?></span>
              </label>
              <input type="range" min="1" max="10" step="0.5" class="form-range sim-slider" name="material_score" value="<?= floatval($factors['material_score'] ?? 5) ?>">
            </div>

            <div class="mb-3">
              <label class="form-label font-weight-bold small text-navy d-flex justify-content-between">
                <span>Recyclability Score (0-10)</span>
                <span id="val-recyclability"><?= floatval($factors['recyclability_score'] ?? 5) ?></span>
              </label>
              <input type="range" min="1" max="10" step="0.5" class="form-range sim-slider" name="recyclability_score" value="<?= floatval($factors['recyclability_score'] ?? 5) ?>">
            </div>

            <div class="mb-3">
              <label class="form-label font-weight-bold small text-navy d-flex justify-content-between">
                <span>Reusability Score (0-10)</span>
                <span id="val-reusability"><?= floatval($factors['reusability_score'] ?? 5) ?></span>
              </label>
              <input type="range" min="1" max="10" step="0.5" class="form-range sim-slider" name="reusability_score" value="<?= floatval($factors['reusability_score'] ?? 5) ?>">
            </div>

            <div class="mb-3">
              <label class="form-label font-weight-bold small text-navy d-flex justify-content-between">
                <span>Packaging Score (0-10)</span>
                <span id="val-packaging"><?= floatval($factors['packaging_score'] ?? 5) ?></span>
              </label>
              <input type="range" min="1" max="10" step="0.5" class="form-range sim-slider" name="packaging_score" value="<?= floatval($factors['packaging_score'] ?? 5) ?>">
            </div>
          </form>
        </div>
      </div>

      <!-- Live Calculated Score Preview -->
      <div class="col-lg-5">
        <div class="ecolens-card p-4 text-center sticky-top" style="top: 90px;">
          <div class="small text-muted font-weight-bold text-uppercase mb-1">ORIGINAL SCORE</div>
          <div class="h4 font-weight-bold text-muted mb-3"><?= floatval($product['scores']['overall_score'] ?? 50) ?> / 100</div>

          <div class="p-4 bg-surface rounded border mb-4">
            <div class="small text-muted font-weight-bold text-uppercase mb-1">PROJECTED SIMULATED SCORE</div>
            <div id="sim-score-val" class="display-3 font-weight-bold text-navy mb-1"><?= floatval($product['scores']['overall_score'] ?? 50) ?></div>
            <div id="sim-score-rating" class="badge badge-tag badge-excellent">Good</div>
          </div>

          <div style="height: 200px; position: relative;">
            <canvas id="sim-thinai-canvas"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const sliders = document.querySelectorAll('.sim-slider');
    sliders.forEach(slider => {
        slider.addEventListener('input', (e) => {
            const field = e.target.name.replace('_score', '');
            const valSpan = document.getElementById('val-' + field);
            if (valSpan) valSpan.innerText = e.target.value;
            runSimulation();
        });
    });

    runSimulation();
});

function runSimulation() {
    const form = document.getElementById('sim-form');
    const formData = new FormData(form);

    fetch(`${APP_URL}/api/business/simulate.php`, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('sim-score-val').innerText = data.simulated_score;
            document.getElementById('sim-score-rating').innerText = data.rating;
            renderEcoLensRadarChart('sim-thinai-canvas', data.thinai);
        }
    });
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
