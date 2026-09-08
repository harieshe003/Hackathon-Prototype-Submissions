<?php
// admin/businesses.php
$pageTitle = "Business Management — Admin Portal";
require_once __DIR__ . '/../includes/header.php';
requireAdmin();

$db = Database::getInstance();
$businesses = $db->query("
    SELECT b.*, u.name as owner_name, u.email as owner_email
    FROM businesses b
    JOIN users u ON b.user_id = u.id
    ORDER BY b.id DESC
")->fetchAll();
?>

<section class="py-4 bg-surface border-bottom">
  <div class="container max-width-1200">
    <h1 class="h2 font-weight-bold text-navy mb-1">Business Producer Accounts</h1>
    <p class="text-muted small mb-0">Overview of registered manufacturer and brand producer accounts.</p>
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
              <th>Company Name</th>
              <th>Owner Name</th>
              <th>Email</th>
              <th>Registration No</th>
              <th>Verified Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($businesses as $b): ?>
              <tr>
                <td>#<?= $b['id'] ?></td>
                <td class="font-weight-bold text-navy"><?= sanitize($b['company_name']) ?></td>
                <td><?= sanitize($b['owner_name']) ?></td>
                <td><?= sanitize($b['contact_email']) ?></td>
                <td><?= sanitize($b['registration_no'] ?: 'N/A') ?></td>
                <td><?= !empty($b['is_verified']) ? '<span class="badge bg-success">Verified Producer</span>' : '<span class="badge bg-secondary">Unverified</span>' ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
