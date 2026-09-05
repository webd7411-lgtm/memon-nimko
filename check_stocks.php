<?php
$pdo = new PDO('mysql:host=localhost;dbname=memonnimko;charset=utf8mb4', 'root', '');
$stmt = $pdo->query('DESCRIBE stocks');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
