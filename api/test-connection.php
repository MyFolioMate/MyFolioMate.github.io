<?php
require_once 'config/Connection.php';

try {
    $db = new Connection();
    $conn = $db->connect();
    
    if ($conn) {
        echo "Connection successful!\n";
        
        // Test a simple query
        $stmt = $conn->query("SELECT 1");
        if ($stmt) {
            echo "Query test successful!\n";
        }
        
        // Print connection info
        echo "\nConnection Details:\n";
        echo "Host: " . $_ENV['DB_HOST'] . "\n";
        echo "Database: " . $_ENV['DB_NAME'] . "\n";
        echo "User: " . $_ENV['DB_USER'] . "\n";
        
        // Test if we can get server info
        echo "\nServer Info: " . $conn->getAttribute(PDO::ATTR_SERVER_INFO) . "\n";
        echo "Server Version: " . $conn->getAttribute(PDO::ATTR_SERVER_VERSION) . "\n";
    } else {
        echo "Connection returned null\n";
    }
} catch (PDOException $e) {
    echo "PDO Error Details:\n";
    echo "Error Code: " . $e->getCode() . "\n";
    echo "Error Message: " . $e->getMessage() . "\n";
    
    // Additional error information
    if ($e->getCode() === 1045) {
        echo "\nAccess denied error detected. Please check:\n";
        echo "1. Username is correct\n";
        echo "2. Password is correct\n";
        echo "3. User has proper permissions\n";
        echo "4. User is allowed to connect from this IP\n";
    }
} catch (Exception $e) {
    echo "General Error: " . $e->getMessage() . "\n";
}

// Also verify the environment variables are loaded
echo "\nEnvironment Variables Check:\n";
echo "DB_HOST set: " . (isset($_ENV['DB_HOST']) ? 'Yes' : 'No') . "\n";
echo "DB_NAME set: " . (isset($_ENV['DB_NAME']) ? 'Yes' : 'No') . "\n";
echo "DB_USER set: " . (isset($_ENV['DB_USER']) ? 'Yes' : 'No') . "\n";
echo "DB_PASS set: " . (isset($_ENV['DB_PASS']) ? 'Yes' : 'No') . "\n"; 