<?php
class Router {
    private $routes = [
        // Auth routes
        'login' => ['controller' => 'AuthController', 'method' => 'login'],
        'register' => ['controller' => 'AuthController', 'method' => 'register'],
        'logout' => ['controller' => 'AuthController', 'method' => 'logout'],
        'reset-password' => ['controller' => 'AuthController', 'method' => 'resetPassword'],
        'update-profile' => ['controller' => 'AuthController', 'method' => 'updateProfile'],
        
        // Dashboard
        'dashboard' => ['controller' => 'DashboardController', 'method' => 'index'],
        
        // Income routes
        'incomes' => ['controller' => 'IncomeController', 'method' => 'index'],
        'income/create' => ['controller' => 'IncomeController', 'method' => 'create'],
        'income/store' => ['controller' => 'IncomeController', 'method' => 'store'],
        'income/edit' => ['controller' => 'IncomeController', 'method' => 'edit'],
        'income/update' => ['controller' => 'IncomeController', 'method' => 'update'],
        'income/delete' => ['controller' => 'IncomeController', 'method' => 'delete'],
        'income/categories' => ['controller' => 'IncomeCategoryController', 'method' => 'index'],
        'income/category/create' => ['controller' => 'IncomeCategoryController', 'method' => 'create'],
        'income/category/store' => ['controller' => 'IncomeCategoryController', 'method' => 'store'],
        'income/category/delete' => ['controller' => 'IncomeCategoryController', 'method' => 'delete'],
        
        // Expense routes
        'expenses' => ['controller' => 'ExpenseController', 'method' => 'index'],
        'expense/create' => ['controller' => 'ExpenseController', 'method' => 'create'],
        'expense/store' => ['controller' => 'ExpenseController', 'method' => 'store'],
        'expense/edit' => ['controller' => 'ExpenseController', 'method' => 'edit'],
        'expense/update' => ['controller' => 'ExpenseController', 'method' => 'update'],
        'expense/delete' => ['controller' => 'ExpenseController', 'method' => 'delete'],
        'expense/categories' => ['controller' => 'ExpenseCategoryController', 'method' => 'index'],
        'expense/category/create' => ['controller' => 'ExpenseCategoryController', 'method' => 'create'],
        'expense/category/store' => ['controller' => 'ExpenseCategoryController', 'method' => 'store'],
        'expense/category/delete' => ['controller' => 'ExpenseCategoryController', 'method' => 'delete'],
        
        // Report routes
        'reports' => ['controller' => 'ReportController', 'method' => 'index'],
        'reports/generate' => ['controller' => 'ReportController', 'method' => 'generate'],
        'reports/export' => ['controller' => 'ReportController', 'method' => 'export'],
        
        // Settings routes
        'settings' => ['controller' => 'SettingsController', 'method' => 'index'],
        'settings/update' => ['controller' => 'SettingsController', 'method' => 'update'],
    ];
    
    public function route() {
        // Get the current page from the URL
        $page = isset($_GET['page']) ? $_GET['page'] : 'login';
        
        // Check if the route exists
        if (isset($this->routes[$page])) {
            $controller = $this->routes[$page]['controller'];
            $method = $this->routes[$page]['method'];
            
            // Include the controller file
            require_once "controllers/{$controller}.php";
            
            // Create an instance of the controller and call the method
            $controllerInstance = new $controller();
            $controllerInstance->$method();
        } else {
            // Route not found, show 404 page
            require_once 'views/404.php';
        }
    }
}
?>