<?php
require_once 'includes/functions.php';
require_once 'classes/VisitorStats.php';

// Authentication Check
if (!isset($_SESSION['user_email'])) {
    $_SESSION['error_message'] = 'You must be logged in to view statistics.';
    header("Location: index.php");
    exit();
}

// Initialize VisitorStats with DB connection
$stats = new VisitorStats($pdo);

// Handle clear data request
if (isset($_POST['clear_data']) && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
    if ($stats->clearData()) {
        $_SESSION['success_message'] = 'Statistics data cleared successfully!';
    } else {
        $_SESSION['error_message'] = 'Failed to clear statistics data.';
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Get grouping and filtering parameters
$groupBy = isset($_GET['group']) ? $_GET['group'] : 'all';
$filters = array();

// Only admins can use advanced filters
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
    $filters['browser'] = isset($_GET['filter_browser']) ? $_GET['filter_browser'] : '';
    $filters['ip'] = isset($_GET['filter_ip']) ? $_GET['filter_ip'] : '';
    $filters['start_date'] = isset($_GET['start_date']) ? $_GET['start_date'] : '';
    $filters['end_date'] = isset($_GET['end_date']) ? $_GET['end_date'] : '';
    $filters['page'] = isset($_GET['filter_page']) ? $_GET['filter_page'] : '';
}

include 'includes/header.php';
?>
<main class="main">
    <div class="main-wrapper">
        <h2 class="main__title">Website Visitor Statistics</h2>
        <?php displayMessages(); ?>

        <div class="stats-controls">
            <form method="GET" class="stats-form">
                <label for="group">Group by:</label>
                <select style="background-color: #495057;" name="group" id="group" onchange="this.form.submit()">
                    <option value="all" <?php echo $groupBy === 'all' ? 'selected' : ''; ?>>All Statistics</option>
                    <option value="time" <?php echo $groupBy === 'time' ? 'selected' : ''; ?>>By Time (Hour)</option>
                    <option value="date" <?php echo $groupBy === 'date' ? 'selected' : ''; ?>>By Date</option>
                    <option value="browser" <?php echo $groupBy === 'browser' ? 'selected' : ''; ?>>By Browser</option>
                    <option value="ip" <?php echo $groupBy === 'ip' ? 'selected' : ''; ?>>By IP Address</option>
                    <option value="page" <?php echo $groupBy === 'page' ? 'selected' : ''; ?>>By Page</option>
                </select>
            </form>

            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
                <form method="POST" class="clear-form" onsubmit="return confirm('Are you sure you want to clear ALL statistics data? This action cannot be undone.')">
                    <button type="submit" name="clear_data" class="main-btn-type-1" style="background-color: #dc3545;">Clear All Data</button>
                </form>
            <?php endif; ?>
        </div>

        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
            <!-- Admin Filter Section -->
            <div class="admin-filters" style="margin: 30px 0; padding: 20px; border-radius: 8px; border: 1px solid #e9ecef;">
                <h3>Admin Filters</h3>
                <form method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end;">
                    <input type="hidden" name="group" value="<?php echo htmlspecialchars($groupBy, ENT_QUOTES, 'UTF-8'); ?>">

                    <div>
                        <label for="filter_browser">Browser:</label>
                        <select name="filter_browser" id="filter_browser" style="background-color: #495057;">
                            <option value="">All Browsers</option>
                            <?php
                            $distinctBrowsers = $stats->getDistinctValues('browser_name');
                            foreach ($distinctBrowsers as $browser):
                            ?>
                                <option value="<?php echo htmlspecialchars($browser, ENT_QUOTES, 'UTF-8'); ?>" <?php echo (isset($filters['browser']) && $filters['browser'] === $browser) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($browser, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="filter_ip">IP Address:</label>
                        <select name="filter_ip" id="filter_ip" style="background-color: #495057;">
                            <option value="">All IPs</option>
                            <?php
                            $distinctIPs = $stats->getDistinctValues('ip_address');
                            foreach ($distinctIPs as $ip):
                            ?>
                                <option value="<?php echo htmlspecialchars($ip, ENT_QUOTES, 'UTF-8'); ?>" <?php echo (isset($filters['ip']) && $filters['ip'] === $ip) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($ip, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="filter_page">Page:</label>
                        <select name="filter_page" id="filter_page" style="background-color: #495057;">
                            <option value="">All Pages</option>
                            <?php
                            $distinctPages = $stats->getDistinctValues('page_visited');
                            foreach ($distinctPages as $page):
                            ?>
                                <option value="<?php echo htmlspecialchars($page, ENT_QUOTES, 'UTF-8'); ?>" <?php echo (isset($filters['page']) && $filters['page'] === $page) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($page, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="start_date" >Start Date:</label>
                        <input style="background-color: #495057;" type="date" name="start_date" id="start_date" value="<?php echo htmlspecialchars(isset($filters['start_date']) ? $filters['start_date'] : '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>

                    <div>
                        <label for="end_date">End Date:</label>
                        <input style="background-color: #495057;" type="date" name="end_date" id="end_date" value="<?php echo htmlspecialchars(isset($filters['end_date']) ? $filters['end_date'] : '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>

                    <div style="grid-column: span 2;">
                        <button style="margin: 30px 0; padding: 20px; border-radius: 8px; border: 1px solid #e9ecef; background-color: #6c757d;" type="submit" class="main-btn-type-1">Apply Filters</button>
                        <a href="?group=<?php echo htmlspecialchars($groupBy, ENT_QUOTES, 'UTF-8'); ?>" class="main-btn-type-1" style="background-color: #6c757d; margin: 30px 0; padding: 20px; border-radius: 8px; border: 1px solid #e9ecef;">Clear Filters</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <div class="stats-content">
            <?php
            $statistics = $stats->getStats($groupBy, $filters);
            $percentages = $stats->getPercentageStats($groupBy, $filters);
            ?>

            <?php if ($groupBy === 'all'): ?>
                <div class="stats-overview">
                    <h3>Overview</h3>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <h4>Total Visits</h4>
                            <p class="stat-number"><?php echo $statistics['total_visits']; ?></p>
                        </div>
                        <div class="stat-card">
                            <h4>Unique IPs</h4>
                            <p class="stat-number"><?php echo $statistics['unique_ips']; ?></p>
                        </div>
                        <div class="stat-card">
                            <h4>Total Records</h4>
                            <p class="stat-number"><?php echo $statistics['total_records']; ?></p>
                        </div>
                    </div>

                    <div class="stats-sections">
                        <div class="stats-section">
                            <h4>Browser Distribution</h4>
                            <div class="stats-list">
                                <?php foreach ($statistics['browsers'] as $browser => $count): ?>
                                    <div class="stat-item">
                                        <span class="stat-label"><?php echo htmlspecialchars($browser, ENT_QUOTES, 'UTF-8'); ?></span>
                                        <span class="stat-value"><?php echo $count; ?> visits</span>
                                        <span class="stat-percentage"><?php echo $percentages[$browser]; ?>%</span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="stats-section">
                            <h4>Page Visits</h4>
                            <div class="stats-list">
                                <?php foreach ($statistics['pages'] as $page => $count): ?>
                                    <div class="stat-item">
                                        <span class="stat-label"><?php echo htmlspecialchars($page, ENT_QUOTES, 'UTF-8'); ?></span>
                                        <span class="stat-value"><?php echo $count; ?> visits</span>
                                        <span class="stat-percentage"><?php echo $percentages[$page]; ?>%</span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="stats-detail">
                    <h3>Statistics by <?php echo ucfirst($groupBy); ?></h3>
                    <?php if (empty($statistics)): ?>
                        <p>No data available for the selected filters.</p>
                    <?php else: ?>
                        <div class="stats-list">
                            <?php foreach ($statistics as $key => $value): ?>
                                <div class="stat-item">
                                    <span class="stat-label"><?php echo htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?></span>
                                    <span class="stat-value"><?php echo $value; ?> visits</span>
                                    <span class="stat-percentage"><?php echo $percentages[$key]; ?>%</span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
            <div class="raw-data-section" style="margin-top: 40px;">
                <h3>Raw Visitor Data (Last 50 Records)</h3>
                <table style="width: 100%; border-collapse: collapse; margin-top: 20px; background: #fff;">
                    <thead>
                        <tr style="background: #343a40; color: #fff;">
                            <th style="padding: 12px; text-align: left;">ID</th>
                            <th style="padding: 12px; text-align: left;">IP Address</th>
                            <th style="padding: 12px; text-align: left;">Browser</th>
                            <th style="padding: 12px; text-align: left;">Page</th>
                            <th style="padding: 12px; text-align: left;">Date</th>
                            <th style="padding: 12px; text-align: left;">Time</th>
                            <th style="padding: 12px; text-align: left;">Visits</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $rawData = $stats->getRawData($filters, 50, 0);
                        foreach ($rawData as $row):
                        ?>
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px; color: #495057;"><?php echo $row['id']; ?></td>
                            <td style="padding: 12px; color: #495057;"><?php echo htmlspecialchars($row['ip_address'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td style="padding: 12px; color: #495057;"><?php echo htmlspecialchars($row['browser_name'] . ' ' . $row['browser_version'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td style="padding: 12px; color: #495057;"><?php echo htmlspecialchars(!empty($row['page_visited']) ? $row['page_visited'] : 'index', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td style="padding: 12px; color: #495057;"><?php echo $row['visit_date']; ?></td>
                            <td style="padding: 12px; color: #495057;"><?php echo $row['visit_time']; ?></td>
                            <td style="padding: 12px; color: #495057;"><?php echo $row['visit_count']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</main>

<style>
.stats-controls {
    display: flex;
    gap: 20px;
    margin-bottom: 30px;
    align-items: center;
}
.stats-form, .clear-form {
    display: flex;
    align-items: center;
    gap: 10px;
}
.stats-form select {
    padding: 8px 12px;
    border: 1px solid #ccc;
    border-radius: 4px;
}
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}
.stat-card {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    border: 1px solid #e9ecef;
}
.stat-card h4 {
    margin: 0 0 10px 0;
    color: #495057;
    font-size: 14px;
    text-transform: uppercase;
}
.stat-number {
    font-size: 32px;
    font-weight: bold;
    color: #007bff;
    margin: 0;
}
.stats-sections {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}
.stats-section h4 {
    margin-bottom: 15px;
    color: #495057;
    border-bottom: 2px solid #007bff;
    padding-bottom: 5px;
}
.stats-list {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
}
.stat-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #e9ecef;
}
.stat-item:last-child {
    border-bottom: none;
}
.stat-label {
    font-weight: 500;
    color: #495057;
}
.stat-value {
    color: #007bff;
    font-weight: bold;
}
.stat-percentage {
    background: #007bff;
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: bold;
}

/* New styles for admin section */
.admin-filters label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: #495057;
}
.admin-filters select,
.admin-filters input {
    width: 100%;
    padding: 8px;
    border: 1px solid #ced4da;
    border-radius: 4px;
}
.admin-filters button {
    width: 100%;
    padding: 10px;
}
</style>

<?php include 'includes/footer.php'; ?>