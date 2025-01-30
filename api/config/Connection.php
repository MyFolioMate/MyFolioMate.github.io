<?php
require_once __DIR__ . '/../vendor/autoload.php';

try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
} catch (Exception $e) {
    error_log("Error loading .env file: " . $e->getMessage());
    throw $e;
}

class Connection {
    private $host;
    private $dbname;
    private $user;
    private $pass;
    
    public function __construct() {
        $this->host = $_ENV['DB_HOST'] ?? null;
        $this->dbname = $_ENV['DB_NAME'] ?? null;
        $this->user = $_ENV['DB_USER'] ?? null;
        $this->pass = $_ENV['DB_PASS'] ?? null;
        
        // Debug information
        error_log("DB Config - Host: " . ($this->host ?? 'null'));
        error_log("DB Config - Name: " . ($this->dbname ?? 'null'));
        error_log("DB Config - User: " . ($this->user ?? 'null'));
        
        // Validate configuration
        if (!$this->host || !$this->dbname || !$this->user) {
            throw new Exception("Missing database configuration. Please check your .env file. Values: " . 
                              "HOST=" . ($this->host ?? 'null') . ", " .
                              "DB=" . ($this->dbname ?? 'null') . ", " .
                              "USER=" . ($this->user ?? 'null'));
        }
    }

    public function connect() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_TIMEOUT => 5, // 5 seconds timeout
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ];
            
            $conn = new PDO($dsn, $this->user, $this->pass, $options);
            return $conn;
        } catch(PDOException $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            error_log("DSN: mysql:host={$this->host};dbname={$this->dbname}");
            error_log("User: {$this->user}");
            throw $e; // Re-throw the exception for handling upstream
        }
    }
}