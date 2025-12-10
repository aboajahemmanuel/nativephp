<?php

// Define credentials and path
$server = '10.10.15.59';
$username = 'FMDQGROUP\aboajah.emmanuel'; // Change to your admin username
$password = '@FMDQdev6';   // Change to your password
$uncPath = "\\\\{$server}\\c$\\xampp\\htdocs\\fmrr";

$logFile = __DIR__ . '/deploy-log.txt';

// Create log file
$logContent = "\n" . str_repeat('=', 50) . "\n";
$logContent .= "Deploy started: " . date('Y-m-d H:i:s') . "\n";
$logContent .= "Target: {$uncPath}\n";
$logContent .= str_repeat('=', 50) . "\n\n";
file_put_contents($logFile, $logContent, FILE_APPEND);

echo "Attempting to connect to network share...<br>";

// Authenticate to the network share
$netUseCommand = "net use \\\\{$server}\\c$ /user:{$username} {$password} 2>&1";
exec($netUseCommand, $netOutput, $netReturn);

$logContent = "Network authentication attempt:\n";
$logContent .= implode("\n", $netOutput) . "\n";
$logContent .= "Return code: {$netReturn}\n\n";
file_put_contents($logFile, $logContent, FILE_APPEND);

if ($netReturn !== 0) {
    echo "✗ Failed to connect to network share:<br>";
    echo "<pre>" . htmlspecialchars(implode("\n", $netOutput)) . "</pre>";
    echo "Please check:<br>";
    echo "- Username and password are correct<br>";
    echo "- Remote server allows admin share access<br>";
    echo "- Network connectivity to {$server}<br>";
    exit;
}

echo "✓ Network share connected successfully<br><br>";

// Clean and normalize the path
$projectPath = rtrim(str_replace('/', '\\', $uncPath), "\\/");
if (!str_starts_with($projectPath, '\\\\')) {
    $projectPath = '\\\\' . ltrim($projectPath, '\\');
}

// Check if project directory exists
if (!is_dir($projectPath)) {
    $errorMsg = "✗ ERROR: Project directory does not exist: {$projectPath}\n";
    file_put_contents($logFile, $errorMsg, FILE_APPEND);
    echo "<strong style='color: red;'>" . htmlspecialchars($errorMsg) . "</strong>";
    
    // Try to list parent directory
    $parentDir = dirname($projectPath);
    if (is_dir($parentDir)) {
        echo "Available items in parent directory:<br>";
        $items = @scandir($parentDir);
        if ($items) {
            echo "<ul>";
            foreach ($items as $item) {
                if ($item != '.' && $item != '..') {
                    echo "<li>" . htmlspecialchars($item) . "</li>";
                }
            }
            echo "</ul>";
        }
    }
    exit;
}

// Check if directory is writable
if (!is_writable($projectPath)) {
    $errorMsg = "✗ ERROR: Project directory is not writable: {$projectPath}\n";
    file_put_contents($logFile, $errorMsg, FILE_APPEND);
    echo "<strong style='color: red;'>" . htmlspecialchars($errorMsg) . "</strong>";
    exit;
}

$safeDir = str_replace('\\', '/', $projectPath);

// Test file to verify script can write to project directory
$testFile = $projectPath . '\\deployment-test-' . date('Y-m-d-His') . '.txt';
$testContent = "Deployment script executed successfully at " . date('Y-m-d H:i:s') . "\n";
$testContent .= "Script location: " . __FILE__ . "\n";

if (@file_put_contents($testFile, $testContent) !== false) {
    $logContent = "✓ Test file created: {$testFile}\n\n";
    file_put_contents($logFile, $logContent, FILE_APPEND);
    echo "✓ Test file created successfully<br><br>";
} else {
    $error = error_get_last();
    $logContent = "✗ Failed to create test file: " . ($error['message'] ?? 'Unknown error') . "\n\n";
    file_put_contents($logFile, $logContent, FILE_APPEND);
    echo "✗ Failed to create test file: " . htmlspecialchars($error['message'] ?? 'Unknown error') . "<br><br>";
    exit;
}

// Define commands with descriptions
$steps = [
    [
        'description' => 'Configuring Git safe directory',
        'command' => "git config --global --add safe.directory {$safeDir}"
    ],
    [
        'description' => 'Clearing application cache',
        'command' => "cd /d \"{$projectPath}\" && php artisan cache:clear"
    ],
    [
        'description' => 'Caching configuration',
        'command' => "cd /d \"{$projectPath}\" && php artisan config:cache"
    ],
    [
        'description' => 'Caching routes',
        'command' => "cd /d \"{$projectPath}\" && php artisan route:cache"
    ],
    [
        'description' => 'Clearing all optimization caches',
        'command' => "cd /d \"{$projectPath}\" && php artisan optimize:clear"
    ],
];

// Execute each command and log the results
foreach ($steps as $index => $step) {
    $stepNum = $index + 1;
    $logEntry = "Step {$stepNum}: {$step['description']}\n";
    $logEntry .= "Command: {$step['command']}\n";
    
    file_put_contents($logFile, $logEntry, FILE_APPEND);
    
    // Execute command and capture output
    exec($step['command'] . " 2>&1", $output, $returnVar);
    
    $logEntry = "Output:\n" . implode("\n", $output) . "\n";
    $logEntry .= "Return code: {$returnVar}\n";
    $logEntry .= ($returnVar === 0 ? "✓ Success\n" : "✗ Failed\n");
    $logEntry .= str_repeat('-', 50) . "\n\n";
    
    file_put_contents($logFile, $logEntry, FILE_APPEND);
    
    // Clear output array for next command
    $output = [];
    
    // Optional: Stop on error
    if ($returnVar !== 0) {
        $errorMsg = "Deployment stopped due to error in step {$stepNum}\n";
        file_put_contents($logFile, $errorMsg, FILE_APPEND);
        echo "Deployment failed at step {$stepNum}. Check log for details.<br>";
        break;
    }
}

// Disconnect from network share (optional cleanup)
exec("net use \\\\{$server}\\c$ /delete 2>&1");

// Final summary
$summary = str_repeat('=', 50) . "\n";
$summary .= "Deploy completed: " . date('Y-m-d H:i:s') . "\n";
$summary .= str_repeat('=', 50) . "\n";
file_put_contents($logFile, $summary, FILE_APPEND);

echo "Deployment completed.<br>";
if (file_exists($logFile)) {
    echo "<pre>" . htmlspecialchars(file_get_contents($logFile)) . "</pre>";
}