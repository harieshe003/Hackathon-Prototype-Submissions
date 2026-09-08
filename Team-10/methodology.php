<?php
// admin/methodology.php
$pageTitle = "Scoring Weights Settings — Admin Portal";
require_once __DIR__ . '/../includes/header.php';
requireAdmin();

$db = Database::getInstance();
$weights = $db->query("SELECT * FROM scoring_weights ORDER BY id ASC")->fetchAll();
?>

<section class="py-4 bg-surface border-bottom">
  <div class="container max-width-1000">
    <h1 class="h2 font-weight-bold text-navy mb-1">Scoring Weight Configuration</h1>
    <p class="text-muted small mb-0">View system-wide factor weighting percentages for the 9 lifecycle metrics.</p>
  </div>
</section>

<section class="py-5">
  <div class="container max-width-1000">
    <div class="ecolens-card p-4 p-md-5">
      <div class="table-responsive mb-4">
        <table class="table table-bordered align-middle">
          <thead class="table-light">
            <tr>
              <th>Factor Name</th>
              <th>Weight %</th>
              <th>Description</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($weights as $w): ?>
              <tr>
                <td class="font-weight-bold text-navy text-capitalize"><?= sanitize($w['factor_name']) ?></td>
                <td><span class="badge bg-success text-white font-weight-bold"><?= floatval($w['weight_percentage']) ?>%</span></td>
                <td><?= sanitize($w['description']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="alert alert-info small mb-0">
        <i class="fas fa-info-circle me-1"></i> Scoring weights sum to 100% and determine the deterministic global Eco Score formula.
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
