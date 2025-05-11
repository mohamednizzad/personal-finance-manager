<?php require_once 'views/layouts/header.php'; ?>

<div class="row mb-4">
    <div class="col-md-6">
        <h1><i class="fas fa-plus-circle me-2"></i>Add Income Category</h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="index.php?page=income/categories" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Categories
        </a>
    </div>
</div>

<div class="card shadow">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-tag me-2"></i>Category Details</h5>
    </div>
    <div class="card-body">
        <form action="index.php?page=income/category/store" method="post">
            <div class="mb-3">
                <label for="name" class="form-label">Category Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Save Category
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>