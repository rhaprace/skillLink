<?php
session_start();
require_once '../../src/middleware/admin-auth.php';
require_once '../../src/config/database.php';
require_once '../../src/services/GoogleBooksService.php';
require_once '../../src/services/BookImportMapper.php';

$pageTitle = 'Import Books - Admin - SkillLink';
$currentPage = 'import-books.php';

$apiKey = getenv('GOOGLE_BOOKS_API_KEY') ?: null;

$googleBooks = new GoogleBooksService($apiKey);
$mapper = new BookImportMapper($pdo);

$searchResults = [];
$searchQuery = '';
$error = null;

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchQuery = trim($_GET['search']);
    $response = $googleBooks->searchProgrammingBooks($searchQuery, 20);
    
    if ($response && isset($response['items'])) {
        foreach ($response['items'] as $volume) {
            $mapped = $mapper->mapVolume($volume);
            if ($mapped) {
                $searchResults[] = array_merge($mapped, ['raw' => $volume]);
            }
        }
    } elseif ($response === false) {
        $error = 'Failed to fetch books from Google Books API. Please check your API key and internet connection.';
    } else {
        $error = 'No programming books found for "' . htmlspecialchars($searchQuery) . '"';
    }
}

require_once '../../src/includes/components/admin-header.php';
?>

<div class="admin-content">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-black mb-2">Import Programming Books</h1>
        <p class="text-gray-600">Search and import books from Google Books API</p>
    </div>

    <?php if (isset($_SESSION['import_success'])): ?>
        <div class="card p-4 mb-6 bg-green-50 border border-green-200">
            <p class="text-green-700"><?php echo htmlspecialchars($_SESSION['import_success']); ?></p>
        </div>
        <?php unset($_SESSION['import_success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['import_warning'])): ?>
        <div class="card p-4 mb-6 bg-yellow-50 border border-yellow-200">
            <p class="text-yellow-700"><?php echo htmlspecialchars($_SESSION['import_warning']); ?></p>
        </div>
        <?php unset($_SESSION['import_warning']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['import_errors'])): ?>
        <div class="card p-4 mb-6 bg-red-50 border border-red-200">
            <p class="text-red-700 font-semibold mb-2">Errors:</p>
            <ul class="list-disc list-inside text-sm text-red-600">
                <?php foreach ($_SESSION['import_errors'] as $err): ?>
                    <li><?php echo htmlspecialchars($err); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php unset($_SESSION['import_errors']); ?>
    <?php endif; ?>

    <?php if (!$apiKey): ?>
        <div class="card p-6 mb-6 bg-yellow-50 border border-yellow-200">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div>
                    <h3 class="font-semibold text-yellow-800 mb-1">API Key Not Configured</h3>
                    <p class="text-sm text-yellow-700 mb-2">
                        Google Books API key is not set. The API will work with limited requests (100/day).
                        For higher limits, set the <code class="bg-yellow-100 px-1 rounded">GOOGLE_BOOKS_API_KEY</code> environment variable.
                    </p>
                    <a href="https://console.cloud.google.com/" target="_blank" class="text-sm text-yellow-800 underline hover:no-underline">
                        Get API Key →
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <div class="card p-6 mb-6">
        <form method="GET" action="" class="space-y-4">
            <div class="flex gap-3">
                <div class="flex-1">
                    <input
                        type="text"
                        name="search"
                        value="<?php echo htmlspecialchars($searchQuery); ?>"
                        placeholder="Search for programming books (e.g., JavaScript, Python, React)..."
                        class="form-input w-full"
                        required
                    >
                </div>
                <button type="submit" class="btn btn-primary whitespace-nowrap">
                    <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Search
                </button>
            </div>
            <div class="flex flex-wrap gap-2">
                <span class="text-sm text-gray-600">Quick search:</span>
                <button type="submit" name="search" value="JavaScript" class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">JavaScript</button>
                <button type="submit" name="search" value="Python" class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Python</button>
                <button type="submit" name="search" value="React" class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">React</button>
                <button type="submit" name="search" value="Node.js" class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Node.js</button>
                <button type="submit" name="search" value="Machine Learning" class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Machine Learning</button>
                <button type="submit" name="search" value="Docker" class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Docker</button>
                <button type="submit" name="search" value="SQL" class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">SQL</button>
            </div>
        </form>
    </div>

    <?php if ($error): ?>
        <div class="card p-6 mb-6 bg-red-50 border border-red-200">
            <p class="text-red-700"><?php echo htmlspecialchars($error); ?></p>
        </div>
    <?php endif; ?>

    <?php if (!empty($searchResults)): ?>
        <div class="mb-4">
            <p class="text-gray-600">Found <strong><?php echo count($searchResults); ?></strong> programming books</p>
        </div>

        <form method="POST" action="process-import.php" id="importForm">
            <div class="space-y-4 mb-6">
                <?php foreach ($searchResults as $index => $book): ?>
                    <div class="card p-6 hover:shadow-lg transition-shadow">
                        <div class="flex gap-4">
                            <div class="flex-shrink-0">
                                <input
                                    type="checkbox"
                                    name="books[]"
                                    value="<?php echo htmlspecialchars(json_encode($book)); ?>"
                                    id="book_<?php echo $index; ?>"
                                    class="w-5 h-5 rounded border-gray-300 text-black focus:ring-black"
                                >
                            </div>
                            
                            <?php if ($book['cover_image_url']): ?>
                                <div class="flex-shrink-0">
                                    <img
                                        src="<?php echo htmlspecialchars($book['cover_image_url']); ?>"
                                        alt="<?php echo htmlspecialchars($book['title']); ?>"
                                        class="w-20 h-28 object-cover rounded shadow-sm"
                                        onerror="this.style.display='none'"
                                    >
                                </div>
                            <?php endif; ?>
                            
                            <label for="book_<?php echo $index; ?>" class="flex-1 cursor-pointer">
                                <h3 class="font-semibold text-lg text-black mb-1"><?php echo htmlspecialchars($book['title']); ?></h3>
                                <p class="text-sm text-gray-600 mb-2">by <?php echo htmlspecialchars($book['author']); ?></p>
                                <p class="text-sm text-gray-700 mb-3 line-clamp-2"><?php echo htmlspecialchars($book['description']); ?></p>
                                
                                <div class="flex flex-wrap gap-2 text-xs">
                                    <span class="px-2 py-1 bg-gray-100 rounded"><?php echo $book['page_count']; ?> pages</span>
                                    <span class="px-2 py-1 bg-gray-100 rounded">⏱<?php echo $book['estimated_duration']; ?> min</span>
                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded capitalize"><?php echo $book['difficulty_level']; ?></span>
                                </div>
                            </label>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="btn btn-primary">
                    Import Selected Books
                </button>
                <button type="button" onclick="selectAll()" class="btn btn-secondary">
                    Select All
                </button>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>
function selectAll() {
    const checkboxes = document.querySelectorAll('input[name="books[]"]');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(cb => cb.checked = !allChecked);
}
</script>

<?php require_once '../../src/includes/components/admin-footer.php'; ?>

