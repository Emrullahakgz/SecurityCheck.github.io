from flask import Flask, request, jsonify, send_file
from flask_cors import CORS 
import subprocess
import os

app = Flask(__name__, static_folder='css')
CORS(app)

@app.route('/')
def home():
    return send_file(os.path.join(os.getcwd(), "network.php")) 

@app.route('/scan', methods=['POST'])
def scan():
    target = request.json.get('target')

    if not target:
        return jsonify({"error": "Target IP or domain is required"}), 400

    try:
        result = subprocess.run(['nmap', '-sV', target], capture_output=True, text=True)
        return jsonify({"result": result.stdout})  
    except Exception as e:
        return jsonify({"error": str(e)}), 500

if __name__ == '__main__':
    app.run(debug=True)



# ----------------------------------------------ÜST TARAF NMAP ENTEGRASYONU--------------------------------------------------------