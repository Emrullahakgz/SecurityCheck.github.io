from flask import Flask, request, jsonify
from flask_cors import CORS
import dns.resolver  

app = Flask(__name__)
CORS(app)

def run_dns_lookup(domain):
    try:
        result = dns.resolver.resolve(domain, 'A')
        ip_list = [ip.address for ip in result]
        return ip_list
    except Exception as e:
        return str(e)
@app.route("/dns", methods=["POST"])
def dns_lookup():
    data = request.get_json()
    if not data or "domain" not in data:
        return jsonify({"error": "Please enter a domain name!"}), 400

    domain = data["domain"].strip()
    if not domain:
        return jsonify({"error": "Domain name cannot be empty!"}), 400

    dns_result = run_dns_lookup(domain)
    if isinstance(dns_result, list):
        return jsonify({"result": dns_result})
    else:
        return jsonify({"error": dns_result}), 500

if __name__ == "__main__":
    app.run(port=5002, debug=True) 
