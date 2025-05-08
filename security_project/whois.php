<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SECURITY CHECK</title>
    <link rel="stylesheet" href="css/who.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">     
</head> 
<body>
    <div class="container">
        <nav class="sidebar">
            <ul>
                <li><a href="index.php"><i class="fa-solid fa-house-user fa-beat" style="color: #e8eaed;"></i> Homepage</a></li>
                <li><a href="network.php"><i class="fa-solid fa-wifi fa-beat fa-sm" style="color: #e4e3e8;"></i> Network Scanner</a></li>
                <li><a href="mx.php"><i class="fa-solid fa-user-secret fa-beat" style="color: #e4e3e8;"></i> MX Lookup</a></li>
                <li><a href="whois.php"><i class="fa-solid fa-fingerprint fa-beat" style="color: #e8eaee;"></i> Whois Lookup</a></li>
                <li><a href="black.php"><i class="fa-solid fa-magnifying-glass fa-beat" style="color: #dee2e8;"></i> Blacklist Check</a></li>
                <li><a href="dmarc.php"><i class="fa-solid fa-envelope fa-beat" style="color: #e6e9ef;"></i> Dmarc Lookup</a></li>
                <li><a href="dns.php"><i class="fa-solid fa-globe fa-beat" style="color: #e8eaed;"></i> DNS Lookup</a></li>
            </ul>
        </nav>

        <section id="anasayfa">
            <div class="content">
                <h1><i class="fa-solid fa-user-shield"></i> SECURITY CHECK</h1>
                <p>Security is important.</p>
            </div>

            <div id="icerik">
                <h1>Whois Query</h1>
                <input type="text" id="domain" placeholder="Enter a domain name...">
                <button onclick="whoisSorgula()">Query</button>
                <h3>Results:</h3>
                <pre id="sonuc"></pre>
            </div>
        </section>
    </div>

    <script>
    function whoisSorgula() {
        var domain = document.getElementById("domain").value;
        if (!domain) {
            alert("Please enter a domain name!");
            return;
        }

        fetch("http://127.0.0.1:5001/whois", {
            method: "POST",
            mode: "cors",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ domain: domain })
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                document.getElementById("sonuc").textContent = "Error: " + data.error;
            } else {
                document.getElementById("sonuc").textContent = formatWhoisResult(data);

                fetch("whois_backend.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        domain: domain,
                        result: data
                    })
                })
                .then(res => res.json())
                .then(dbResponse => console.log("Veritabanı yanıtı:", dbResponse))
                .catch(err => console.error("Veritabanı hatası:", err));
            }
        })
        .catch(error => {
            document.getElementById("sonuc").textContent = "An error occurred: " + error;
        });
    }

    function formatWhoisResult(data) {
        let formatted = "";
        for (let key in data) {
            if (Array.isArray(data[key])) {
                formatted += `${key}: ${data[key].join(", ")}\n`;
            } else {
                formatted += `${key}: ${data[key]}\n`;
            }
        }
        return formatted;
    }
    </script>
</body>
</html>