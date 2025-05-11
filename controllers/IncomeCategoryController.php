<?php
require_once 'models/IncomeCategory.php';

class IncomeCategoryController {
    private $categoryModel;
    
    public function __construct() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        
        $this->categoryModel = new IncomeCategory();
    }
    
    public function index() {
        $userId = $_SESSION['user_id'];
        
        // Get all categories
        $categories = $this->categoryModel->getCategoriesByUserId($userId);
        
        // Load categories view
        require_once 'views/income/categories.php';
    }
    
    public function create() {
        // Load create category view
        require_once 'views/income/category_create.php';
    }
    
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=income/categories');
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $name = $_POST['name'] ?? '';
        
        // Validate input
        if (empty($name)) {
            $_SESSION['error'] = "Category name is required";
            header('Location: index.php?page=income/category/create');
            exit;
        }
        
        // Check if category already exists
        if ($this->categoryModel->getCategoryByName($userId, $name)) {
            $_SESSION['error'] = "Category already exists";
            header('Location: index.php?page=income/category/create');
            exit;
        }
        
        // Create category
        $success = $this->categoryModel->createCategory($userId, $name);
        
        if ($success) {
            $_SESSION['success'] = "Category added successfully";
            header('Location: index.php?page=income/categories');
        } else {
            $_SESSION['error'] = "Failed to add category";
            header('Location: index.php?page=income/category/create');
        }
        exit;
    }
    
    public function delete() {
        $userId = $_SESSION['user_id'];
        $categoryId = $_GET['id'] ?? null;
        
        if (!$categoryId) {
            header('Location: index.php?page=income/categories');
            exit;
        }
        
        // Check if category is in use
        if ($this->categoryModel->isCategoryInUse($userId, $categoryId)) {
            $_SESSION['error'] = "Cannot delete category that is in use";
            header('Location: index.php?page=income/categories');
            exit;
        }
        
        // Delete category
        $success = $this->categoryModel->deleteCategory($userId, $categoryId);
        
        if ($success) {
            $_SESSION['success'] = "Category deleted successfully";
        } else {
            $_SESSION['error'] = "Failed to delete category";
        }
        
        header('Location: index.php?page=income/categories');
        exit;
    }
}
?>