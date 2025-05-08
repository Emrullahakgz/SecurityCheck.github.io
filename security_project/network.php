<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SECURITY CHECK</title>
    <link rel="stylesheet" href="css/network.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">     
</head> 
<body>
    <div class="container">
        <nav class="sidebar">
            <ul>
                <li><a href="index.php"><i class="fa-solid fa-house-user fa-beat" style="color: #e8eaed;"></i> Homepage</a></li>
                <li><a href="network.php"><i class="fa-solid fa-wifi fa-beat fa-sm" style="color: #e4e3e8;"></i>   Network Scanner</a></li>
                <li><a href="mx.php"><i class="fa-solid fa-user-secret fa-beat" style="color: #e4e3e8;"></i>  MX Lookup</a></li>
                <li><a href="whois.php"><i class="fa-solid fa-fingerprint fa-beat" style="color: #e8eaee;"></i>  Whois Lookup</a></li>
                <li><a href="black.php"><i class="fa-solid fa-magnifying-glass fa-beat" style="color: #dee2e8;"></i>  Blacklist Check</a></li>
                <li><a href="dmarc.php"><i class="fa-solid fa-envelope fa-beat" style="color: #e6e9ef;"></i>  Dmarc Lookup</a></li>
                <li><a href="dns.php"><i class="fa-solid fa-globe fa-beat" style="color: #e8eaed;"></i>  DNS Lookup</a></li>
            </ul>
        </nav>
        <section id="anasayfa">
            <div class="content">
                <h1><i class="fa-solid fa-user-shield"></i> SECURITY CHECK</h1>
                <p> Security is important.</p>
            </div>
            <div id="icerik">

                <script>
                    function runScan() {
                        const target = document.getElementById("target").value;
                        if (!target) {
                            alert("Please enter a target!");
                            return;
                        }
                        document.getElementById("result").innerText = "Scanning in progress, please wait...";

                        fetch('http://127.0.0.1:5000/scan', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ target: target })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.result) {
                                document.getElementById("result").innerText = data.result;
                                fetch('nmap_backend.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        domain: target,
                                        result: data.result
                                    })
                                })
                                .then(res => res.json())
                                .then(dbResponse => console.log("Veritabanı:", dbResponse));

                            } else {
                                document.getElementById("result").innerText = "Error: " + data.error;
                            }
                        })
                        .catch(error => {
                            console.error("Hata:", error);
                            document.getElementById("result").innerText = "An error occurred during the scan.";
                        });
                    }
                </script>

                <h1><label for="target">Target IP or Domain:</label></h1>
                <input type="text" id="target" placeholder="For example: 192.168.1.1 or example.com">
                <button onclick="runScan()">Scan</button>
                
                <h3>Results:</h3>
                <pre id="result"></pre>
            </div>
        </section>   
    </div>
</body>
</html>