from flask import Flask, request, jsonify
from sentence_transformers import SentenceTransformer

app = Flask(__name__)
model = SentenceTransformer("all-MiniLM-L6-v2")

@app.route("/generate", methods=["POST"])
def generate():
    data = request.json
    text = data["text"]
    embedding = model.encode(text).tolist()
    return jsonify({"embedding": embedding})

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000)
