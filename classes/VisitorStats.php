<?php
/**
 * Visitor Statistics Class
 * Tracks website visitors and provides analytics
 */
class VisitorStats {
    private $csvFile;
    private $data;
    
    public function __construct($csvFile = 'data/visitor_stats.csv') {
        $this->csvFile = $csvFile;
        $this->ensureDataDirectory();
        $this->loadData();
    }
    
    private function ensureDataDirectory() {
        $dataDir = dirname($this->csvFile);
        if (!is_dir($dataDir)) {
            mkdir($dataDir, 0755, true);
        }
    }
    
    private function loadData() {
        $this->data = array();
        if (file_exists($this->csvFile)) {
            $handle = fopen($this->csvFile, 'r');
            if ($handle) {
                while (($row = fgetcsv($handle)) !== false) {
                    if (count($row) >= 7) {
                        $this->data[] = array(
                            'ip' => $row[0],
                            'browser' => $row[1],
                            'browser_version' => $row[2],
                            'visits' => (int)$row[3],
                            'page' => $row[4],
                            'date' => $row[5],
                            'time' => $row[6]
                        );
                    }
                }
                fclose($handle);
            }
        }
    }
    
    private function saveData() {
        $handle = fopen($this->csvFile, 'w');
        if ($handle) {
            foreach ($this->data as $row) {
                fputcsv($handle, array(
                    $row['ip'],
                    $row['browser'],
                    $row['browser_version'],
                    $row['visits'],
                    $row['page'],
                    $row['date'],
                    $row['time']
                ));
            }
            fclose($handle);
        }
    }
    
    public function trackVisit($page = '') {
        $ip = $this->getClientIP();
        $browser = $this->getBrowserInfo();
        $currentDate = date('Y-m-d');
        $currentTime = date('H:i:s');
        
        // Check if this IP already visited today
        $existingIndex = -1;
        foreach ($this->data as $index => $record) {
            if ($record['ip'] === $ip && $record['date'] === $currentDate) {
                $existingIndex = $index;
                break;
            }
        }
        
        if ($existingIndex >= 0) {
            // Update existing record
            $this->data[$existingIndex]['visits']++;
            $this->data[$existingIndex]['page'] = $page;
            $this->data[$existingIndex]['time'] = $currentTime;
        } else {
            // Add new record
            $this->data[] = array(
                'ip' => $ip,
                'browser' => $browser['name'],
                'browser_version' => $browser['version'],
                'visits' => 1,
                'page' => $page,
                'date' => $currentDate,
                'time' => $currentTime
            );
        }
        
        $this->saveData();
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
    
    public function getStats($groupBy = 'all') {
        $stats = array();
        
        switch ($groupBy) {
            case 'time':
                $stats = $this->getTimeStats();
                break;
            case 'date':
                $stats = $this->getDateStats();
                break;
            case 'browser':
                $stats = $this->getBrowserStats();
                break;
            case 'ip':
                $stats = $this->getIPStats();
                break;
            case 'page':
                $stats = $this->getPageStats();
                break;
            default:
                $stats = $this->getAllStats();
        }
        
        return $stats;
    }
    
    private function getTimeStats() {
        $timeStats = array();
        foreach ($this->data as $record) {
            $hour = date('H', strtotime($record['time']));
            if (!isset($timeStats[$hour])) {
                $timeStats[$hour] = 0;
            }
            $timeStats[$hour] += $record['visits'];
        }
        return $timeStats;
    }
    
    private function getDateStats() {
        $dateStats = array();
        foreach ($this->data as $record) {
            if (!isset($dateStats[$record['date']])) {
                $dateStats[$record['date']] = 0;
            }
            $dateStats[$record['date']] += $record['visits'];
        }
        return $dateStats;
    }
    
    private function getBrowserStats() {
        $browserStats = array();
        foreach ($this->data as $record) {
            $browser = $record['browser'];
            if (!isset($browserStats[$browser])) {
                $browserStats[$browser] = 0;
            }
            $browserStats[$browser] += $record['visits'];
        }
        return $browserStats;
    }
    
    private function getIPStats() {
        $ipStats = array();
        foreach ($this->data as $record) {
            if (!isset($ipStats[$record['ip']])) {
                $ipStats[$record['ip']] = 0;
            }
            $ipStats[$record['ip']] += $record['visits'];
        }
        return $ipStats;
    }
    
    private function getPageStats() {
        $pageStats = array();
        foreach ($this->data as $record) {
            $page = $record['page'] ?: 'index';
            if (!isset($pageStats[$page])) {
                $pageStats[$page] = 0;
            }
            $pageStats[$page] += $record['visits'];
        }
        return $pageStats;
    }
    
    private function getAllStats() {
        $visits = array();
        $ips = array();
        
        foreach ($this->data as $record) {
            $visits[] = $record['visits'];
            $ips[] = $record['ip'];
        }
    
        return array(
            'total_visits' => array_sum($visits),
            'unique_ips' => count(array_unique($ips)),
            'total_records' => count($this->data),
            'browsers' => $this->getBrowserStats(),
            'pages' => $this->getPageStats(),
            'dates' => $this->getDateStats()
        );
    }
    
    public function getPercentageStats($groupBy = 'browser') {
        $validGroupings = array('time', 'date', 'browser', 'ip', 'page');
        if (!in_array($groupBy, $validGroupings)) {
            trigger_error("Percentage stats are not available for grouping '$groupBy'. Valid options: " . implode(', ', $validGroupings), E_USER_WARNING);
            return array();
        }
    
        $stats = $this->getStats($groupBy);
        $total = array_sum($stats);
        $percentages = array();
    
        foreach ($stats as $key => $value) {
            $percentages[$key] = $total > 0 ? round(($value / $total) * 100, 2) : 0;
        }
    
        return $percentages;
    }
    
    public function clearData() {
        $this->data = array();
        $this->saveData();
    }
    
    public function exportData($format = 'csv') {
        if ($format === 'csv') {
            return $this->csvFile;
        }
        return false;
    }
}
?>
