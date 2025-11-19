<?php
session_start();
require_once '../src/config/database.php';
require_once '../src/controllers/BookController.php';

$pageTitle = 'Browse Books - SkillLink';
$bookController = new BookController($pdo);

$categoryId = isset($_GET['category']) ? intval($_GET['category']) : null;
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

if ($searchQuery) {
    $books = $bookController->searchBooks($searchQuery);
} else {
    $books = $bookController->getAllBooks($categoryId);
}

$categories = $bookController->getCategories();

require_once '../src/includes/header.php';
?>

<div class="min-h-screen bg-gray-50 py-8">
    <div class="container-custom">
        <div class="mb-8 animate-fade-in">
            <h1 class="text-4xl font-bold text-black mb-2">Browse Learning Materials</h1>
            <p class="text-gray-600">Explore our collection of books and tutorials</p>
        </div>
        <div class="mb-8 animate-slide-up" style="animation-delay: 100ms;">
            <div class="card p-6">
                <form method="GET" action="books.php" class="space-y-4">
                    <div class="flex gap-3">
                        <input 
                            type="text" 
                            name="search" 
                            placeholder="Search books by title or description..." 
                            value="<?php echo htmlspecialchars($searchQuery); ?>"
                            class="form-input flex-1"
                        >
                        <button type="submit" class="btn btn-primary">
                            Search
                        </button>
                        <?php if ($searchQuery || $categoryId): ?>
                            <a href="books.php" class="btn btn-secondary">
                                Clear
                            </a>
                        <?php endif; ?>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <a href="books.php" 
                           class="px-4 py-2 rounded-lg text-sm font-medium transition-colors <?php echo !$categoryId ? 'bg-black text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                            All Categories
                        </a>
                        <?php foreach ($categories as $category): ?>
                            <a href="books.php?category=<?php echo $category['id']; ?>" 
                               class="px-4 py-2 rounded-lg text-sm font-medium transition-colors <?php echo $categoryId == $category['id'] ? 'bg-black text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                                <?php echo htmlspecialchars($category['icon'] . ' ' . $category['name']); ?>
                                <span class="ml-1 text-xs opacity-75">(<?php echo $category['books_count']; ?>)</span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </form>
            </div>
        </div>

        <div class="mb-4 animate-fade-in" style="animation-delay: 150ms;">
            <p class="text-gray-600">
                Found <span class="font-semibold text-black"><?php echo count($books); ?></span> 
                <?php echo count($books) === 1 ? 'book' : 'books'; ?>
                <?php if ($searchQuery): ?>
                    for "<?php echo htmlspecialchars($searchQuery); ?>"
                <?php endif; ?>
            </p>
        </div>

        <?php if (empty($books)): ?>
            <div class="card p-12 text-center animate-fade-in" style="animation-delay: 200ms;">
                <p class="text-gray-500 text-lg mb-4">No books found</p>
                <a href="books.php" class="btn btn-primary">Browse All Books</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($books as $index => $book): ?>
                    <a href="book.php?id=<?php echo $book['id']; ?>" 
                       class="card card-hover animate-slide-up" 
                       style="animation-delay: <?php echo 200 + ($index * 50); ?>ms;">
                        <div class="p-6">
                            <?php if ($book['category_name']): ?>
                                <div class="mb-3">
                                    <span class="inline-block px-3 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded-full">
                                        <?php echo htmlspecialchars($book['category_name']); ?>
                                    </span>
                                </div>
                            <?php endif; ?>

                            <h3 class="text-xl font-bold text-black mb-2 line-clamp-2">
                                <?php echo htmlspecialchars($book['title']); ?>
                            </h3>

                            <?php if ($book['author']): ?>
                                <p class="text-sm text-gray-600 mb-3">
                                    by <?php echo htmlspecialchars($book['author']); ?>
                                </p>
                            <?php endif; ?>

                            <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                                <?php echo htmlspecialchars($book['description']); ?>
                            </p>

                            <div class="flex items-center justify-between text-xs text-gray-500 pt-4 border-t border-gray-100">
                                <span class="flex items-center gap-1">
                                    ⏱️ <?php echo $book['estimated_duration']; ?> min
                                </span>
                                <span class="capitalize px-2 py-1 bg-gray-50 rounded">
                                    <?php echo htmlspecialchars($book['difficulty_level']); ?>
                                </span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../src/includes/footer.php'; ?>
