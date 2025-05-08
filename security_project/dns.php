<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>DNS Lookup</title>
    <link rel="stylesheet" href="css/who.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body>
    <div class="container">
        <nav class="sidebar">
            <ul>
                <li><a href="index.php"><i class="fa-solid fa-house-user fa-beat"></i> Homepage</a></li>
                <li><a href="network.php"><i class="fa-solid fa-wifi fa-beat"></i> Network Scanner</a></li>
                <li><a href="mx.php"><i class="fa-solid fa-user-secret fa-beat"></i> MX Lookup</a></li>
                <li><a href="whois.php"><i class="fa-solid fa-fingerprint fa-beat"></i> Whois Lookup</a></li>
                <li><a href="black.php"><i class="fa-solid fa-magnifying-glass fa-beat"></i> Blacklist Check</a></li>
                <li><a href="dmarc.php"><i class="fa-solid fa-envelope fa-beat"></i> Dmarc Lookup</a></li>
                <li><a href="dns.php"><i class="fa-solid fa-globe fa-beat"></i> DNS Lookup</a></li>
            </ul>
        </nav>

        <section id="anasayfa">
            <div class="content">
                <h1><i class="fa-solid fa-user-shield"></i> SECURITY CHECK</h1>
                <p>Security is important.</p>
            </div>
            <div id="icerik">
                <form method="post">
                    <h1>DNS Lookup Query</h1>
                    <input type="text" name="domain" placeholder="Enter a domain name...">
                    <button type="submit">Query</button>
                    <h3>Results:</h3>
                    <pre id="sonuc">
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['domain'])) {
    $domain = trim($_POST['domain']);
    $data = json_encode(['domain' => $domain]);

    $ch = curl_init('http://127.0.0.1:5002/dns');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $response = curl_exec($ch);
    curl_close($ch);

    if ($response) {
        $json = json_decode($response, true);
        if (isset($json['result'])) {
            echo "A Records:\n";
            $resultStr = "";
            foreach ($json['result'] as $ip) {
                echo "- $ip\n";
                $resultStr .= $ip . "\n"; 
            }

            $postData = http_build_query([
                'domain' => $domain,
                'result' => $resultStr
            ]);
            
            $ch_backend = curl_init('http://localhost/security_project/dns_backend.php'); 
            curl_setopt($ch_backend, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch_backend, CURLOPT_POST, true);
            curl_setopt($ch_backend, CURLOPT_POSTFIELDS, $postData);
            $backend_response = curl_exec($ch_backend);
            curl_close($ch_backend);
        } else {
            echo "Error: " . ($json['error'] ?? 'Unknown error occurred.');
        }
    } else {
        echo "Failed to connect to DNS API.";
    }
}
?>
                    </pre>
                </form>
            </div>
        </section>
    </div>
</body>

</html>