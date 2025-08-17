<?php
$pageTitle = 'Quản lý người dùng';
$currentPage = 'users';
ob_start();
?>

<table class="table table-bordered table-hover align-middle">
  <thead class="table-light">
    <tr>
      <th style="width:80px">ID</th>
      <th>Tên</th>
      <th>Email</th>
      <th style="width:120px" class="text-center">Vai trò</th>
      <th style="width:120px" class="text-center">Hành động</th>
    </tr>
  </thead>
  <tbody>
    <?php if (!empty($users)):
      foreach ($users as $user): ?>
        <tr>
          <td><?= $user['id'] ?></td>
          <td><?= htmlspecialchars($user['name']) ?></td>
          <td><?= htmlspecialchars($user['email']) ?></td>
          <td class="text-center">
            <span
              class="badge <?= ($user['role'] === 'admin' ? 'bg-primary' : 'bg-secondary') ?>"><?= htmlspecialchars($user['role'] ?? 'user') ?></span>
          </td>
          <td class="text-center">
            <a href="?act=user-edit&id=<?= $user['id'] ?>" class="btn btn-warning btn-sm"><i
                class="bi bi-pencil-square"></i></a>
          </td>
        </tr>
      <?php endforeach; else: ?>
      <tr>
        <td colspan="3" class="text-center text-muted">Chưa có người dùng nào!</td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>
<?php
$mainContent = ob_get_clean();
require_once __DIR__ . '/../layout/layout.php';
?>