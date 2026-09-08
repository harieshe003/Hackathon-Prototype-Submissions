<?php
// business/add-product.php
$pageTitle = "Add Product — Business Portal";
require_once __DIR__ . '/../includes/header.php';
requireBusiness();

$db = Database::getInstance();
$categories = $db->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
?>

<section class="py-4 bg-surface border-bottom">
  <div class="container max-width-1000">
    <h1 class="h2 font-weight-bold text-navy mb-1">Add Product & Lifecycle Data</h1>
    <p class="text-muted small mb-0">Enter technical parameters across raw materials, manufacturing, packaging, and end-of-life.</p>
  </div>
</section>

<section class="py-5">
  <div class="container max-width-1000">
    <div class="ecolens-card p-4 p-md-5">
      <form action="<?= APP_URL ?>/api/products/create.php" method="POST" id="add-product-form">
        <h5 class="font-weight-bold text-navy mb-3 border-bottom pb-2">1. General Product Details</h5>
        
        <div class="row g-3 mb-4">
          <div class="col-md-6">
            <label class="form-label font-weight-bold small text-navy">Product Name *</label>
            <input type="text" name="name" class="form-control" placeholder="Stainless Steel Tumbler 750ml" required>
          </div>
          <div class="col-md-6">
            <label class="form-label font-weight-bold small text-navy">Brand Name</label>
            <input type="text" name="brand" class="form-control" placeholder="EcoHydro">
          </div>
          <div class="col-md-6">
            <label class="form-label font-weight-bold small text-navy">Category</label>
            <select name="category" class="form-select">
              <?php foreach ($categories as $cat): ?>
                <option value="<?= sanitize($cat['name']) ?>"><?= sanitize($cat['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label font-weight-bold small text-navy">Primary Material</label>
            <input type="text" name="material" class="form-control" placeholder="18/8 Stainless Steel">
          </div>
          <div class="col-md-6">
            <label class="form-label font-weight-bold small text-navy">Weight (grams)</label>
            <input type="number" step="0.1" name="weight" class="form-control" placeholder="350">
          </div>
          <div class="col-md-6">
            <label class="form-label font-weight-bold small text-navy">Manufacturing Location</label>
            <input type="text" name="manufacturing_location" class="form-control" placeholder="Coimbatore, India">
          </div>
        </div>

        <h5 class="font-weight-bold text-navy mb-3 border-bottom pb-2">2. Packaging & Logistics</h5>
        
        <div class="row g-3 mb-4">
          <div class="col-md-6">
            <label class="form-label font-weight-bold small text-navy">Packaging Material</label>
            <input type="text" name="packaging_material" class="form-control" placeholder="Recycled Cardboard Box">
          </div>
          <div class="col-md-6">
            <label class="form-label font-weight-bold small text-navy">Packaging Weight (grams)</label>
            <input type="number" step="0.1" name="packaging_weight" class="form-control" placeholder="25">
          </div>
          <div class="col-md-6">
            <label class="form-label font-weight-bold small text-navy">Transport Distance (km)</label>
            <input type="number" step="1" name="transport_distance" class="form-control" placeholder="220">
          </div>
          <div class="col-md-6">
            <label class="form-label font-weight-bold small text-navy">Carbon Footprint (kg CO2e)</label>
            <input type="number" step="0.01" name="carbon_footprint" class="form-control" placeholder="2.10">
          </div>
        </div>

        <h5 class="font-weight-bold text-navy mb-3 border-bottom pb-2">3. Durability & End-of-Life</h5>
        
        <div class="row g-3 mb-4">
          <div class="col-md-4">
            <div class="form-check mt-4">
              <input class="form-check-input" type="checkbox" name="reusable" value="1" id="reusable" checked>
              <label class="form-check-label font-weight-bold small text-navy" for="reusable">Reusable Architecture?</label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-check mt-4">
              <input class="form-check-input" type="checkbox" name="recyclable" value="1" id="recyclable" checked>
              <label class="form-check-label font-weight-bold small text-navy" for="recyclable">Post-Consumer Recyclable?</label>
            </div>
          </div>
          <div class="col-md-4">
            <label class="form-label font-weight-bold small text-navy">Expected Service Lifespan</label>
            <input type="text" name="lifespan" class="form-control" placeholder="10+ years">
          </div>
          <div class="col-md-6">
            <label class="form-label font-weight-bold small text-navy">End-of-Life Method</label>
            <input type="text" name="end_of_life" class="form-control" placeholder="Recycled Scrap Metal">
          </div>
          <div class="col-md-6">
            <label class="form-label font-weight-bold small text-navy">Data Confidence Level</label>
            <select name="data_confidence" class="form-select">
              <option value="High">High Confidence (3rd Party LCA Audit)</option>
              <option value="Medium" selected>Medium Confidence (Manufacturer Disclosure)</option>
              <option value="Low">Low Confidence (Estimated)</option>
            </select>
          </div>
        </div>

        <button type="submit" class="btn btn-ecolens-primary py-2 px-4">
          <i class="fas fa-check-circle me-1"></i> Submit Product & Calculate Score
        </button>
      </form>
    </div>
  </div>
</section>

<script>
document.getElementById('add-product-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch(this.action, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success && data.data) {
            window.location.href = '<?= APP_URL ?>/product.php?id=' + data.data.id;
        } else {
            alert(data.error || 'Product creation failed.');
        }
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
