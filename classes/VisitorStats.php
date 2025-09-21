<?php
/**
 * Visitor Statistics Class
 * Tracks website visitors and provides analytics using MySQL database
 */
class VisitorStats {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function trackVisit($page = '') {
        $ip = $this->getClientIP();
        $browser = $this->getBrowserInfo();
        $currentDate = date('Y-m-d');
        $currentTime = date('H:i:s');

        try {
            // Check if this IP already visited today on this page
            $stmt = $this->pdo->prepare("
                SELECT id, visit_count FROM visitor_stats 
                WHERE ip_address = ? AND visit_date = ? AND page_visited = ?
            ");
            $stmt->execute(array($ip, $currentDate, $page));
            $existingRecord = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existingRecord) {
                // Update existing record
                $newCount = $existingRecord['visit_count'] + 1;
                $updateStmt = $this->pdo->prepare("
                    UPDATE visitor_stats 
                    SET visit_count = ?, visit_time = ?, browser_name = ?, browser_version = ?
                    WHERE id = ?
                ");
                $updateStmt->execute(array($newCount, $currentTime, $browser['name'], $browser['version'], $existingRecord['id']));
            } else {
                // Insert new record
                $insertStmt = $this->pdo->prepare("
                    INSERT INTO visitor_stats (ip_address, browser_name, browser_version, page_visited, visit_date, visit_time, visit_count)
                    VALUES (?, ?, ?, ?, ?, ?, 1)
                ");
                $insertStmt->execute(array($ip, $browser['name'], $browser['version'], $page, $currentDate, $currentTime));
            }

        } catch (PDOException $e) {
            error_log("Error in VisitorStats::trackVisit: " . $e->getMessage());
            // Fail silently to not break the user experience
        }
    }

    private function getClientIP() {
        $ipKeys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR');
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
    }

    private function getBrowserInfo() {
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $browsers = array(
            'Chrome' => '/Chrome\/([0-9\.]+)/',
            'Firefox' => '/Firefox\/([0-9\.]+)/',
            'Safari' => '/Safari\/([0-9\.]+)/',
            'Edge' => '/Edge\/([0-9\.]+)/',
            'Opera' => '/Opera\/([0-9\.]+)/',
            'IE' => '/MSIE ([0-9\.]+)/'
        );

        foreach ($browsers as $name => $pattern) {
            if (preg_match($pattern, $userAgent, $matches)) {
                return array(
                    'name' => $name,
                    'version' => $matches[1]
                );
            }
        }

        return array(
            'name' => 'Unknown',
            'version' => '0.0'
        );
    }

    public function getStats($groupBy = 'all', $filters = array()) {
        $stats = array();

        switch ($groupBy) {
            case 'time':
                $stats = $this->getTimeStats($filters);
                break;
            case 'date':
                $stats = $this->getDateStats($filters);
                break;
            case 'browser':
                $stats = $this->getBrowserStats($filters);
                break;
            case 'ip':
                $stats = $this->getIPStats($filters);
                break;
            case 'page':
                $stats = $this->getPageStats($filters);
                break;
            default:
                $stats = $this->getAllStats($filters);
        }

        return $stats;
    }

    private function buildFilterWhereClause($filters) {
        $whereClause = "1=1";
        $params = array();

        if (!empty($filters['browser'])) {
            $whereClause .= " AND browser_name = ?";
            $params[] = $filters['browser'];
        }
        if (!empty($filters['ip'])) {
            $whereClause .= " AND ip_address = ?";
            $params[] = $filters['ip'];
        }
        if (!empty($filters['start_date'])) {
            $whereClause .= " AND visit_date >= ?";
            $params[] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $whereClause .= " AND visit_date <= ?";
            $params[] = $filters['end_date'];
        }
        if (!empty($filters['page'])) {
            $whereClause .= " AND page_visited = ?";
            $params[] = $filters['page'];
        }

        return array('where' => $whereClause, 'params' => $params);
    }

    private function getTimeStats($filters) {
        $filter = $this->buildFilterWhereClause($filters);
        $sql = "SELECT HOUR(visit_time) as hour, SUM(visit_count) as total_visits 
                FROM visitor_stats 
                WHERE {$filter['where']} 
                GROUP BY hour 
                ORDER BY hour ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($filter['params']);

        $timeStats = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $timeStats[$row['hour']] = (int)$row['total_visits'];
        }

        return $timeStats;
    }

    private function getDateStats($filters) {
        $filter = $this->buildFilterWhereClause($filters);
        $sql = "SELECT visit_date, SUM(visit_count) as total_visits 
                FROM visitor_stats 
                WHERE {$filter['where']} 
                GROUP BY visit_date 
                ORDER BY visit_date DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($filter['params']);

        $dateStats = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dateStats[$row['visit_date']] = (int)$row['total_visits'];
        }

