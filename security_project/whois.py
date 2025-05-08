from flask import Flask, request, jsonify
from flask_cors import CORS
import subprocess
import re

app = Flask(__name__)
CORS(app, resources={r"/*": {"origins": "*"}})  

def run_whois_lookup(domain):
    try:
        result = subprocess.run(["whois", domain], capture_output=True, text=True, timeout=10)
        whois_output = result.stdout

        parsed_data = parse_whois_output(whois_output)
        return parsed_data
    except Exception as e:
        return {"error": str(e)}

def parse_whois_output(output):
    """WHOIS metin çıktısını JSON formatına dönüştüren fonksiyon"""
    data = {}
    

    patterns = {
        "Domain Name": r"Domain Name:\s*(.+)",
        "Registrar": r"Registrar:\s*(.+)",
        "Registrar URL": r"Registrar URL:\s*(.+)",
        "Creation Date": r"Creation Date:\s*(.+)",
        "Updated Date": r"Updated Date:\s*(.+)",
        "Registrant Country": r"Registrant Country:\s*(.+)",
        "Registry Expiry Date": r"Registry Expiry Date:\s*(.+)", 
        "Reseller": r"Reseller:\s*(.+)", 
        "Name Servers": r"Name Server:\s*(.+)"
    }
    
    data["Name Servers"] = []

    for key, pattern in patterns.items():
        matches = re.findall(pattern, output, re.IGNORECASE)
        if matches:
            if key == "Name Servers":
                data[key] = matches  
            else:
                data[key] = matches[0]  

    return data

@app.route("/whois", methods=["POST"])
def whois_lookup():
    data = request.get_json()
    if not data or "domain" not in data:
        return jsonify({"error": "Please enter a domain name!"}), 400

    domain = data["domain"].strip()
    if not domain:
        return jsonify({"error": "Domain name cannot be empty!"}), 400

    whois_result = run_whois_lookup(domain)

    return jsonify(whois_result)  

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5001, debug=True)