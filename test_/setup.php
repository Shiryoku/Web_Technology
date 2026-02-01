<?php
$host = 'localhost';
$username = 'root';
$password = ''; // Default XAMPP

try {
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = file_get_contents('database.sql');

    // Split into individual queries (basic split by semicolon)
    // This is a naive split but works for the provided simple SQL
    //$pdo->exec($sql); 
    // Better compatibility:
    $pdo->exec($sql);

    echo "Database created and seeded successfully! You can now delete this file and <a href='index.php'>Go to Home</a>.";
} catch (PDOException $e) {
    echo "Error creating database: " . $e->getMessage();
}
?>