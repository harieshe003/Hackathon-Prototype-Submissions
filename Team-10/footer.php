<?php
// includes/footer.php
?>
</main>

<footer>
  <div class="container max-width-1200">
    <div class="row g-4 mb-4">
      <div class="col-lg-4 col-md-6">
        <img src="<?= APP_URL ?>/assets/images/logo.png" alt="EcoLens Logo" style="height: 52px; border-radius: 8px;" class="mb-3">
        <p class="text-light opacity-75 small">
          Five Lenses. One Sustainable Choice.<br>
          See Beyond the Product. Choose Better.
        </p>
        <p class="small text-light opacity-50">
          EcoLens score is calculated deterministically across 9 lifecycle metrics. EcoLens 5 Thinai landscapes serve as an interpretive framework.
        </p>
      </div>

      <div class="col-lg-2 col-md-6">
        <h6 class="text-white font-weight-bold mb-3">Explore</h6>
        <ul class="list-unstyled small">
          <li class="mb-2"><a href="<?= APP_URL ?>/products.php">Product Catalog</a></li>
          <li class="mb-2"><a href="<?= APP_URL ?>/compare.php">Product Comparison</a></li>
          <li class="mb-2"><a href="<?= APP_URL ?>/ecoswap.php">EcoSwap Swaps</a></li>
          <li class="mb-2"><a href="<?= APP_URL ?>/location.php">Location Intelligence</a></li>
        </ul>
      </div>

      <div class="col-lg-3 col-md-6">
        <h6 class="text-white font-weight-bold mb-3">Platform</h6>
        <ul class="list-unstyled small">
          <li class="mb-2"><a href="<?= APP_URL ?>/methodology.php">Scoring Methodology</a></li>
          <li class="mb-2"><a href="<?= APP_URL ?>/business/dashboard.php">Business Sustainability</a></li>
          <li class="mb-2"><a href="<?= APP_URL ?>/business/simulator.php">Impact Simulator</a></li>
          <li class="mb-2"><a href="<?= APP_URL ?>/login.php">Account Login</a></li>
        </ul>
      </div>

      <div class="col-lg-3 col-md-6">
        <h6 class="text-white font-weight-bold mb-3">Trust & Transparency</h6>
        <p class="small text-light opacity-75">
          EcoLens verified products undergo rigorous ISO 14040 data audits and supply chain disclosures.
        </p>
        <div class="d-flex gap-3 text-white fs-5 mt-2">
          <a href="#" class="text-white opacity-75"><i class="fab fa-twitter"></i></a>
          <a href="#" class="text-white opacity-75"><i class="fab fa-linkedin"></i></a>
          <a href="#" class="text-white opacity-75"><i class="fab fa-github"></i></a>
        </div>
      </div>
    </div>

    <div class="border-top border-secondary pt-3 d-flex flex-column flex-md-row justify-content-between align-items-center small opacity-75">
      <p class="mb-0">&copy; <?= date('Y') ?> EcoLens Platform. All rights reserved.</p>
      <div class="d-flex gap-3">
        <a href="<?= APP_URL ?>/methodology.php" class="text-white">Privacy Policy</a>
        <a href="<?= APP_URL ?>/methodology.php" class="text-white">Terms of Service</a>
      </div>
    </div>
  </div>
</footer>

<!-- Location Permission Modal -->
<div class="modal fade" id="locationPermissionModal" tabindex="-1" aria-labelledby="locationPermissionModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title font-weight-bold text-navy" id="locationPermissionModalLabel">
          <i class="fas fa-location-crosshairs text-success me-2"></i> Enable Location Intelligence?
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="localStorage.setItem('ecolens_loc_prompted', 'true')"></button>
      </div>
      <div class="modal-body text-muted small">
        <p class="mb-3 text-dark">
          EcoLens uses your location context to evaluate local ecosystem stress—such as coastal marine plastic vulnerability (<em>Neithal</em>) and municipal water scarcity (<em>Palai</em>).
        </p>
        <div class="p-3 bg-surface rounded border mb-2 text-dark">
          <strong>Why grant location access?</strong><br>
          Product packaging and water consumption scores will be accurately adjusted for your local ecosystem.
        </div>
      </div>
      <div class="modal-footer border-0 pt-0 justify-content-between">
        <a href="<?= APP_URL ?>/location.php" class="btn btn-sm btn-link text-muted text-decoration-none" data-bs-dismiss="modal" onclick="localStorage.setItem('ecolens_loc_prompted', 'true')">Choose City Manually</a>
        <button type="button" class="btn btn-sm btn-ecolens-primary btn-allow-location">
          <i class="fas fa-location-arrow me-1"></i> Allow Location Access
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/bootstrap.bundle.min.js"></script>
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Leaflet.js CDN -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- EcoLens Application JS -->
<script src="<?= APP_URL ?>/assets/js/app.js"></script>
<script src="<?= APP_URL ?>/assets/js/charts.js"></script>
<script src="<?= APP_URL ?>/assets/js/map.js"></script>
</body>
</html>
