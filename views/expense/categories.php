<?php require_once 'views/layouts/header.php'; ?>

<div class="row mb-4">
    <div class="col-md-6">
        <h1><i class="fas fa-tags me-2"></i>Expense Categories</h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="index.php?page=expense/category/create" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>Add Category
        </a>
        <a href="index.php?page=expenses" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Expenses
        </a>
    </div>
</div>

<div class="card shadow">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Category List</h5>
    </div>
    <div class="card-body">
        <?php if (empty($categories)): ?>
            <p class="text-muted text-center">No categories found.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Created At</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($category['name']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($category['created_at'])); ?></td>
                                <td class="text-center">
                                    <a href="index.php?page=expense/category/delete&id=<?php echo $category['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this category?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>