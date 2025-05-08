from flask import Flask, request, jsonify
from flask_cors import CORS
import subprocess

app = Flask(__name__)
CORS(app)

def run_mx_lookup(domain):
    try:
        result = subprocess.run(["nslookup", "-type=mx", domain], capture_output=True, text=True, timeout=10)
        return result.stdout
    except Exception as e:
        return str(e)

@app.route("/mxlookup", methods=["POST"])
def mx_lookup():
    data = request.get_json()
    if not data or "domain" not in data:
        return jsonify({"error": "Please enter a domain name!"}), 400

    domain = data["domain"].strip()
    if not domain:
        return jsonify({"error": "Domain name cannot be empty!"}), 400

    mx_result = run_mx_lookup(domain)
    return jsonify({"result": mx_result})

if __name__ == "__main__":
    app.run(port=5003, debug=True)