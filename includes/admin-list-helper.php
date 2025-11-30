<?php
/**
 * Admin List Helper
 * Provides reusable functions for admin list views
 */

/**
 * Display a data table with edit/delete actions
 */
function displayAdminTable($items, $columns, $editUrl, $deleteUrl, $actions = []) {
    if (empty($items)) {
        echo '<div class="alert alert-info">No items found.</div>';
        return;
    }
    ?>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <?php foreach ($columns as $key => $label): ?>
                        <th><?php echo $label; ?></th>
                    <?php endforeach; ?>
                    <th style="width: 150px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <?php foreach ($columns as $key => $label): ?>
                            <td>
                                <?php
                                $value = $item[$key] ?? '';
                                
                                // Handle special column types
                                if (strpos($key, 'image') !== false && !empty($value)) {
                                    // Image column
                                    echo '<img src="' . htmlspecialchars($value) . '" alt="Image" style="max-width: 50px; max-height: 50px; border-radius: 3px;">';
                                } elseif ($key === 'rating') {
                                    // Rating column
                                    echo '<span style="color: #ffc107;">';
                                    for ($i = 0; $i < $value; $i++) {
                                        echo '★';
                                    }
                                    for ($i = $value; $i < 5; $i++) {
                                        echo '☆';
                                    }
                                    echo '</span>';
                                } elseif ($key === 'status') {
                                    // Status badge
                                    $badge_class = $value === 'published' ? 'bg-success' : 'bg-warning';
                                    echo '<span class="badge ' . $badge_class . '">' . ucfirst($value) . '</span>';
                                } elseif ($key === 'is_featured' || $key === 'is_active' || $key === 'is_read') {
                                    // Boolean column
                                    echo $value ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>';
                                } elseif (strlen($value) > 50) {
                                    // Truncate long text
                                    echo htmlspecialchars(substr($value, 0, 50)) . '...';
                                } else {
                                    echo htmlspecialchars($value);
                                }
                                ?>
                            </td>
                        <?php endforeach; ?>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="<?php echo sprintf($editUrl, $item['id']); ?>" class="btn btn-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?php echo sprintf($deleteUrl, $item['id']); ?>" class="btn btn-danger" title="Delete" onclick="return confirm('Are you sure?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                                <?php if (!empty($actions)): ?>
                                    <?php foreach ($actions as $action): ?>
                                        <a href="<?php echo sprintf($action['url'], $item['id']); ?>" class="btn btn-<?php echo $action['class'] ?? 'secondary'; ?>" title="<?php echo $action['title']; ?>">
                                            <i class="<?php echo $action['icon']; ?>"></i>
                                        </a>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

/**
 * Display pagination
 */
function displayPagination($current_page, $total_pages, $base_url) {
    if ($total_pages <= 1) {
        return;
    }
    ?>
    <nav aria-label="Page navigation" class="mt-4">
        <ul class="pagination justify-content-center">
            <?php if ($current_page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="<?php echo $base_url; ?>?page=1">First</a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="<?php echo $base_url; ?>?page=<?php echo $current_page - 1; ?>">Previous</a>
                </li>
            <?php endif; ?>

            <?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
                <li class="page-item <?php echo $i === $current_page ? 'active' : ''; ?>">
                    <a class="page-link" href="<?php echo $base_url; ?>?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($current_page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="<?php echo $base_url; ?>?page=<?php echo $current_page + 1; ?>">Next</a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="<?php echo $base_url; ?>?page=<?php echo $total_pages; ?>">Last</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
    <?php
}

/**
 * Get paginated items
 */
function getPaginatedItems($conn, $table, $page = 1, $per_page = 10, $order_by = 'id DESC') {
    $page = max(1, intval($page));
    $offset = ($page - 1) * $per_page;
    
    // Get total count
    $count_result = $conn->query("SELECT COUNT(*) as total FROM $table");
    $count = $count_result->fetch_assoc()['total'];
    $total_pages = ceil($count / $per_page);
    
    // Get items
    $result = $conn->query("SELECT * FROM $table ORDER BY $order_by LIMIT $offset, $per_page");
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
    
    return [
        'items' => $items,
        'current_page' => $page,
        'total_pages' => $total_pages,
        'total_count' => $count,
        'per_page' => $per_page
    ];
}
?>
