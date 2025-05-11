<?php
class Income {
    private $db;
    
    public function __construct() {
        $this->db = getDbConnection();
    }
    
    public function getIncomes($userId, $startDate = null, $endDate = null, $categoryId = null) {
        $sql = "SELECT i.*, ic.name as category_name 
                FROM incomes i 
                JOIN income_categories ic ON i.category_id = ic.id 
                WHERE i.user_id = ?";
        $params = [$userId];
        $types = "i";
        
        if ($startDate) {
            $sql .= " AND i.income_date >= ?";
            $params[] = $startDate;
            $types .= "s";
        }
        
        if ($endDate) {
            $sql .= " AND i.income_date <= ?";
            $params[] = $endDate;
            $types .= "s";
        }
        
        if ($categoryId) {
            $sql .= " AND i.category_id = ?";
            $params[] = $categoryId;
            $types .= "i";
        }
        
        $sql .= " ORDER BY i.income_date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $incomes = [];
        while ($row = $result->fetch_assoc()) {
            $incomes[] = $row;
        }
        
        return $incomes;
    }
    
    public function getIncomeById($userId, $incomeId) {
        $stmt = $this->db->prepare("SELECT i.*, ic.name as category_name 
                                   FROM incomes i 
                                   JOIN income_categories ic ON i.category_id = ic.id 
                                   WHERE i.id = ? AND i.user_id = ?");
        $stmt->bind_param("ii", $incomeId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return null;
        }
        
        return $result->fetch_assoc();
    }
    
    public function createIncome($userId, $categoryId, $amount, $description, $incomeDate) {
        $stmt = $this->db->prepare("INSERT INTO incomes (user_id, category_id, amount, description, income_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iidss", $userId, $categoryId, $amount, $description, $incomeDate);
        
        return $stmt->execute();
    }
    
    public function updateIncome($userId, $incomeId, $categoryId, $amount, $description, $incomeDate) {
        $stmt = $this->db->prepare("UPDATE incomes SET category_id = ?, amount = ?, description = ?, income_date = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("idssii", $categoryId, $amount, $description, $incomeDate, $incomeId, $userId);
        
        return $stmt->execute();
    }
    
    public function deleteIncome($userId, $incomeId) {
        $stmt = $this->db->prepare("DELETE FROM incomes WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $incomeId, $userId);
        
        return $stmt->execute();
    }
    
    public function getTotalIncomeByDateRange($userId, $startDate = null, $endDate = null, $categoryId = null) {
        $sql = "SELECT SUM(amount) as total FROM incomes WHERE user_id = ?";
        $params = [$userId];
        $types = "i";
        
        if ($startDate) {
            $sql .= " AND income_date >= ?";
            $params[] = $startDate;
            $types .= "s";
        }
        
        if ($endDate) {
            $sql .= " AND income_date <= ?";
            $params[] = $endDate;
            $types .= "s";
        }
        
        if ($categoryId) {
            $sql .= " AND category_id = ?";
            $params[] = $categoryId;
            $types .= "i";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['total'] ?? 0;
    }
    
    public function getRecentIncomes($userId, $limit = 5) {
        $stmt = $this->db->prepare("SELECT i.*, ic.name as category_name 
                                   FROM incomes i 
                                   JOIN income_categories ic ON i.category_id = ic.id 
                                   WHERE i.user_id = ? 
                                   ORDER BY i.income_date DESC 
                                   LIMIT ?");
        $stmt->bind_param("ii", $userId, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $incomes = [];
        while ($row = $result->fetch_assoc()) {
            $incomes[] = $row;
        }
        
        return $incomes;
    }
    
    public function getIncomeByCategory($userId, $startDate = null, $endDate = null) {
        $sql = "SELECT ic.id, ic.name, SUM(i.amount) as total 
                FROM incomes i 
                JOIN income_categories ic ON i.category_id = ic.id 
                WHERE i.user_id = ?";
        $params = [$userId];
        $types = "i";
        
        if ($startDate) {
            $sql .= " AND i.income_date >= ?";
            $params[] = $startDate;
            $types .= "s";
        }
        
        if ($endDate) {
            $sql .= " AND i.income_date <= ?";
            $params[] = $endDate;
            $types .= "s";
        }
        
        $sql .= " GROUP BY ic.id, ic.name ORDER BY total DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $categorySummary = [];
        while ($row = $result->fetch_assoc()) {
            $categorySummary[] = $row;
        }
        
        return $categorySummary;
    }
}
?>