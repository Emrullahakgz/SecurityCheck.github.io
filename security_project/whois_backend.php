
<?php

header("Content-Type: application/json");
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["domain"]) || !isset($data["result"])) {
    echo json_encode(["error" => "Eksik veri."]);
    exit;
}

$domain = $data["domain"];
$result = $data["result"];

$host = "localhost";
$user = "root";  
$pass = "";      
$dbname = "security_database"; 

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    echo json_encode(["error" => "Veritabanı bağlantı hatası: " . $conn->connect_error]);
    exit;
}

$result_json = $conn->real_escape_string(json_encode($result));

$sql = "INSERT INTO whois_results (domain, result_json, created_at) 
        VALUES ('$domain', '$result_json', NOW())";  

if ($conn->query($sql) === TRUE) {
    echo json_encode(["success" => "Veri başarıyla kaydedildi."]);
} else {
    echo json_encode(["error" => "Kayıt başarısız: " . $conn->error]);
}

$conn->close();
?>