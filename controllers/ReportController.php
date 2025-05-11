<?php
require_once 'models/Income.php';
require_once 'models/Expense.php';
require_once 'models/IncomeCategory.php';
require_once 'models/ExpenseCategory.php';

class ReportController {
    private $incomeModel;
    private $expenseModel;
    private $incomeCategoryModel;
    private $expenseCategoryModel;
    
    public function __construct() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        
        $this->incomeModel = new Income();
        $this->expenseModel = new Expense();
        $this->incomeCategoryModel = new IncomeCategory();
        $this->expenseCategoryModel = new ExpenseCategory();
    }
    
    public function index() {
        $userId = $_SESSION['user_id'];
        
        // Get all income categories for filter
        $incomeCategories = $this->incomeCategoryModel->getCategoriesByUserId($userId);
        
        // Get all expense categories for filter
        $expenseCategories = $this->expenseCategoryModel->getCategoriesByUserId($userId);
        
        // Load reports view
        require_once 'views/report/index.php';
    }
    
    public function generate() {
        $userId = $_SESSION['user_id'];
        
        // Get filter parameters
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-t');
        $incomeCategoryId = $_GET['income_category_id'] ?? null;
        $expenseCategoryId = $_GET['expense_category_id'] ?? null;
        $reportType = $_GET['report_type'] ?? 'all'; // all, income, expense
        
        // Initialize data arrays
        $incomeData = [];
        $expenseData = [];
        $summary = [
            'total_income' => 0,
            'total_expense' => 0,
            'balance' => 0
        ];
        
        // Get income data if requested
        if ($reportType === 'all' || $reportType === 'income') {
            $incomeData = $this->incomeModel->getIncomes($userId, $startDate, $endDate, $incomeCategoryId);
            $summary['total_income'] = $this->incomeModel->getTotalIncomeByDateRange($userId, $startDate, $endDate, $incomeCategoryId);
        }
        
        // Get expense data if requested
        if ($reportType === 'all' || $reportType === 'expense') {
            $expenseData = $this->expenseModel->getExpenses($userId, $startDate, $endDate, $expenseCategoryId);
            $summary['total_expense'] = $this->expenseModel->getTotalExpenseByDateRange($userId, $startDate, $endDate, $expenseCategoryId);
        }
        
        // Calculate balance
        $summary['balance'] = $summary['total_income'] - $summary['total_expense'];
        
        // Get category summaries
        $incomeCategorySummary = [];
        $expenseCategorySummary = [];
        
        if ($reportType === 'all' || $reportType === 'income') {
            $incomeCategorySummary = $this->incomeModel->getIncomeByCategory($userId, $startDate, $endDate);
        }
        
        if ($reportType === 'all' || $reportType === 'expense') {
            $expenseCategorySummary = $this->expenseModel->getExpenseByCategory($userId, $startDate, $endDate);
        }
        
        // Load report results view
        require_once 'views/report/results.php';
    }
    
    public function export() {
        $userId = $_SESSION['user_id'];
        
        // Get filter parameters
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-t');
        $incomeCategoryId = $_GET['income_category_id'] ?? null;
        $expenseCategoryId = $_GET['expense_category_id'] ?? null;
        $reportType = $_GET['report_type'] ?? 'all'; // all, income, expense
        
        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="finance_report_' . date('Y-m-d') . '.csv"');
        
        // Create a file pointer connected to the output stream
        $output = fopen('php://output', 'w');
        
        // Add BOM to fix UTF-8 in Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Get data based on report type
        if ($reportType === 'all' || $reportType === 'income') {
            // Add income header
            fputcsv($output, ['INCOME REPORT']);
            fputcsv($output, ['Date', 'Category', 'Description', 'Amount']);
            
            // Get income data
            $incomeData = $this->incomeModel->getIncomes($userId, $startDate, $endDate, $incomeCategoryId);
            
            // Add income data
            foreach ($incomeData as $income) {
                fputcsv($output, [
                    $income['income_date'],
                    $income['category_name'],
                    $income['description'],
                    $income['amount']
                ]);
            }
            
            // Add income total
            $totalIncome = $this->incomeModel->getTotalIncomeByDateRange($userId, $startDate, $endDate, $incomeCategoryId);
            fputcsv($output, ['', '', 'Total Income', $totalIncome]);
            
            // Add empty row if both reports are included
            if ($reportType === 'all') {
                fputcsv($output, []);
            }
        }
        
        if ($reportType === 'all' || $reportType === 'expense') {
            // Add expense header
            fputcsv($output, ['EXPENSE REPORT']);
            fputcsv($output, ['Date', 'Category', 'Description', 'Amount']);
            
            // Get expense data
            $expenseData = $this->expenseModel->getExpenses($userId, $startDate, $endDate, $expenseCategoryId);
            
            // Add expense data
            foreach ($expenseData as $expense) {
                fputcsv($output, [
                    $expense['expense_date'],
                    $expense['category_name'],
                    $expense['description'],
                    $expense['amount']
                ]);
            }
            
            // Add expense total
            $totalExpense = $this->expenseModel->getTotalExpenseByDateRange($userId, $startDate, $endDate, $expenseCategoryId);
            fputcsv($output, ['', '', 'Total Expense', $totalExpense]);
        }
        
        // Add summary if both reports are included
        if ($reportType === 'all') {
            fputcsv($output, []);
            fputcsv($output, ['SUMMARY']);
            
            $totalIncome = $this->incomeModel->getTotalIncomeByDateRange($userId, $startDate, $endDate, $incomeCategoryId);
            $totalExpense = $this->expenseModel->getTotalExpenseByDateRange($userId, $startDate, $endDate, $expenseCategoryId);
            $balance = $totalIncome - $totalExpense;
            
            fputcsv($output, ['Total Income', $totalIncome]);
            fputcsv($output, ['Total Expense', $totalExpense]);
            fputcsv($output, ['Balance', $balance]);
        }
        
        // Close the file pointer
        fclose($output);
        exit;
    }
}
?>