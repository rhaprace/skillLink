<?php
class BookImportMapper {
    private $pdo;
    private $categories = [];
    private $categoryKeywords = [];
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->loadCategories();
        $this->initializeCategoryKeywords();
    }
    private function loadCategories() {
        $stmt = $this->pdo->query("SELECT id, name FROM categories");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->categories[strtolower($row['name'])] = $row['id'];
        }
    }
    private function initializeCategoryKeywords() {
        $this->categoryKeywords = [
            'Web Development' => ['javascript', 'react', 'vue', 'angular', 'node.js', 'html', 'css', 'web', 'frontend', 'backend', 'php', 'laravel', 'ruby', 'rails'],
            'Data Science' => ['python', 'data science', 'machine learning', 'ai', 'artificial intelligence', 'pandas', 'numpy', 'tensorflow', 'data analysis', 'statistics', 'sql', 'database', 'mysql', 'postgresql', 'mongodb'],
            'Mobile Development' => ['android', 'ios', 'swift', 'kotlin', 'react native', 'flutter', 'mobile'],
            'DevOps' => ['devops', 'docker', 'kubernetes', 'ci/cd', 'jenkins', 'ansible', 'terraform'],
            'Cloud Computing' => ['aws', 'azure', 'cloud', 'gcp', 'google cloud'],
            'Cybersecurity' => ['security', 'cybersecurity', 'hacking', 'penetration', 'encryption'],
            'Game Development' => ['game', 'unity', 'unreal', 'gaming'],
            'Programming' => ['java', 'c++', 'c#', '.net', 'programming', 'coding', 'software', 'algorithm', 'data structures']
        ];
    }
    public function mapVolume($volume) {
        if (!isset($volume['volumeInfo'])) {
            return null;
        }
        
        $info = $volume['volumeInfo'];
        
        if (empty($info['title'])) {
            return null;
        }
        
        $title = $this->sanitize($info['title']);
        $authors = isset($info['authors']) ? implode(', ', $info['authors']) : 'Unknown';
        $description = isset($info['description']) ? $this->sanitize($info['description'], 1000) : '';
        $pageCount = isset($info['pageCount']) ? intval($info['pageCount']) : 0;
        $categories = isset($info['categories']) ? $info['categories'] : [];
        $imageUrl = isset($info['imageLinks']['thumbnail']) ? $info['imageLinks']['thumbnail'] : null;
        
        $mappedData = [
            'title' => $title,
            'author' => $this->sanitize($authors),
            'description' => $description,
            'estimated_duration' => $this->calculateReadingTime($pageCount),
            'category_id' => $this->detectCategory($title, $description, $categories),
            'difficulty_level' => $this->detectDifficulty($title, $description),
            'cover_image_url' => $imageUrl,
            'google_books_id' => $volume['id'] ?? null,
            'page_count' => $pageCount
        ];
        
        return $mappedData;
    }
    private function sanitize($text, $maxLength = null) {
        $text = strip_tags($text);
        $text = trim($text);
        
        if ($maxLength && strlen($text) > $maxLength) {
            $text = substr($text, 0, $maxLength) . '...';
        }
        
        return $text;
    }
    
    private function calculateReadingTime($pageCount) {
        if ($pageCount <= 0) {
            return 30;
        }
        
        return max(30, intval($pageCount / 2));
    }
    private function detectCategory($title, $description, $googleCategories) {
        $text = strtolower($title . ' ' . $description . ' ' . implode(' ', $googleCategories));
        
        foreach ($this->categoryKeywords as $categoryName => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($text, strtolower($keyword)) !== false) {
                    $categoryKey = strtolower($categoryName);
                    if (isset($this->categories[$categoryKey])) {
                        return $this->categories[$categoryKey];
                    }
                }
            }
        }
        
        return $this->categories['programming'] ?? null;
    }
    private function detectDifficulty($title, $description) {
        $text = strtolower($title . ' ' . $description);
        
        $beginnerKeywords = ['beginner', 'introduction', 'getting started', 'basics', 'fundamentals', 'learn', 'start'];
        $advancedKeywords = ['advanced', 'expert', 'mastering', 'professional', 'deep dive', 'architecture'];
        
        foreach ($advancedKeywords as $keyword) {
            if (strpos($text, $keyword) !== false) {
                return 'advanced';
            }
        }
        
        foreach ($beginnerKeywords as $keyword) {
            if (strpos($text, $keyword) !== false) {
                return 'beginner';
            }
        }
        
        return 'intermediate';
    }
}

