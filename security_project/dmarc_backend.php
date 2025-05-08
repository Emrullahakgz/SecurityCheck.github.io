
<?php

header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$database = "security_database";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $rawData = file_get_contents("php://input");
    $input = json_decode($rawData, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "JSON parse error."]);
        exit();
    }
    if (!empty($input["domain"]) && !empty($input["result"])) {
        $domain = $conn->real_escape_string($input["domain"]);
        $result = $conn->real_escape_string($input["result"]);
        $sql = "INSERT INTO dmarc_results (domain, result) VALUES ('$domain', '$result')";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(["status" => "success", "message" => "Dmarc result saved to database."]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Database insert error: " . $conn->error]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Invalid input data."]);
    }
} else {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Only POST method allowed."]);
}

$conn->close();
?>