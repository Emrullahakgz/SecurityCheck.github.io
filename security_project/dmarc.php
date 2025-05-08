<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DMARC Lookup</title>
    <link rel="stylesheet" href="css/dmarc.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
    <div class="container">
        <nav class="sidebar">
            <ul>
                <li><a href="index.php"><i class="fa-solid fa-house-user fa-beat"></i> Homepage</a></li>
                <li><a href="network.php"><i class="fa-solid fa-wifi fa-beat fa-sm"></i> Network Scanner</a></li>
                <li><a href="mx.php"><i class="fa-solid fa-user-secret fa-beat"></i> MX Lookup</a></li>
                <li><a href="whois.php"><i class="fa-solid fa-fingerprint fa-beat"></i> Whois Lookup</a></li>
                <li><a href="black.php"><i class="fa-solid fa-magnifying-glass fa-beat"></i> Blacklist Check</a></li>
                <li><a href="dmarc.php"><i class="fa-solid fa-envelope fa-beat"></i> DMARC Lookup</a></li>
                <li><a href="dns.php"><i class="fa-solid fa-globe fa-beat"></i> DNS Lookup</a></li>
            </ul>
        </nav>

        <section id="anasayfa">
            <div class="content">
                <h1><i class="fa-solid fa-user-shield"></i> SECURITY CHECK</h1>
                <p>Security is important.</p>
            </div>

            <div id="icerik">
                <h1>DMARC Query</h1>
                <form method="post">
                    <input type="text" name="domain" id="domain" placeholder="Enter a domain name..." required>
                    <button type="submit">Query</button>
                </form>

                <h3>Results:</h3>
                <pre id="sonuc">
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['domain'])) {
    $domain = trim($_POST['domain']);
    $data = ['domain' => $domain];

    $options = [
        'http' => [
            'header'  => "Content-Type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($data)
        ]
    ];
    $context = stream_context_create($options);
    $response = @file_get_contents('http://127.0.0.1:5005/dmarc', false, $context);

    if ($response !== false) {
        $json = json_decode($response, true);
        if (isset($json['result'])) {
            echo "DMARC Result:\n";
            echo htmlspecialchars($json['result']);

            $saveData = [
                'domain' => $domain,
                'result' => $json['result']
            ];
            $saveOptions = [
                'http' => [
                    'header'  => "Content-Type: application/json\r\n",
                    'method'  => 'POST',
                    'content' => json_encode($saveData)
                ]
            ];
            $saveContext = stream_context_create($saveOptions);
            $backendResponse = @file_get_contents('http://localhost/security_project/dmarc_backend.php', false, $saveContext);

            if ($backendResponse === false) {
                echo "\n[!] Veritabanına kayıt başarısız. Backend'e erişilemedi.";
            }
        } else {
            echo "Error: " . htmlspecialchars($json['error'] ?? 'Unknown error occurred.');
        }
    } else {
        echo "Failed to connect to DMARC API at http://127.0.0.1:5005/dmarc";
    }
}
?>
                </pre>
            </div>
        </section>
    </div>
</body>
</html>