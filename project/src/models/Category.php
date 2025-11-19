<?php

class Category
{
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll() {
        $sql = "SELECT * FROM categories ORDER BY name ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $sql = "SELECT * FROM categories WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function getBySlug($slug) {
        $sql = "SELECT * FROM categories WHERE slug = :slug LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':slug' => $slug]);
        return $stmt->fetch();
    }

    public function getBooksCount($categoryId = null) {
        if ($categoryId) {
            $sql = "SELECT COUNT(*) as count FROM books WHERE category_id = :category_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':category_id' => $categoryId]);
            $result = $stmt->fetch();
            return $result['count'];
        } else {
            $sql = "SELECT c.id, c.name, c.slug, c.icon, COUNT(b.id) as books_count 
                    FROM categories c 
                    LEFT JOIN books b ON c.id = b.category_id 
                    GROUP BY c.id 
                    ORDER BY c.name ASC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll();
        }
    }
}
