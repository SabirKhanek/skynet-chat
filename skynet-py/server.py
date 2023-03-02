import os
from flask import abort, current_app, make_response, request, Flask
from flask_cors import CORS
import json
from chatbot import generate_response
from datetime import datetime

app = Flask(__name__)
CORS(app)

# datetime object containing current date and time
now = datetime.now()

print(f"{__file__}: Server Started on: ", now)

app = Flask(__name__)
CORS(app)

pid = os.getpid()
print("PID: ", pid)
with open('server.pid', 'w') as f:
    f.write(str(pid))


@app.route("/", methods=["GET"])
def index():
    re = {"status": "sky_success", "message": "Skynet Server is running"}

    return make_response(json.dumps(re, indent=4), 200)


@app.route("/api/generate_response", methods=["GET", "POST"])
def g_resp():
    req = request.get_json()
    if 'text' not in req.keys():
        return make_response(json.dumps({"status": "error", "message": "No text found in the request"}), 400)

    text = req['text']
    if not text:
        return make_response(json.dumps({"status": "error", "message": "Invalid text"}), 400)
    
    

    resp = generate_response(req['pastMessages'])

    re = {"status": "sky_success", "message": resp}
    print(f"Request: {text}\nResponse: {resp}")
    return make_response(json.dumps(re, indent=4), 200)



if __name__ == "__main__":
    # app.run(host='0.0.0.0')
    app.run(host='0.0.0.0', port='6929')
