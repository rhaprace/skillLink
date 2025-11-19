<?php
require_once __DIR__ . '/../models/Bookmark.php';
require_once __DIR__ . '/../models/Book.php';

class BookmarkController
{
    private $bookmarkModel;
    private $bookModel;

    public function __construct($pdo) {
        $this->bookmarkModel = new Bookmark($pdo);
        $this->bookModel = new Book($pdo);
    }

    public function toggleBookmark($userId, $bookId) {
        if (!$userId) {
            return [
                'success' => false,
                'message' => 'You must be logged in to bookmark books'
            ];
        }

        $book = $this->bookModel->getById($bookId);
        if (!$book) {
            return [
                'success' => false,
                'message' => 'Book not found'
            ];
        }

        try {
            $result = $this->bookmarkModel->toggleBookmark($userId, $bookId);
            
            if ($result['success']) {
                $message = $result['action'] === 'added' 
                    ? 'Book added to bookmarks' 
                    : 'Book removed from bookmarks';
                
                return [
                    'success' => true,
                    'message' => $message,
                    'bookmarked' => $result['bookmarked'],
                    'action' => $result['action']
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to update bookmark'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred while updating bookmark'
            ];
        }
    }

    public function getUserBookmarks($userId, $limit = null, $offset = 0) {
        if (!$userId) {
            return [];
        }

        return $this->bookmarkModel->getUserBookmarks($userId, $limit, $offset);
    }

    public function getBookmarkCount($userId) {
        if (!$userId) {
            return 0;
        }

        return $this->bookmarkModel->getBookmarkCount($userId);
    }

    public function isBookmarked($userId, $bookId) {
        if (!$userId) {
            return false;
        }

        return $this->bookmarkModel->isBookmarked($userId, $bookId);
    }

    public function getUserBookmarkIds($userId) {
        if (!$userId) {
            return [];
        }

        return $this->bookmarkModel->getUserBookmarkIds($userId);
    }
}

