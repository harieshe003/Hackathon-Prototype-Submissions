<?php
// location.php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = "Location Intelligence — EcoLens";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/classes/LocationEngine.php';

$activeCity = $_SESSION['user_location'] ?? 'Chennai';
$locationEngine = new LocationEngine();
$profile = $locationEngine->getLocationProfile($activeCity);
?>

<section class="py-4 bg-surface border-bottom">
  <div class="container max-width-1200">
    <div class="row align-items-center g-3">
      <div class="col-md-7">
        <h1 class="h2 font-weight-bold text-navy mb-1">📍 Location Intelligence Center</h1>
        <p class="text-muted small mb-0">Discover how local ecosystem stress (coastal marine proximity, water scarcity, urbanization) modifies environmental factor relevance.</p>
      </div>
      <div class="col-md-5 text-md-end">
        <button id="btn-get-location" class="btn btn-ecolens-primary">
          <i class="fas fa-location-arrow me-1"></i> Use My Location
        </button>
      </div>
    </div>
  </div>
</section>

<section class="py-5">
  <div class="container max-width-1200">
    <div class="row g-4">
      <!-- Interactive Leaflet Map Column -->
      <div class="col-lg-7">
        <div class="ecolens-card p-3 h-100">
          <h5 class="font-weight-bold text-navy mb-3"><i class="fas fa-map-marked-alt text-success me-2"></i> Select Map Coordinates</h5>
          <div id="ecolens-map-container" style="height: 380px; width: 100%; border-radius: 12px;" class="border"></div>
          <div class="small text-muted mt-2 text-center">Click or drag the marker to update regional environmental context.</div>
        </div>
      </div>

      <!-- Environmental Context & Thinai Relevance Profile -->
      <div class="col-lg-5">
        <div class="ecolens-card p-4 h-100">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="font-weight-bold text-navy mb-0">Environmental Context</h5>
            <span class="badge bg-success text-white"><?= sanitize($profile['city']) ?>, <?= sanitize($profile['country']) ?></span>
          </div>

          <div class="p-3 bg-surface rounded mb-4 border">
            <div class="row text-center g-2 small">
              <div class="col-4">
                <div class="text-muted font-weight-bold">Coastal</div>
                <div class="font-weight-bold text-navy"><?= sanitize($profile['coastal_proximity']) ?></div>
              </div>
              <div class="col-4">
                <div class="text-muted font-weight-bold">Water Stress</div>
                <div class="font-weight-bold text-navy"><?= sanitize($profile['water_stress']) ?></div>
              </div>
              <div class="col-4">
                <div class="text-muted font-weight-bold">Urbanization</div>
                <div class="font-weight-bold text-navy"><?= sanitize($profile['urbanization']) ?></div>
              </div>
            </div>
          </div>

          <h6 class="font-weight-bold text-navy mb-3">Thinai Landscape Relevance</h6>
          
          <div class="mb-3">
            <div class="d-flex justify-content-between small font-weight-bold mb-1">
              <span>Neithal (Coastal / Marine Waste)</span>
              <span class="text-success"><?= floatval($profile['relevance']['neithal']) ?>%</span>
            </div>
            <div class="progress" style="height: 8px;">
              <div class="progress-bar bg-success" style="width: <?= floatval($profile['relevance']['neithal']) ?>%;"></div>
            </div>
          </div>

          <div class="mb-3">
            <div class="d-flex justify-content-between small font-weight-bold mb-1">
              <span>Palai (Resource / Water Scarcity)</span>
              <span class="text-teal"><?= floatval($profile['relevance']['palai']) ?>%</span>
            </div>
            <div class="progress" style="height: 8px;">
              <div class="progress-bar bg-info" style="width: <?= floatval($profile['relevance']['palai']) ?>%;"></div>
            </div>
          </div>

          <div class="mb-3">
            <div class="d-flex justify-content-between small font-weight-bold mb-1">
              <span>Marutham (Agri & Soil)</span>
              <span class="text-navy"><?= floatval($profile['relevance']['marutham']) ?>%</span>
            </div>
            <div class="progress" style="height: 8px;">
              <div class="progress-bar bg-primary" style="width: <?= floatval($profile['relevance']['marutham']) ?>%;"></div>
            </div>
          </div>

          <div class="mb-3">
            <div class="d-flex justify-content-between small font-weight-bold mb-1">
              <span>Mullai (Forest & Bio)</span>
              <span class="text-muted"><?= floatval($profile['relevance']['mullai']) ?>%</span>
            </div>
            <div class="progress" style="height: 8px;">
              <div class="progress-bar bg-secondary" style="width: <?= floatval($profile['relevance']['mullai']) ?>%;"></div>
            </div>
          </div>

          <p class="small text-muted mt-3 mb-0">
            <i class="fas fa-info-circle text-teal me-1"></i> In <?= sanitize($profile['city']) ?>, high coastal marine relevance penalizes single-use plastic packaging, while high water stress prioritizes low water-consumption manufacturing.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    initEcoLensMap('ecolens-map-container', <?= floatval($profile['latitude']) ?>, <?= floatval($profile['longitude']) ?>, '<?= sanitize($profile['city']) ?>');
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
