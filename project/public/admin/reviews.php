<?php
session_start();
require_once '../../src/middleware/admin-auth.php';
require_once '../../src/config/database.php';
require_once '../../src/controllers/AdminController.php';
require_once '../../src/models/Book.php';

$pageTitle = 'Reviews Management - Admin - SkillLink';
$adminController = new AdminController($pdo);
$bookModel = new Book($pdo);

$search = $_GET['search'] ?? null;
$bookId = $_GET['book_id'] ?? null;
$userId = $_GET['user_id'] ?? null;
$rating = $_GET['rating'] ?? null;

$reviews = $adminController->getAllReviews($search, $bookId, $userId, $rating);
$books = $bookModel->getAll();

require_once '../../src/includes/components/admin-header.php';
?>

<div class="mb-8">
    <h1 class="text-3xl font-bold text-black mb-2">Reviews Management</h1>
    <p class="text-gray-600">View and moderate all book reviews</p>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">
        <?php echo htmlspecialchars($_GET['success']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
        <?php echo htmlspecialchars($_GET['error']); ?>
    </div>
<?php endif; ?>
<div class="card mb-6">
    <div class="p-6">
        <form method="GET" action="reviews.php" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="lg:col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" name="search" value="<?php echo htmlspecialchars($search ?? ''); ?>"
                       placeholder="Search by user, book, or review text..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Book</label>
                <select name="book_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Books</option>
                    <?php foreach ($books as $book): ?>
                        <option value="<?php echo $book['id']; ?>" <?php echo $bookId == $book['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($book['title']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                <select name="rating" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Ratings</option>
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <option value="<?php echo $i; ?>" <?php echo $rating == $i ? 'selected' : ''; ?>>
                            <?php echo $i; ?> Star<?php echo $i > 1 ? 's' : ''; ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            
            <div class="flex items-end gap-2">
                <button type="submit" class="btn btn-primary flex-1">Filter</button>
                <a href="reviews.php" class="btn btn-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>
<div class="card">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Review</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Helpful</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($reviews)): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                            </svg>
                            <p class="text-lg font-medium">No reviews found</p>
                            <p class="text-sm">Try adjusting your filters</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($reviews as $review): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                #<?php echo $review['id']; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="font-medium"><?php echo htmlspecialchars($review['book_title']); ?></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="font-medium"><?php echo htmlspecialchars($review['username']); ?></div>
                                <div class="text-xs text-gray-500"><?php echo htmlspecialchars($review['email']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-1">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <svg class="w-4 h-4 <?php echo $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-300'; ?>" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    <?php endfor; ?>
                                    <span class="ml-1 text-sm font-medium text-gray-700"><?php echo $review['rating']; ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 max-w-md">
                                <?php if ($review['review_text']): ?>
                                    <div class="line-clamp-2"><?php echo htmlspecialchars($review['review_text']); ?></div>
                                <?php else: ?>
                                    <span class="text-gray-400 italic">No review text</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <?php echo $review['helpful_count']; ?> helpful
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo date('M d, Y', strtotime($review['created_at'])); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <form method="POST" action="delete-review.php" class="inline" onsubmit="return confirm('Are you sure you want to delete this review? This action cannot be undone.');">
                                    <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                                    <button type="submit" class="text-red-600 hover:text-red-900 font-medium">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<?php require_once '../../src/includes/components/admin-footer.php'; ?>

