from flask import Flask

app=Flask(__name__)

@app.route('/')
def home():
    return "hola mundoooo! y chao"

app.run(host='0.0.0.0', debug=True,port=5001)

