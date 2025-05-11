<?php
class ExpenseCategory {
    private $db;
    
    public function __construct() {
        $this->db = getDbConnection();
    }
    
    public function getCategoriesByUserId($userId) {
        $stmt = $this->db->prepare("SELECT * FROM expense_categories WHERE user_id = ? ORDER BY name");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
        
        return $categories;
    }
    
    public function getCategoryById($userId, $categoryId) {
        $stmt = $this->db->prepare("SELECT * FROM expense_categories WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $categoryId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return null;
        }
        
        return $result->fetch_assoc();
    }
    
    public function getCategoryByName($userId, $name) {
        $stmt = $this->db->prepare("SELECT * FROM expense_categories WHERE name = ? AND user_id = ?");
        $stmt->bind_param("si", $name, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return null;
        }
        
        return $result->fetch_assoc();
    }
    
    public function createCategory($userId, $name) {
        $stmt = $this->db->prepare("INSERT INTO expense_categories (user_id, name) VALUES (?, ?)");
        $stmt->bind_param("is", $userId, $name);
        
        return $stmt->execute();
    }
    
    public function updateCategory($userId, $categoryId, $name) {
        $stmt = $this->db->prepare("UPDATE expense_categories SET name = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sii", $name, $categoryId, $userId);
        
        return $stmt->execute();
    }
    
    public function deleteCategory($userId, $categoryId) {
        $stmt = $this->db->prepare("DELETE FROM expense_categories WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $categoryId, $userId);
        
        return $stmt->execute();
    }
    
    public function isCategoryInUse($userId, $categoryId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM expenses WHERE category_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $categoryId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['count'] > 0;
    }
}
?>