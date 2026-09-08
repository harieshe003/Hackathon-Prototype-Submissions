<?php
// admin/users.php
$pageTitle = "User Management — Admin Portal";
require_once __DIR__ . '/../includes/header.php';
requireAdmin();

$db = Database::getInstance();
$users = $db->query("SELECT * FROM users ORDER BY id DESC")->fetchAll();
?>

<section class="py-4 bg-surface border-bottom">
  <div class="container max-width-1200">
    <h1 class="h2 font-weight-bold text-navy mb-1">User Management</h1>
    <p class="text-muted small mb-0">Manage registered consumer, business, and administrator accounts.</p>
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
              <th>Name</th>
              <th>Email</th>
              <th>Role</th>
              <th>Location</th>
              <th>Registered Date</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $u): ?>
              <tr>
                <td>#<?= $u['id'] ?></td>
                <td class="font-weight-bold text-navy"><?= sanitize($u['name']) ?></td>
                <td><?= sanitize($u['email']) ?></td>
                <td><span class="badge bg-<?= $u['role'] === 'admin' ? 'danger' : ($u['role'] === 'business' ? 'teal' : 'secondary') ?>"><?= sanitize($u['role']) ?></span></td>
                <td><?= sanitize($u['location_city']) ?></td>
                <td><?= sanitize($u['created_at']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
