<?php
// admin/verifications.php
$pageTitle = "Verification Management — Admin Portal";
require_once __DIR__ . '/../includes/header.php';
requireAdmin();

$db = Database::getInstance();

// Handle approval / rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $reqId = intval($_POST['request_id']);
    $action = $_POST['action']; // 'approve' or 'reject'
    $status = ($action === 'approve') ? 'Approved' : 'Rejected';

    $stmtReq = $db->prepare("UPDATE verification_requests SET status = ?, reviewed_at = CURRENT_TIMESTAMP WHERE id = ?");
    $stmtReq->execute([$status, $reqId]);

    if ($action === 'approve') {
        $stmtGetProd = $db->prepare("SELECT product_id FROM verification_requests WHERE id = ? LIMIT 1");
        $stmtGetProd->execute([$reqId]);
        $prodId = $stmtGetProd->fetchColumn();

        if ($prodId) {
            $stmtUpd = $db->prepare("UPDATE products SET is_verified = 1 WHERE id = ?");
            $stmtUpd->execute([$prodId]);
        }
    }
}

$verifications = $db->query("
    SELECT v.*, p.name as product_name, p.id as product_id, b.company_name, e.document_name, e.source_url
    FROM verification_requests v
    JOIN products p ON v.product_id = p.id
    JOIN businesses b ON v.business_id = b.id
    LEFT JOIN verification_evidence e ON v.id = e.verification_id
    ORDER BY v.id DESC
")->fetchAll();
?>

<section class="py-4 bg-surface border-bottom">
  <div class="container max-width-1200">
    <h1 class="h2 font-weight-bold text-navy mb-1">Verification Queue</h1>
    <p class="text-muted small mb-0">Review manufacturer evidence submissions and issue official EcoLens Verified badges.</p>
  </div>
</section>

<section class="py-5">
  <div class="container max-width-1200">
    <div class="ecolens-card p-4">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>Product Name</th>
              <th>Business Name</th>
              <th>Document Name</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($verifications as $v): ?>
              <tr>
                <td>#<?= $v['id'] ?></td>
                <td class="font-weight-bold text-navy">
                  <a href="<?= APP_URL ?>/product.php?id=<?= $v['product_id'] ?>" target="_blank"><?= sanitize($v['product_name']) ?></a>
                </td>
                <td><?= sanitize($v['company_name']) ?></td>
                <td>
                  <?= sanitize($v['document_name'] ?: 'LCA Audit Report') ?>
                  <?php if (!empty($v['source_url'])): ?>
                    <br><a href="<?= sanitize($v['source_url']) ?>" target="_blank" class="small text-teal"><i class="fas fa-external-link me-1"></i> View Evidence</a>
                  <?php endif; ?>
                </td>
                <td>
                  <span class="badge bg-<?= $v['status'] === 'Approved' ? 'success' : ($v['status'] === 'Pending' ? 'warning' : 'danger') ?>">
                    <?= sanitize($v['status']) ?>
                  </span>
                </td>
                <td>
                  <?php if ($v['status'] === 'Pending'): ?>
                    <form action="<?= APP_URL ?>/admin/verifications.php" method="POST" class="d-inline-flex gap-1">
                      <input type="hidden" name="request_id" value="<?= $v['id'] ?>">
                      <button type="submit" name="action" value="approve" class="btn btn-sm btn-success"><i class="fas fa-check"></i> Approve</button>
                      <button type="submit" name="action" value="reject" class="btn btn-sm btn-outline-danger"><i class="fas fa-xmark"></i> Reject</button>
                    </form>
                  <?php else: ?>
                    <span class="small text-muted">Reviewed</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
