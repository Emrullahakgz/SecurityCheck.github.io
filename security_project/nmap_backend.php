
<?php
$host = "localhost";
$db = "security_database";
$user = "root";
$pass = "";
$charset = "utf8mb4";

$data = json_decode(file_get_contents("php://input"), true);

$domain = $data["domain"] ?? null;
$result = $data["result"] ?? null;
$scan_time = date("Y-m-d H:i:s");

if (!$domain || !$result) {
    echo json_encode(["status" => "error", "message" => "Eksik veri."]);
    exit;
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "INSERT INTO nmap_results (domain, result, scan_time) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$domain, $result, $scan_time]);

    echo json_encode(["status" => "success", "message" => "Veri başarıyla kaydedildi."]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Veritabanı hatası: " . $e->getMessage()]);
}
?>