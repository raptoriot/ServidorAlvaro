#este ejemṕlo viene del curso "creating our apploication endpoints" de oreally
#2 octubre de 2019
#este es una api para crear tiendas o stores con items para cada store

from flask import Flask,jsonify,request
app= Flask(__name__)

stores=[
    {
        'name':'My Wonderful Store',
        'items':[
            {'name':'Mylibro','price':16.99},
            {'name':'libro2','price':21.99}
        ]
    }
]
#aca crea una tienda nueva con item vacios
#POST /store data: {name:}
@app.route('/store',methods=['POST'])
def create_store():
    print("llego post")
    request_data=request.get_json()
    new_store={'name':request_data['name'],'items':[]}
    stores.append(new_store)
    return jsonify(new_store)

#aca consulta por la existencia de una tienda
#GET /store/<string:name>
@app.route('/store/<string:name>')
def get_store(name):
    print("entro a la tienda")
    for store in stores:
        if store['name']==name:
            return jsonify(store)
    return jsonify({'message': 'store not found'})

#GET /store
@app.route('/store')
def get_stores():
    return jsonify({'stores':stores})

# POST /store/(<string:name>/item {name:,price:}
@app.route('/store/<string:name>/item',methods=['POST'])
def create_item_in_store(name):
    request_data = request.get_json()
    for store in stores:
        if store['name']==name:

            new_item={'name':request_data['name'],'price':request_data['price']}
            store['items'].append(new_item)
            return jsonify(new_item)

    return jsonify({'message': 'store not found'})


#aca solicita los item de una tienda en particular
# GET /store/<string:name>/item
@app.route('/store/<string:name>/item')
def get_items_in_store(name):
    for store in stores:
        if store['name'] == name:
            return jsonify({'items':store['items']})
    return jsonify({'message': 'store not found'})
@app.route('/')
def home():
    return "esta es la api de prueba"


app.run(port=5002,host='0.0.0.0')