<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "security_database";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$domain = $_POST['domain'] ?? '';
$result = $_POST['result'] ?? '';

if (!empty($domain) && !empty($result)) {
    $stmt = $conn->prepare("INSERT INTO dns_results (domain, result, created_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $domain, $result);

    if ($stmt->execute()) {
        echo "Saved successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Missing domain or result.";
}

$conn->close();
?>