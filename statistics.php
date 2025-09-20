<?php
require_once 'includes/functions.php';
require_once 'classes/VisitorStats.php';

$stats = new VisitorStats();
$groupBy = isset($_GET['group']) ? $_GET['group'] : 'all';

// Handle clear data request
if (isset($_POST['clear_data'])) {
    $stats->clearData();
    $_SESSION['success_message'] = 'Statistics data cleared successfully!';
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
                <select name="group" id="group" onchange="this.form.submit()">
                    <option value="all" <?php echo $groupBy === 'all' ? 'selected' : ''; ?>>All Statistics</option>
                    <option value="time" <?php echo $groupBy === 'time' ? 'selected' : ''; ?>>By Time (Hour)</option>
                    <option value="date" <?php echo $groupBy === 'date' ? 'selected' : ''; ?>>By Date</option>
                    <option value="browser" <?php echo $groupBy === 'browser' ? 'selected' : ''; ?>>By Browser</option>
                    <option value="ip" <?php echo $groupBy === 'ip' ? 'selected' : ''; ?>>By IP Address</option>
                    <option value="page" <?php echo $groupBy === 'page' ? 'selected' : ''; ?>>By Page</option>
                </select>
            </form>
            
            <form method="POST" class="clear-form" onsubmit="return confirm('Are you sure you want to clear all statistics data?')">
                <button type="submit" name="clear_data" class="main-btn-type-1">Clear All Data</button>
            </form>
        </div>
        
        <div class="stats-content">
            <?php
            $statistics = $stats->getStats($groupBy);
            $percentages = $stats->getPercentageStats($groupBy);
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
                                        <span class="stat-label"><?php echo htmlspecialchars($browser); ?></span>
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
                                        <span class="stat-label"><?php echo htmlspecialchars($page); ?></span>
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
                    <div class="stats-list">
                        <?php foreach ($statistics as $key => $value): ?>
                            <div class="stat-item">
                                <span class="stat-label"><?php echo htmlspecialchars($key); ?></span>
                                <span class="stat-value"><?php echo $value; ?> visits</span>
                                <span class="stat-percentage"><?php echo $percentages[$key]; ?>%</span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="stats-export">
            <h3>Export Data</h3>
            <p>Statistics data is automatically saved to: <code>data/visitor_stats.csv</code></p>
            <a href="data/visitor_stats.csv" class="main-btn-type-1" download>Download CSV</a>
        </div>
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

.stats-export {
    margin-top: 30px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
    text-align: center;
}

.stats-export code {
    background: #e9ecef;
    padding: 2px 6px;
    border-radius: 4px;
    font-family: monospace;
}
</style>

<?php include 'includes/footer.php'; ?>
