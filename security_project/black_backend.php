
<?php
header('Content-Type: application/json');

$host = "localhost";
$user = "root";
$password = "";  
$database = "security_database";

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['domain_or_ip']) || !isset($data['results'])) {
    echo json_encode(["error" => "Eksik veri!"]);
    exit;
}
$domain_or_ip = $data['domain_or_ip'];
$results = json_encode($data['results'], JSON_UNESCAPED_UNICODE); 

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    echo json_encode(["error" => "Veritabanı bağlantı hatası: " . $conn->connect_error]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO blacklist_results (domain_or_ip, result, created_at) VALUES (?, ?, NOW())");
$stmt->bind_param("ss", $domain_or_ip, $results);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Kayıt başarıyla eklendi."]);
} else {
    echo json_encode(["error" => "Veritabanına eklenemedi: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>