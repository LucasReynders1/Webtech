<?php
// Database configuration
$host = 'db';
$db = 'your_database';
$user = 'your_user';
$pass = 'your_password';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// Path to the JSON file
$jsonFilePath = '/usr/src/app/cpu_freq.json';

// Check if JSON file exists
if (!file_exists($jsonFilePath)) {
    die("JSON file not found.");
}

// Read the JSON file
$jsonData = file_get_contents($jsonFilePath);
$data = json_decode($jsonData, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die("Error decoding JSON.");
}

// Assuming the JSON structure is {"cpu_freq": 2200}
$cpuFreq = $data['cpu_freq'];

// Insert or update the database
$sql = "INSERT INTO cpu_data (id, cpu_freq) VALUES (1, :cpu_freq)
        ON DUPLICATE KEY UPDATE cpu_freq = :cpu_freq";

$stmt = $pdo->prepare($sql);
$stmt->execute(['cpu_freq' => $cpuFreq]);

echo "Database updated successfully.";
?>