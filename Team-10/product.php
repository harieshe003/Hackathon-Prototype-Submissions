<?php
// product.php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/classes/ProductEngine.php';
require_once __DIR__ . '/classes/EcoSwapEngine.php';
require_once __DIR__ . '/classes/AIEngine.php';

$id = intval($_GET['id'] ?? 0);
$city = sanitize($_SESSION['user_location'] ?? 'Chennai');

$productEngine = new ProductEngine();
$analysis = $productEngine->analyzeProduct($id, $city);

if (!$analysis) {
    header("Location: " . APP_URL . "/products.php");
    exit;
}

$product = $analysis['product'];
$pageTitle = sanitize($product['name']) . " — EcoLens Score";
require_once __DIR__ . '/includes/header.php';

$ecoSwapEngine = new EcoSwapEngine();
$ecoSwapData = $ecoSwapEngine->recommendSwap($product, $analysis['factorScores'], 3);
?>

<section class="py-4 bg-surface border-bottom">
  <div class="container max-width-1200">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-2 small">
        <li class="breadcrumb-item"><a href="<?= APP_URL ?>/products.php">Catalog</a></li>
        <li class="breadcrumb-item"><a href="<?= APP_URL ?>/products.php?category=<?= urlencode($product['category']) ?>"><?= sanitize($product['category']) ?></a></li>
        <li class="breadcrumb-item active"><?= sanitize($product['name']) ?></li>
      </ol>
    </nav>

    <div class="row align-items-center g-4">
      <div class="col-md-3 text-center">
        <div class="p-3 bg-white rounded border shadow-sm position-relative">
          <img src="<?= APP_URL ?>/<?= sanitize($product['image']) ?>" alt="<?= sanitize($product['name']) ?>" class="img-fluid" style="max-height: 180px; object-fit: contain;">
        </div>
      </div>

      <div class="col-md-5">
        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
          <span class="badge bg-navy text-white"><?= sanitize($product['brand']) ?></span>
          <span class="badge bg-light text-dark border"><?= sanitize($product['material']) ?></span>
          <span class="badge bg-info text-dark" title="Scientific Safety Label">
            <i class="fas fa-shield-halved me-1"></i> <?= sanitize($analysis['safetyLabel']) ?>
          </span>
          <?php if (!empty($product['is_verified'])): ?>
            <span class="badge bg-success text-white"><i class="fas fa-check-circle me-1"></i> EcoLens Verified</span>
          <?php endif; ?>
        </div>

        <h1 class="h2 font-weight-bold text-navy mb-2"><?= sanitize($product['name']) ?></h1>
        <p class="text-muted small mb-3"><?= sanitize($product['description'] ?: 'Lifecycle environmental impact analysis across raw material, energy, water, transport, packaging, and end-of-life.') ?></p>

        <div class="d-flex gap-2">
          <a href="<?= APP_URL ?>/compare.php?add=<?= $product['id'] ?>" class="btn btn-sm btn-ecolens-primary">
            <i class="fas fa-scale-balanced me-1"></i> Add to Comparison
          </a>
          <a href="<?= APP_URL ?>/ecoswap.php?product_id=<?= $product['id'] ?>" class="btn btn-sm btn-ecolens-secondary">
            <i class="fas fa-arrows-rotate me-1"></i> EcoSwap Alternatives
          </a>
        </div>
      </div>

      <!-- Eco Score & Location Context Card -->
      <div class="col-md-4">
        <div class="ecolens-card p-4 text-center border-success">
          <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
            <span class="small text-muted font-weight-bold">BASE SCORE: <strong><?= $analysis['baseEcoScore']['score'] ?></strong>/100</span>
            <span class="badge bg-light text-dark border"><i class="fas fa-location-dot text-success me-1"></i> <?= sanitize($analysis['location']['city']) ?></span>
          </div>

          <div class="small text-muted font-weight-bold text-uppercase mb-1">Contextual Eco Score</div>
          <div class="display-4 font-weight-bold text-navy mb-1"><?= floatval($analysis['contextualEcoScore']['contextual_score']) ?></div>
          <div class="mb-2">
            <span class="badge badge-tag" style="background-color: <?= $analysis['contextualEcoScore']['color'] ?>; color: #fff;">
              <?= sanitize($analysis['contextualEcoScore']['classification']) ?>
            </span>
          </div>
          
          <div class="small text-muted">
            Data Confidence: <strong><?= sanitize($analysis['confidence']['level']) ?></strong>
            <span class="ms-1" title="Known: <?= $analysis['confidence']['knownFactors'] ?>, Estimated: <?= $analysis['confidence']['estimatedFactors'] ?>, Unknown: <?= $analysis['confidence']['unknownFactors'] ?>">
              <i class="fas fa-info-circle text-teal"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Location Context Rationale Banner -->
