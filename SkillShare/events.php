<?php
require_once 'config/db.php';
include 'includes/header.php';

// Filter logic
$category_filters = isset($_GET['categories']) && is_array($_GET['categories']) ? $_GET['categories'] : [];
$search_query = isset($_GET['search']) ? $_GET['search'] : null;

$sql = "SELECT e.*, c.name as category_name, u.full_name as organizer_name 
        FROM events e 
        JOIN categories c ON e.category_id = c.id 
        JOIN users u ON e.organizer_id = u.id 
        WHERE 1=1 AND e.event_date >= NOW()";
$params = [];

if (!empty($category_filters)) {
    $placeholders = str_repeat('?,', count($category_filters) - 1) . '?';
    $sql .= " AND c.id IN ($placeholders)";
    $params = array_merge($params, $category_filters);
}

if ($search_query) {
    $sql .= " AND (e.title LIKE ? OR e.description LIKE ?)";
    $params[] = "%$search_query%";
    $params[] = "%$search_query%";
}

$sql .= " ORDER BY e.event_date ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$events = $stmt->fetchAll();

// Fetch categories for sidebar
$stmt_cat = $pdo->query("SELECT * FROM categories");
$categories = $stmt_cat->fetchAll();
?>

<div class="container" style="margin-top: 2rem;">
    <form method="GET">
        <div class="flex justify-between items-center mb-4">
            <h1>Browse Workshops</h1>
            <div class="flex gap-2">
                <input type="text" name="search" placeholder="Search workshops..." class="form-control"
                    value="<?php echo htmlspecialchars($search_query); ?>">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </div>

        <div class="grid" style="grid-template-columns: 250px 1fr; gap: 2rem;">
            <!-- Sidebar -->
            <aside>
                <div class="card">
                    <div class="card-body">
                        <div class="flex justify-between items-center mb-2">
                            <h3 class="card-title" style="font-size: 1.1rem; margin: 0;">Categories</h3>
                            <button type="submit" class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">Apply</button>
                        </div>
                        <div class="category-nav">
                            <?php foreach ($categories as $cat): ?>
                                <label class="category-link" style="cursor: pointer;">
                                    <span class="flex items-center gap-2">
                                        <input type="checkbox" name="categories[]" value="<?php echo $cat['id']; ?>"
                                            <?php echo in_array($cat['id'], $category_filters) ? 'checked' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Event List -->
            <div class="grid grid-cols-3" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));">
                <?php foreach ($events as $event): ?>
                    <div class="card">
                        <img src="<?php echo $event['image_path'] ? 'uploads/' . $event['image_path'] : 'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80'; ?>"
                            alt="Event Image" class="card-image">
                        <div class="card-body">
                            <div class="flex justify-between items-center mb-2">
                                <span
                                    class="badge badge-primary"><?php echo htmlspecialchars($event['category_name']); ?></span>
                                <span class="text-muted"
                                    style="font-size: 0.875rem;">$<?php echo number_format($event['price'], 2); ?></span>
                            </div>
                            <h3 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h3>
                            <p class="text-muted mb-2" style="font-size: 0.9rem;">
                                <i data-lucide="calendar" style="width: 14px; height: 14px; display: inline;"></i>
                                <?php echo date('M d, Y h:i A', strtotime($event['event_date'])); ?>
                            </p>
                            <p class="text-muted mb-4" style="font-size: 0.9rem;">
                                <i data-lucide="<?php echo $event['event_type'] == 'online' ? 'video' : 'map-pin'; ?>" style="width: 14px; height: 14px; display: inline;"></i>
                                <?php echo htmlspecialchars($event['location']); ?>
                                <span class="badge badge-secondary" style="font-size: 0.7rem; margin-left: 0.5rem;"><?php echo ucfirst($event['event_type']); ?></span>
                            </p>
                            <a href="event_details.php?id=<?php echo $event['id']; ?>" class="btn btn-outline btn-block">View
                                Details</a>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if (count($events) == 0): ?>
                    <div style="grid-column: 1 / -1; padding: 2rem; text-align: center;">
                        <p class="text-muted">No workshops found matching your criteria.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>