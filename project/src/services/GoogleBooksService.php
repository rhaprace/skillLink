<?php

class GoogleBooksService {
    private $apiKey;
    private $baseUrl = 'https://www.googleapis.com/books/v1/volumes';
    private $cacheDir;
    
    public function __construct($apiKey = null) {
        $this->apiKey = $apiKey;
        $this->cacheDir = __DIR__ . '/../../cache/google-books/';
        
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }
    
    public function searchProgrammingBooks($query, $maxResults = 20, $startIndex = 0) {
        $searchQuery = "subject:computers+" . urlencode($query);
        
        $params = [
            'q' => $searchQuery,
            'maxResults' => min($maxResults, 40),
            'startIndex' => $startIndex,
            'orderBy' => 'relevance',
            'printType' => 'books',
            'langRestrict' => 'en'
        ];
        
        if ($this->apiKey) {
            $params['key'] = $this->apiKey;
        }
        
        return $this->makeRequest($params);
    }

    public function getBookById($volumeId) {
        $url = $this->baseUrl . '/' . $volumeId;
        
        $params = [];
        if ($this->apiKey) {
            $params['key'] = $this->apiKey;
        }
        
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        
        return $this->makeApiCall($url);
    }
    
    private function makeRequest($params) {
        $url = $this->baseUrl . '?' . http_build_query($params);
        return $this->makeApiCall($url);
    }
    
    private function makeApiCall($url) {
        $cacheKey = md5($url);
        $cacheFile = $this->cacheDir . $cacheKey . '.json';
        
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < 86400) {
            $cachedData = file_get_contents($cacheFile);
            return json_decode($cachedData, true);
        }
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            error_log("Google Books API Error: " . $error);
            return false;
        }
        
        if ($httpCode !== 200) {
            error_log("Google Books API HTTP Error: " . $httpCode);
            return false;
        }
        
        $data = json_decode($response, true);
        
        if (!$data) {
            error_log("Google Books API: Invalid JSON response");
            return false;
        }
        
        file_put_contents($cacheFile, $response);
        
        return $data;
    }
    
    public function clearCache() {
        $files = glob($this->cacheDir . '*.json');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}

