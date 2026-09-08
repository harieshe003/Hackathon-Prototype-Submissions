<?php
// business/audit.php
$pageTitle = "Sustainability Audit — Business Portal";
require_once __DIR__ . '/../includes/header.php';
requireBusiness();

$productId = intval($_GET['product_id'] ?? 1);
require_once __DIR__ . '/../classes/ProductEngine.php';
$productEngine = new ProductEngine();
$product = $productEngine->getProductById($productId);

$allProducts = $productEngine->searchProducts('', '', null, 50);
?>

<section class="py-4 bg-surface border-bottom">
  <div class="container max-width-1000">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h1 class="h2 font-weight-bold text-navy mb-1">Automated Sustainability Audit</h1>
        <p class="text-muted small mb-0">Identify top score improvement opportunities across your product portfolio.</p>
      </div>

      <form action="<?= APP_URL ?>/business/audit.php" method="GET">
        <select name="product_id" class="form-select" onchange="this.form.submit()">
          <?php foreach ($allProducts as $ap): ?>
            <option value="<?= $ap['id'] ?>" <?= $ap['id'] === $productId ? 'selected' : '' ?>>
              <?= sanitize($ap['name']) ?> (<?= floatval($ap['overall_score']) ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </form>
    </div>
  </div>
</section>

<section class="py-5">
  <div class="container max-width-1000">
    <?php if ($product): ?>
      <div id="audit-result-card" class="ecolens-card p-4 p-md-5 mb-4">
        <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-3">
          <img src="<?= APP_URL ?>/<?= sanitize($product['image']) ?>" style="height: 60px; object-fit: contain;">
          <div>
            <h4 class="font-weight-bold text-navy mb-0"><?= sanitize($product['name']) ?></h4>
            <div class="small text-muted">Current Eco Score: <strong><?= floatval($product['scores']['overall_score']) ?> / 100</strong> (<?= sanitize($product['scores']['rating']) ?>)</div>
          </div>
        </div>

        <div id="audit-loading" class="text-center py-4">
          <div class="spinner-border text-success mb-2" role="status"></div>
          <p class="small text-muted mb-0">Running multi-factor audit checks...</p>
        </div>

        <div id="audit-content" class="d-none">
          <!-- Dynamically populated via API -->
        </div>
      </div>
    <?php endif; ?>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    fetch(`${APP_URL}/api/business/audit.php?product_id=<?= $productId ?>`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('audit-loading').classList.add('d-none');
            const content = document.getElementById('audit-content');
            content.classList.remove('d-none');

            if (data.success && data.audit) {
                const a = data.audit;
                content.innerHTML = `
                    <div class="alert alert-success border-success mb-4 p-4">
                        <div class="badge bg-success text-white mb-2">TOP IMPROVEMENT OPPORTUNITY</div>
                        <h4 class="font-weight-bold text-navy mb-2">${a.top_opportunity}</h4>
                        <p class="text-dark mb-2">${a.suggested_action}</p>
                        <div class="font-weight-bold text-success">
                            Estimated Impact: <strong>+${a.estimated_impact_points} Score Points</strong> (Projected Score: ${a.projected_score} / 100)
                        </div>
                    </div>
                    <div class="text-end">
                        <a href="${APP_URL}/business/simulator.php?product_id=<?= $productId ?>" class="btn btn-ecolens-primary">
                            <i class="fas fa-sliders me-1"></i> Open in Simulator
                        </a>
                    </div>
                `;
            }
        });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
