from flask import Flask , request
from flask_restful import Resource, Api

app=Flask(__name__)
api= Api(app)
items =["alvaro","sayen","ximena",3]
#items="hola"


class Item(Resource):

    def get(self,name):
        for item in items:
            if item['name'] == name :
                    return item
        return {'item':None},404


    def post(self,name):
        print("llego datos")
        dato = request.form.to_dict() or {}
        usuario=request.form['user']
        device= request.form['device_reg_id']
        print(dato['user'])
        print(device)
        item={'name':name,'price':120.00}
        items.append(item)
        print(name)
        return item , 20

class ItemList(Resource):
    def get(self):
        return{'items':items}
#TODO aca se debe hacer algo
api.add_resource(Item, '/prueba/<string:name>')
api.add_resource(ItemList, '/items')

app.run(host='0.0.0.0', debug=True,port=5005)