<section class="py-3 bg-light border-bottom">
  <div class="container max-width-1200">
    <div class="p-3 bg-white rounded border d-flex align-items-center gap-3">
      <div class="h3 text-teal mb-0"><i class="fas fa-earth-asia"></i></div>
      <div>
        <h6 class="font-weight-bold text-navy mb-1">Contextual Location Intelligence (<?= sanitize($analysis['location']['city']) ?>)</h6>
        <p class="small text-muted mb-0"><?= sanitize($analysis['contextualEcoScore']['explanation']) ?></p>
      </div>
    </div>
  </div>
</section>

<!-- Main Details Grid -->
<section class="py-5">
  <div class="container max-width-1200">
    <div class="row g-4 mb-5">
      <!-- 9 Environmental Factors Grid -->
      <div class="col-lg-7">
        <div class="ecolens-card p-4 h-100">
          <h4 class="font-weight-bold text-navy mb-4"><i class="fas fa-chart-pie text-success me-2"></i> 9 Environmental Factor Scores</h4>
          
          <div class="row g-3">
            <?php foreach ($analysis['factorScores'] as $key => $f): ?>
              <div class="col-6 col-md-4">
                <div class="factor-card text-center p-3 rounded border bg-light h-100">
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="badge <?= $f['status'] === 'KNOWN' ? 'bg-success' : ($f['status'] === 'ESTIMATED' ? 'bg-warning text-dark' : 'bg-secondary') ?> style="font-size: 0.65rem;">
                      <?= $f['status'] ?>
                    </span>
                    <span class="small text-muted" style="font-size: 0.65rem;" title="<?= sanitize($f['source']) ?>"><?= sanitize($f['source']) ?></span>
                  </div>
                  <div class="factor-title font-weight-bold text-navy small mb-1"><?= sanitize($f['name']) ?></div>
                  <div class="h5 font-weight-bold text-success mb-0"><?= floatval($f['score']) ?> <span class="small text-muted fs-6">/ 10</span></div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>

          <div class="mt-4 pt-3 border-top small text-muted">
            <i class="fas fa-shield-cat text-teal me-1"></i> Data Quality: <?= $analysis['confidence']['knownFactors'] ?> Known, <?= $analysis['confidence']['estimatedFactors'] ?> Estimated, <?= $analysis['confidence']['unknownFactors'] ?> Unknown parameters. Never hallucinated.
          </div>
        </div>
      </div>

      <!-- EcoLens 5 Thinai Impact Lenses -->
      <div class="col-lg-5">
        <div class="ecolens-card p-4 h-100 text-center">
          <h4 class="font-weight-bold text-navy mb-1">🌿 EcoLens 5 Thinai Ecological Lenses</h4>
          <p class="small text-muted mb-3">Contextual ecological interpretation framework</p>
          
          <div style="height: 250px; position: relative;" class="mb-3">
            <canvas id="ecolens-radar-canvas"></canvas>
          </div>

          <div class="text-start small">
            <?php foreach ($analysis['thinaiScores'] as $tKey => $t): ?>
              <div class="d-flex justify-content-between align-items-center py-1 border-bottom border-light">
                <span><?= $t['icon'] ?> <strong><?= $t['name'] ?></strong> (<?= $t['label'] ?>)</span>
                <span class="font-weight-bold text-navy"><?= $t['score'] ?> / 100</span>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- AI Summary Section -->
    <div class="ecolens-card p-4 p-md-5 mb-5 border-success">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="font-weight-bold text-navy mb-0"><i class="fas fa-robot text-success me-2"></i> AI Ecological Interpretation</h4>
        <button id="btn-generate-ai" class="btn btn-sm btn-ecolens-secondary">
          <i class="fas fa-sync-alt me-1"></i> Refresh Summary
        </button>
      </div>

      <div id="ai-loading" class="text-center py-4 d-none">
        <div class="spinner-border text-success mb-2" role="status"></div>
        <p class="small text-muted mb-0">Interpreting pre-calculated engine metrics...</p>
      </div>

      <div id="ai-content">
        <!-- Content dynamically injected -->
      </div>
    </div>

    <!-- EcoSwap Recommendations Section -->
    <?php if (!empty($ecoSwapData['recommendations'])): ?>
      <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h3 class="font-weight-bold text-navy mb-0"><i class="fas fa-arrows-rotate text-success me-2"></i> EcoSwap Recommendations</h3>
            <p class="small text-muted mb-0">Data-backed sustainable alternatives with score improvements</p>
          </div>
          <span class="badge bg-light text-dark border">Based on available environmental data</span>
        </div>

        <div class="row g-4">
          <?php foreach ($ecoSwapData['recommendations'] as $swap): $alt = $swap['alternative']; ?>
            <div class="col-md-4">
              <div class="ecolens-card p-4 h-100 border-success d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="badge bg-success text-white"><?= $swap['score_improvement'] ?></span>
                  <span class="font-weight-bold text-navy fs-5"><?= floatval($alt['overall_score']) ?> / 100</span>
                </div>
                <h5 class="font-weight-bold text-navy mb-1"><?= sanitize($alt['name']) ?></h5>
                <div class="small text-muted mb-2"><?= sanitize($alt['brand']) ?> &bull; <?= sanitize($alt['material']) ?></div>
                
                <ul class="small text-muted ps-3 mb-3">
                  <?php foreach ($swap['reasons'] as $r): ?>
                    <li><?= sanitize($r) ?></li>
                  <?php endforeach; ?>
                </ul>

                <div class="mt-auto pt-2 border-top border-light">
                  <a href="<?= APP_URL ?>/product.php?id=<?= $alt['id'] ?>" class="btn btn-sm btn-ecolens-primary w-100">View Alternative Details</a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Render Thinai Radar Chart
    const thinaiData = <?= json_encode($analysis['thinaiScores']) ?>;
    renderEcoLensRadarChart('ecolens-radar-canvas', thinaiData);

    // Fetch AI Interpretation
    fetchAiExplanation(<?= $product['id'] ?>);

    document.getElementById('btn-generate-ai').addEventListener('click', () => {
        fetchAiExplanation(<?= $product['id'] ?>);
    });
});

