from flask import Flask, request, jsonify
from flask_cors import CORS
import subprocess

app = Flask(__name__)
CORS(app)
DNSBL_LIST = [
    'bl.spamcop.net',
    'dnsbl.sorbs.net',
    'b.barracudacentral.org',
    'cbl.abuseat.org'
]

def check_dnsbl(domain_or_ip):
    results = {}
    
    for dnsbl in DNSBL_LIST:
        try:
            result = subprocess.run(
                ['nslookup', domain_or_ip + '.' + dnsbl],
                capture_output=True, text=True, timeout=10
            )
            if "NXDOMAIN" in result.stdout:
                results[dnsbl] = "Not blacklisted"
            else:
                results[dnsbl] = "May be blacklisted"
        except subprocess.TimeoutExpired:
            results[dnsbl] = "Timeout"
        except Exception as e:
            results[dnsbl] = f"An error occurred:{str(e)}"
    
    return results

@app.route('/blacklist', methods=['POST'])
def blacklist_check():
    data = request.get_json()

    if 'domain_or_ip' not in data:
        return jsonify({"error": "Please server ıp or domain name !"}), 400

    domain_or_ip = data['domain_or_ip'].strip()

    result = check_dnsbl(domain_or_ip)
    
    return jsonify({"domain_or_ip": domain_or_ip, "results": result})

if __name__ == '__main__':
    app.run(debug=True, port=5004)