<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=memon_nimko', 'root', '');
    $stmt = $pdo->query('SHOW TABLES');
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Total Tables: " . count($tables) . "\n\n";
    echo "Populated Tables:\n";
    echo "----------------------------------------\n";
    foreach ($tables as $table) {
        $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
        if ($count > 0) {
            printf("%-35s: %d rows\n", $table, $count);
        }
    }
    
    echo "\nSample Users:\n";
    echo "----------------------------------------\n";
    $users = $pdo->query("SELECT id, name, email FROM users")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($users as $u) {
        echo "ID: {$u['id']} | Name: {$u['name']} | Email: {$u['email']}\n";
    }

    echo "\nSample Roles:\n";
    echo "----------------------------------------\n";
    $roles = $pdo->query("SELECT id, name FROM roles")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($roles as $r) {
        echo "ID: {$r['id']} | Name: {$r['name']}\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