function fetchAiExplanation(productId) {
    const loading = document.getElementById('ai-loading');
    const content = document.getElementById('ai-content');
    loading.classList.remove('d-none');
    content.innerHTML = '';

    fetch(`${APP_URL}/api/ai/product-summary.php?id=${productId}`)
        .then(res => res.json())
        .then(data => {
            loading.classList.add('d-none');
            if (data.success && data.data) {
                const exp = data.data;
                content.innerHTML = `
                    <div class="p-3 bg-surface rounded mb-4">
                        <p class="text-dark mb-0 whitespace-pre-line">${exp.summary}</p>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-success"><i class="fas fa-check-circle me-1"></i> Identified Strengths</h6>
                            <ul class="small text-muted ps-3 mb-0">
                                ${exp.strengths.length ? exp.strengths.map(s => `<li class="mb-1">${s}</li>`).join('') : '<li>Balanced base performance</li>'}
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-warning"><i class="fas fa-triangle-exclamation me-1"></i> Key Weaknesses</h6>
                            <ul class="small text-muted ps-3 mb-0">
                                ${exp.weaknesses.length ? exp.weaknesses.map(w => `<li class="mb-1">${w}</li>`).join('') : '<li>No severe low factors</li>'}
                            </ul>
                        </div>
                    </div>
                    <div class="mt-3 text-end">
                        <span class="small text-muted"><i class="fas fa-info-circle me-1"></i> ${exp.disclaimer}</span>
                    </div>
                `;
            } else {
                content.innerHTML = `<div class="alert alert-warning">AI summary unavailable.</div>`;
            }
        })
        .catch(() => {
            loading.classList.add('d-none');
            content.innerHTML = `<div class="alert alert-warning">Could not load AI interpretation.</div>`;
        });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
