<?php
require_once __DIR__ . '/../models/Book.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/UserProgress.php';

class BookController
{
    private $bookModel;
    private $categoryModel;
    private $progressModel;

    public function __construct($pdo) {
        $this->bookModel = new Book($pdo);
        $this->categoryModel = new Category($pdo);
        $this->progressModel = new UserProgress($pdo);
    }

    public function getAllBooks($categoryId = null, $limit = null, $offset = 0) {
        if ($categoryId) {
            return $this->bookModel->getByCategory($categoryId, $limit, $offset);
        }
        return $this->bookModel->getAll($limit, $offset);
    }

    public function getBook($bookId, $userId = null) {
        $book = $this->bookModel->getById($bookId);
        
        if (!$book) {
            return null;
        }

        if ($userId) {
            $progress = $this->progressModel->getUserProgress($userId, $bookId);
            $book['user_progress'] = $progress ? $progress : null;
        }

        return $book;
    }

    public function searchBooks($query, $limit = null, $offset = 0) {
        return $this->bookModel->search($query, $limit, $offset);
    }

    public function getFeaturedBooks($limit = 6) {
        return $this->bookModel->getFeatured($limit);
    }

    public function getCategories() {
        return $this->categoryModel->getBooksCount();
    }

    public function trackView($bookId, $userId = null) {
        $this->bookModel->incrementViews($bookId);

        if ($userId) {
            $existing = $this->progressModel->getUserProgress($userId, $bookId);
            if (!$existing) {
                $this->progressModel->updateProgress($userId, $bookId, 0);
            }
        }
    }

    public function updateProgress($userId, $bookId, $progressPercentage) {
        if (!$userId) {
            return ['success' => false, 'message' => 'User not logged in'];
        }

        try {
            $this->progressModel->updateProgress($userId, $bookId, $progressPercentage);
            return ['success' => true, 'message' => 'Progress updated'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to update progress'];
        }
    }

    public function getUserLibrary($userId, $status = null, $limit = null, $offset = 0) {
        if (!$userId) {
            return [];
        }

        return $this->progressModel->getAllUserProgress($userId, $status, $limit, $offset);
    }

    public function getUserLibraryCount($userId, $status = null) {
        if (!$userId) {
            return 0;
        }

        return $this->progressModel->getUserProgressCount($userId, $status);
    }

    public function getUserDashboardStats($userId) {
        if (!$userId) {
            return [
                'total_books' => 0,
                'completed_books' => 0,
                'in_progress_books' => 0,
                'avg_completion' => 0
            ];
        }

        $stats = $this->progressModel->getUserStats($userId);
        
        return [
            'total_books' => $stats['total_books'] ?? 0,
            'completed_books' => $stats['completed_books'] ?? 0,
            'in_progress_books' => $stats['in_progress_books'] ?? 0,
            'avg_completion' => round($stats['avg_completion'] ?? 0, 1)
        ];
    }

    public function getRecentlyAccessed($userId, $limit = 5) {
        if (!$userId) {
            return [];
        }

        return $this->progressModel->getRecentlyAccessed($userId, $limit);
    }

    public function getTotalBooksCount() {
        return $this->bookModel->getTotalCount();
    }
}
