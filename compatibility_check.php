<?php
// PHP 5.3+ Compatibility Check
echo "<h1>PHP 5.3+ Compatibility Check</h1>";

echo "<h2>PHP Version Information:</h2>";
echo "Current PHP Version: " . phpversion() . "<br>";
echo "PHP 5.3+ Compatible: " . (version_compare(PHP_VERSION, '5.3.0', '>=') ? "✅ Yes" : "❌ No") . "<br>";

echo "<h2>Required Functions Check:</h2>";
$required_functions = array(
    'session_start',
    'session_id',
    'filter_var',
    'preg_match',
    'htmlspecialchars',
    'number_format',
    'date',
    'array_filter',
    'usort',
    'count',
    'isset',
    'empty',
    'trim'
);

foreach ($required_functions as $func) {
    echo $func . ": " . (function_exists($func) ? "✅ Available" : "❌ Missing") . "<br>";
}

echo "<h2>Array Syntax Test:</h2>";
$test_array = array('test' => 'value');
echo "Array syntax: " . (is_array($test_array) ? "✅ Working" : "❌ Failed") . "<br>";

echo "<h2>Session Test:</h2>";
if (session_id() == '') {
    session_start();
}
echo "Session started: " . (session_id() != '' ? "✅ Yes" : "❌ No") . "<br>";

echo "<h2>Filter Test:</h2>";
$test_email = 'test@example.com';
echo "Email filter: " . (filter_var($test_email, FILTER_VALIDATE_EMAIL) !== false ? "✅ Working" : "❌ Failed") . "<br>";

echo "<h2>Regex Test:</h2>";
$test_phone = '+7 (999) 999-99-99';
$phone_regex = '/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/';
echo "Phone regex: " . (preg_match($phone_regex, $test_phone) ? "✅ Working" : "❌ Failed") . "<br>";

echo "<h2>Sort Test:</h2>";
$test_array = array(3, 1, 4, 1, 5);
usort($test_array, 'compareNumbers');
function compareNumbers($a, $b) {
    if ($a == $b) return 0;
    return ($a < $b) ? -1 : 1;
}
echo "Array sort: " . (implode(',', $test_array) == '1,1,3,4,5' ? "✅ Working" : "❌ Failed") . "<br>";

echo "<h2>Overall Compatibility:</h2>";
$all_good = version_compare(PHP_VERSION, '5.3.0', '>=') && 
            function_exists('session_start') && 
            function_exists('filter_var') && 
            function_exists('preg_match');

echo $all_good ? "✅ <strong>All systems compatible with PHP 5.3+</strong>" : "❌ <strong>Some issues detected</strong>";

echo "<br><br><a href='test.php'>Run Full Test</a> | <a href='index.php'>Go to Main Site</a>";
?>