        return $dateStats;
    }

    private function getBrowserStats($filters) {
        $filter = $this->buildFilterWhereClause($filters);
        $sql = "SELECT browser_name, SUM(visit_count) as total_visits 
                FROM visitor_stats 
                WHERE {$filter['where']} 
                GROUP BY browser_name 
                ORDER BY total_visits DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($filter['params']);

        $browserStats = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $browserStats[$row['browser_name']] = (int)$row['total_visits'];
        }

        return $browserStats;
    }

    private function getIPStats($filters) {
        $filter = $this->buildFilterWhereClause($filters);
        $sql = "SELECT ip_address, SUM(visit_count) as total_visits 
                FROM visitor_stats 
                WHERE {$filter['where']} 
                GROUP BY ip_address 
                ORDER BY total_visits DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($filter['params']);

        $ipStats = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $ipStats[$row['ip_address']] = (int)$row['total_visits'];
        }

        return $ipStats;
    }

    private function getPageStats($filters) {
        $filter = $this->buildFilterWhereClause($filters);
        $sql = "SELECT page_visited, SUM(visit_count) as total_visits 
                FROM visitor_stats 
                WHERE {$filter['where']} 
                GROUP BY page_visited 
                ORDER BY total_visits DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($filter['params']);

        $pageStats = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pageName = !empty($row['page_visited']) ? $row['page_visited'] : 'index';
            $pageStats[$pageName] = (int)$row['total_visits'];
        }

        return $pageStats;
    }

    private function getAllStats($filters) {
        $filter = $this->buildFilterWhereClause($filters);

        // Get total visits
        $stmt = $this->pdo->prepare("SELECT SUM(visit_count) as total_visits FROM visitor_stats WHERE {$filter['where']}");
        $stmt->execute($filter['params']);
        $totalVisitsResult = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalVisits = (int)$totalVisitsResult['total_visits'];

        // Get unique IPs
        $stmt = $this->pdo->prepare("SELECT COUNT(DISTINCT ip_address) as unique_ips FROM visitor_stats WHERE {$filter['where']}");
        $stmt->execute($filter['params']);
        $uniqueIPsResult = $stmt->fetch(PDO::FETCH_ASSOC);
        $uniqueIPs = (int)$uniqueIPsResult['unique_ips'];

        // Get total records (rows)
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as total_records FROM visitor_stats WHERE {$filter['where']}");
        $stmt->execute($filter['params']);
        $totalRecordsResult = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalRecords = (int)$totalRecordsResult['total_records'];

        return array(
            'total_visits' => $totalVisits,
            'unique_ips' => $uniqueIPs,
            'total_records' => $totalRecords,
            'browsers' => $this->getBrowserStats($filters),
            'pages' => $this->getPageStats($filters),
            'dates' => $this->getDateStats($filters)
        );
    }

    public function getPercentageStats($groupBy = 'browser', $filters = array()) {
        $validGroupings = array('time', 'date', 'browser', 'ip', 'page');
        if (!in_array($groupBy, $validGroupings)) {
            trigger_error("Percentage stats are not available for grouping '$groupBy'. Valid options: " . implode(', ', $validGroupings), E_USER_WARNING);
            return array();
        }

        $stats = $this->getStats($groupBy, $filters);
        $total = array_sum($stats);
        $percentages = array();

        foreach ($stats as $key => $value) {
            $percentages[$key] = $total > 0 ? round(($value / $total) * 100, 2) : 0;
        }

        return $percentages;
    }

    public function clearData() {
        try {
            $this->pdo->exec("TRUNCATE TABLE visitor_stats");
            return true;
        } catch (PDOException $e) {
            error_log("Error clearing visitor stats: " . $e->getMessage());
            return false;
        }
    }

    // New method to get raw data for admin filtering
    public function getRawData($filters = array(), $limit = 50, $offset = 0) {
        $filter = $this->buildFilterWhereClause($filters);

        $sql = "SELECT id, ip_address, browser_name, browser_version, page_visited, visit_date, visit_time, visit_count 
                FROM visitor_stats 
                WHERE {$filter['where']} 
                ORDER BY created_at DESC 
                LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);

        // Bind parameters
        foreach ($filter['params'] as $index => $param) {
            $stmt->bindValue($index + 1, $param);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // New method to get distinct values for filter dropdowns
    public function getDistinctValues($column) {
        $validColumns = array('browser_name', 'ip_address', 'page_visited');
        if (!in_array($column, $validColumns)) {
            return array();
        }

        $sql = "SELECT DISTINCT $column FROM visitor_stats ORDER BY $column ASC";
        $stmt = $this->pdo->query($sql);
        $results = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return array_filter($results, function($value) { return !empty($value); });
    }
}
?>