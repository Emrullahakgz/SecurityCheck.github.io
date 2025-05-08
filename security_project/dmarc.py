from flask import Flask, request, jsonify
import dns.resolver
from flask_cors import CORS

app = Flask(__name__)
CORS(app)

def check_dmarc(domain):
    try:
        result = dns.resolver.resolve('_dmarc.' + domain, 'TXT')
        for rdata in result:
            return str(rdata) 
        return "DMARC record not found!"
    except (dns.resolver.NoAnswer, dns.resolver.NXDOMAIN):
        return "DMARC record not found!"
    except Exception as e:
        return str(e)

@app.route("/dmarc", methods=["POST"])
def dmarc_lookup():
    data = request.get_json()
    if not data or "domain" not in data:
        return jsonify({"error": "Please enter a domain name!"}), 400

    domain = data["domain"].strip()
    if not domain:
        return jsonify({"error": "Domain name cannot be empty!"}), 400

    dmarc_result = check_dmarc(domain)
    return jsonify({"result": dmarc_result})

if __name__ == "__main__":
    app.run(port=5005, debug=True)
