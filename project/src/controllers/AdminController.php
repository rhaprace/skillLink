<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Book.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Bookmark.php';
require_once __DIR__ . '/../models/UserProgress.php';
require_once __DIR__ . '/../models/Review.php';

class AdminController
{
    private $pdo;
    private $userModel;
    private $bookModel;
    private $categoryModel;
    private $bookmarkModel;
    private $progressModel;
    private $reviewModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->userModel = new User($pdo);
        $this->bookModel = new Book($pdo);
        $this->categoryModel = new Category($pdo);
        $this->bookmarkModel = new Bookmark($pdo);
        $this->progressModel = new UserProgress($pdo);
        $this->reviewModel = new Review($pdo);
    }

    public function getDashboardStats() {
        $stats = [];

        $sql = "SELECT COUNT(*) as total FROM users";
        $stmt = $this->pdo->query($sql);
        $stats['total_users'] = $stmt->fetch()['total'];

        $sql = "SELECT COUNT(*) as total FROM books";
        $stmt = $this->pdo->query($sql);
        $stats['total_books'] = $stmt->fetch()['total'];

        $sql = "SELECT COUNT(*) as total FROM categories";
        $stmt = $this->pdo->query($sql);
        $stats['total_categories'] = $stmt->fetch()['total'];

        $sql = "SELECT COUNT(*) as total FROM user_bookmarks";
        $stmt = $this->pdo->query($sql);
        $stats['total_bookmarks'] = $stmt->fetch()['total'];

        $sql = "SELECT COUNT(*) as total FROM book_reviews";
        $stmt = $this->pdo->query($sql);
        $stats['total_reviews'] = $stmt->fetch()['total'];

        return $stats;
    }

    public function getRecentUsers($limit = 5) {
        $sql = "SELECT id, username, email, created_at FROM users ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getPopularBooks($limit = 5) {
        $sql = "SELECT b.*, c.name as category_name, 
                (SELECT COUNT(*) FROM user_bookmarks WHERE book_id = b.id) as bookmark_count
                FROM books b
                LEFT JOIN categories c ON b.category_id = c.id
                ORDER BY b.views_count DESC, bookmark_count DESC
                LIMIT :limit";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAllUsers($search = null) {
        if ($search) {
            $sql = "SELECT id, username, email, created_at FROM users 
                    WHERE username LIKE :search OR email LIKE :search 
                    ORDER BY created_at DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':search' => '%' . $search . '%']);
        } else {
            $sql = "SELECT id, username, email, created_at FROM users ORDER BY created_at DESC";
            $stmt = $this->pdo->query($sql);
        }
        return $stmt->fetchAll();
    }

    public function getUserDetails($userId) {
        $user = $this->userModel->findById($userId);
        if (!$user) {
            return null;
        }

        $progressCount = $this->progressModel->getUserProgressCount($userId);
        $bookmarkCount = $this->bookmarkModel->getBookmarkCount($userId);
        $recentProgress = $this->progressModel->getRecentlyAccessed($userId, 5);
        $bookmarks = $this->bookmarkModel->getUserBookmarks($userId, 5);
        
        return [
            'user' => $user,
            'progress_count' => $progressCount,
            'bookmark_count' => $bookmarkCount,
            'recent_progress' => $recentProgress,
            'bookmarks' => $bookmarks
        ];
    }

    public function updateUser($userId, $username, $email) {
        try {
            if ($this->userModel->checkUsernameExists($username, $userId)) {
                return ['success' => false, 'message' => 'Username already exists'];
            }
            
            if ($this->userModel->checkEmailExists($email, $userId)) {
                return ['success' => false, 'message' => 'Email already exists'];
            }
            
            $result = $this->userModel->updateUser($userId, [
                'username' => $username,
                'email' => $email
            ]);
            
            if ($result) {
                return ['success' => true, 'message' => 'User updated successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to update user'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'An error occurred'];
        }
    }

    public function deleteUser($userId) {
        try {
            $result = $this->userModel->deleteUser($userId);
            
            if ($result) {
                return ['success' => true, 'message' => 'User deleted successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to delete user'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'An error occurred'];
        }
    }

    public function getAllBooksAdmin($search = null, $categoryId = null, $difficulty = null) {
        $sql = "SELECT b.*, c.name as category_name FROM books b 
                LEFT JOIN categories c ON b.category_id = c.id WHERE 1=1";
        $params = [];
        
        if ($search) {
            $sql .= " AND (b.title LIKE :search OR b.author LIKE :search2)";
            $params[':search'] = '%' . $search . '%';
            $params[':search2'] = '%' . $search . '%';
        }
        
        if ($categoryId) {
            $sql .= " AND b.category_id = :category_id";
            $params[':category_id'] = $categoryId;
        }

        if ($difficulty) {
            $sql .= " AND b.difficulty_level = :difficulty";
            $params[':difficulty'] = $difficulty;
        }

        $sql .= " ORDER BY b.created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function createBook($data) {
        try {
            $sql = "INSERT INTO books (title, description, content, author, category_id, cover_image,
                    difficulty_level, estimated_duration, is_featured)
                    VALUES (:title, :description, :content, :author, :category_id, :cover_image,
                    :difficulty_level, :estimated_duration, :is_featured)";

            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                ':title' => $data['title'],
                ':description' => $data['description'],
                ':content' => $data['content'],
                ':author' => $data['author'],
                ':category_id' => $data['category_id'],
                ':cover_image' => $data['cover_image'] ?? null,
                ':difficulty_level' => $data['difficulty_level'],
                ':estimated_duration' => $data['estimated_duration'],
                ':is_featured' => $data['is_featured'] ?? 0
            ]);

            if ($result) {
                return ['success' => true, 'message' => 'Book created successfully', 'id' => $this->pdo->lastInsertId()];
            } else {
                return ['success' => false, 'message' => 'Failed to create book'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()];
        }
    }

    public function updateBook($bookId, $data) {
        try {
            $sql = "UPDATE books SET title = :title, description = :description, content = :content,
                    author = :author, category_id = :category_id, cover_image = :cover_image,
                    difficulty_level = :difficulty_level, estimated_duration = :estimated_duration,
                    is_featured = :is_featured WHERE id = :id";

            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                ':id' => $bookId,
                ':title' => $data['title'],
                ':description' => $data['description'],
                ':content' => $data['content'],
                ':author' => $data['author'],
                ':category_id' => $data['category_id'],
                ':cover_image' => $data['cover_image'] ?? 'default-book.jpg',
                ':difficulty_level' => $data['difficulty_level'],
                ':estimated_duration' => $data['estimated_duration'],
                ':is_featured' => $data['is_featured'] ?? 0
            ]);

            if ($result) {
                return ['success' => true, 'message' => 'Book updated successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to update book'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'An error occurred'];
        }
    }

    public function deleteBook($bookId) {
        try {
            $sql = "DELETE FROM books WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([':id' => $bookId]);

            if ($result) {
                return ['success' => true, 'message' => 'Book deleted successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to delete book'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'An error occurred'];
        }
    }

    public function toggleFeatured($bookId) {
        try {
            $sql = "UPDATE books SET is_featured = NOT is_featured WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([':id' => $bookId]);

            if ($result) {
                return ['success' => true, 'message' => 'Featured status updated'];
            } else {
                return ['success' => false, 'message' => 'Failed to update featured status'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'An error occurred'];
        }
    }

    public function getAllCategoriesAdmin() {
        return $this->categoryModel->getBooksCount();
    }

    public function createCategory($name, $slug, $icon, $description = null) {
        try {
            $sql = "INSERT INTO categories (name, slug, icon, description) VALUES (:name, :slug, :icon, :description)";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                ':name' => $name,
                ':slug' => $slug,
                ':icon' => $icon,
                ':description' => $description
            ]);

            if ($result) {
                return ['success' => true, 'message' => 'Category created successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to create category'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Category already exists or an error occurred'];
        }
    }

    public function updateCategory($categoryId, $name, $slug, $icon, $description = null) {
        try {
            $sql = "UPDATE categories SET name = :name, slug = :slug, icon = :icon, description = :description WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                ':id' => $categoryId,
                ':name' => $name,
                ':slug' => $slug,
                ':icon' => $icon,
                ':description' => $description
            ]);

            if ($result) {
                return ['success' => true, 'message' => 'Category updated successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to update category'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'An error occurred'];
        }
    }

    public function deleteCategory($categoryId) {
        try {
            $sql = "SELECT COUNT(*) as count FROM books WHERE category_id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $categoryId]);
            $result = $stmt->fetch();

            if ($result['count'] > 0) {
                return ['success' => false, 'message' => 'Cannot delete category with existing books'];
            }

            $sql = "DELETE FROM categories WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([':id' => $categoryId]);

            if ($result) {
                return ['success' => true, 'message' => 'Category deleted successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to delete category'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'An error occurred'];
        }
    }

    public function getAllBookmarks($userId = null, $bookId = null) {
        $sql = "SELECT ub.*,
                       u.username,
                       u.email as user_email,
                       b.title as book_title,
                       b.author as book_author,
                       c.name as category_name
                FROM user_bookmarks ub
                INNER JOIN users u ON ub.user_id = u.id
                INNER JOIN books b ON ub.book_id = b.id
                LEFT JOIN categories c ON b.category_id = c.id
                WHERE 1=1";
        $params = [];

        if ($userId) {
            $sql .= " AND ub.user_id = :user_id";
            $params[':user_id'] = $userId;
        }

        if ($bookId) {
            $sql .= " AND ub.book_id = :book_id";
            $params[':book_id'] = $bookId;
        }

        $sql .= " ORDER BY ub.created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function deleteBookmark($bookmarkId) {
        try {
            $sql = "DELETE FROM user_bookmarks WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([':id' => $bookmarkId]);

            if ($result) {
                return ['success' => true, 'message' => 'Bookmark deleted successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to delete bookmark'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'An error occurred'];
        }
    }

    // Review Management Methods
    public function getAllReviews($search = null, $bookId = null, $userId = null, $rating = null) {
        return $this->reviewModel->getAllReviews($search, $bookId, $userId, $rating);
    }

    public function deleteReview($reviewId) {
        try {
            $result = $this->reviewModel->deleteByAdmin($reviewId);

            if ($result) {
                return ['success' => true, 'message' => 'Review deleted successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to delete review'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'An error occurred'];
        }
    }
}


