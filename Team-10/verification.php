<?php
// business/verification.php
$pageTitle = "EcoLens Verified Application — Business Portal";
require_once __DIR__ . '/../includes/header.php';
requireBusiness();

$productId = intval($_GET['product_id'] ?? 1);
require_once __DIR__ . '/../classes/ProductEngine.php';
$productEngine = new ProductEngine();
$product = $productEngine->getProductById($productId);
?>

<section class="py-4 bg-surface border-bottom">
  <div class="container max-width-800">
    <h1 class="h2 font-weight-bold text-navy mb-1">🌱 EcoLens Verification Portal</h1>
    <p class="text-muted small mb-0">Submit ISO 14040 Life Cycle Assessment documentation or third-party lab evidence for official verification.</p>
  </div>
</section>

<section class="py-5">
  <div class="container max-width-800">
    <div class="ecolens-card p-4 p-md-5">
      <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-3">
        <img src="<?= APP_URL ?>/<?= sanitize($product['image']) ?>" style="height: 60px; object-fit: contain;">
        <div>
          <h5 class="font-weight-bold text-navy mb-0"><?= sanitize($product['name']) ?></h5>
          <div class="small text-muted"><?= sanitize($product['brand']) ?> &bull; Current Confidence: <strong><?= sanitize($product['data_confidence']) ?></strong></div>
        </div>
      </div>

      <div id="ver-alert" class="alert alert-success d-none mb-3"></div>

      <form action="<?= APP_URL ?>/api/verification/submit.php" method="POST" id="ver-form">
        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

        <div class="mb-3">
          <label class="form-label font-weight-bold small text-navy">Document Name / LCA Audit Title *</label>
          <input type="text" name="document_name" class="form-control" placeholder="ISO 14040 Life Cycle Assessment Report 2026" required>
        </div>

        <div class="mb-3">
          <label class="form-label font-weight-bold small text-navy">Public Audit / Evidence URL</label>
          <input type="url" name="source_url" class="form-control" placeholder="https://ecobrand.com/lca-report.pdf">
        </div>

        <div class="mb-4">
          <label class="form-label font-weight-bold small text-navy">Auditor Notes & Methodology Disclosures</label>
          <textarea name="notes" class="form-control" rows="4" placeholder="Detail third-party lab testing procedures, resin grade certifications, or energy audit logs..."></textarea>
        </div>

        <button type="submit" class="btn btn-ecolens-primary py-2 px-4">
          <i class="fas fa-paper-plane me-1"></i> Submit Verification Application
        </button>
      </form>
    </div>
  </div>
</section>

<script>
document.getElementById('ver-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const alertDiv = document.getElementById('ver-alert');
    const formData = new FormData(this);

    fetch(this.action, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alertDiv.innerText = data.message;
            alertDiv.classList.remove('d-none');
            this.reset();
        } else {
            alert(data.error || 'Submission failed');
        }
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
