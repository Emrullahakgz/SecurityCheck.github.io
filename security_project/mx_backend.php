
<?php
header('Content-Type: application/json');

$host = 'localhost';
$dbname = 'security_database';
$username = 'root';
$password = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!isset($data['domain']) || !isset($data['result'])) {
        echo json_encode(["error" => "Missing required data"]);
        exit;
    }

    $domain = $data['domain'];
    $result = $data['result'];

    $stmt = $pdo->prepare("INSERT INTO mx_results (domain, result, created_at) VALUES (?, ?, NOW())");
    $stmt->execute([$domain, $result]);

    echo json_encode(["success" => true, "message" => "Data saved successfully"]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>