from flask import Flask , request,jsonify
from flask_restful import Resource, Api
import mysql.connector
import json
import base64

app=Flask(__name__)
api= Api(app)
items =[]

db = mysql.connector.connect(host="127.0.0.1",user="root",passwd="mg45TVwer",db="bsa_registro_maquinas_gc",charset = "utf8" )        # el nombre de la base de datos

#respuesta={'estado':'ok','edad':22,'cursos':['python','flask']}

# Varificar si existe una llave
def checkKey(dict, key):
    if key in dict:
        return True
    else:
        return False

def valores(dicc):
    dato=dicc
    query=dato['query']
    device_id=dato['device_id']
    user=dato['user']
    respuesta = {'status': 'error'}
    print("bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb")
    print(query)


    if(query == '0'):#Ping si esta online el ws
        respuesta = {'status': 'ok'}

    if (query == '1'):# revisa en la bd si esta el usuario
        cur = db.cursor()
        consulta_1= " SELECT id,nombre FROM usuarios WHERE email = '" + user + "' AND activo = '1' LIMIT 1"
        print(consulta_1)
        cur.execute(consulta_1)
        estado=0
        for row in cur.fetchall():
            print(str(row[0]) + "  " + str(row[1]))
            estado=1
        cur.close()

        if estado==1:
            respuesta['status']='ok'
            respuesta['nombre']=row[1]
            respuesta['id'] = row[0]
        print(" ")

    if (query == '2'):#despues de logear, deja registro del log
        cur = db.cursor()
        consulta_2="INSERT INTO login_log_app (usuarios,dispositivos,fecha) VALUES ('"+user+"','"+device_id+"',NOW())"
        print(consulta_2)
        cur.execute(consulta_2)
        db.commit()
        cur.close()
        respuesta['status'] = 'ok'

    if (query == '10'):  # crea device
        print("entro a crer dispositivo")
        device_id=dato['device_id']
        print(device_id)

#TODO aca va un if con check login en el api de camilo
        if(len(device_id)>0):
            #     consulta_10="INSERT INTO dispositivos (device_id,".assigned) VALUES (?,NOW())","s",array($device_id))";
            print(device_id)


    if (query == '20'):#solicita la lista de forularios
        cur = db.cursor()
        consulta_20="SELECT id,nombre,definicion,primary_fields FROM formularios WHERE activo = 1"
        print(consulta_20)
        cur.execute(consulta_20)
        contador=1
        print("--------------------------------")
        formularios=[]
        dict2={}
        for row in cur.fetchall():
            #print(str(row[0]) + "----" + str(row[1])+"---"+str(row[2]) + "----"+str(row[3])  )

            print("+++++++++++++")
            #print(variable)
            formularios.append({'id':row[0],'nombre':row[1],'definicion':row[2],'primary_fields':row[3]})
            contador=contador+1
            print("+++++++++++++")
        respuesta['status'] = 'ok'
        respuesta['formularios'] =formularios

        cur.close()

    if (query == '30'): #("SYNC_REGISTROS_DATA_FROM_DEVICE",30);
        print(dato)
        if (dato['extra']):
            print("rquery 30")
            print(dato['extra'])

            dic2 = dato['extra']
            obj = json.loads(dic2)
            print(".......................................")
            if(checkKey(dato,'device_reg_id')):
                dispositivos = dato['device_reg_id']
            if (checkKey(dato, 'device_reg_id')==False):
                dispositivos=None
            print(dispositivos)

            if (checkKey(dato, 'user')):
                usuarios = dato['user']
            if (checkKey(dato, 'user')==False):
                usuarios=None
            print(usuarios)

            if (checkKey(dato, 'extra')):
                extra = dato['extra']
            if (checkKey(dato, 'extra')==False):
                extra=None
            print(extra)
            print(".......................................")
#Ahora los datos de extra
            if (checkKey(obj, 'formularios')):
                formularios = obj['formularios']
            if (checkKey(obj, 'formularios')==False):
                formularios=None
            print(formularios)

            if (checkKey(obj, 'android_bd_id')):
                android_bd_id = obj['android_bd_id']
            if (checkKey(obj, 'android_bd_id')==False):
                android_bd_id=None
            print(android_bd_id)

            if (checkKey(obj, 'fecha')):
                fecha = obj['fecha']
            if (checkKey(obj, 'fecha') == False):
                fecha = None
            print(fecha)

            if (checkKey(obj, 'alerta_nivel')):
                alerta_nivel = obj['alerta_nivel']
            if (checkKey(obj, 'alerta_nivel') == False):
                alerta_nivel = None
            print(alerta_nivel)

            if (checkKey(obj, 'latitud')):
                latitud  = obj['latitud']
            if (checkKey(obj, 'latitud') == False):
                latitud  = None
            print(latitud )

            if (checkKey(obj, 'longitud')):
                longitud = obj['longitud']
            if (checkKey(obj, 'longitud') == False):
                longitud = None
            print(longitud)

            if (checkKey(obj, 'rondas_uuid')):
                rondas_uuid = obj['rondas_uuid']
            if (checkKey(obj, 'rondas_uuid') == False):
                rondas_uuid= None
            print(rondas_uuid)

            if (checkKey(obj, 'datos')):
                agr = len(obj['datos']) - (len(obj['datos']) // 4) * 4
                datos = base64.b64decode(obj['datos']+agr*"=").decode('utf-8')
            if (checkKey(obj, 'datos') == False):
                datos = None
            print(datos)


            print(".......................................")
            #En el original se Hce un replace a la tabla registros, yo voy a insertar en una nueva tabla registros 2
            consulta30_1="android_bd_id,dispositivos,formularios,usuarios,fecha,datos,alerta_nivel"
            cur = db.cursor() 
            consulta30="REPLACE INTO registros2 (android_bd_id,dispositivos,formularios,usuarios,fecha,datos,alerta_nivel) VALUES ('"+android_bd_id+"','"+dispositivos+"','"+formularios+"','"+usuarios+"','"+fecha+"','"+datos+"','"+alerta_nivel+"')"
            cur.execute(consulta30)
            db.commit()
            cur.close()
            respuesta['status'] = 'ok'

    if (query == '31'):  # sincronizar datos de rondas, aca viene un dato extra en string, pero forma de diccionario, revisar que sucede cuando no viene algun valor,
        if(dato['extra']):
             print("rquery 31")
             print(dato['extra'])
             dic2=dato['extra']
             obj = json.loads(dic2)

             android_bd_id=obj['android_bd_id']



             dispositivos = obj['dispositivos']
             usuarios = obj['usuarios']
             fecha = obj['fecha']
             uuid = obj['uuid']
             synced = obj['synced']
             comentario=""

             if(synced=='0'):
                 cur = db.cursor()
                 consulta_31_0 = "REPLACE INTO rondas (android_bd_id,dispositivos,usuarios,fecha,comentario,uuid) VALUES ('"+android_bd_id+"','"+dispositivos+"','"+usuarios+"','"+fecha+"','"+comentario+"','"+uuid+"')"
                 print(consulta_31_0)
                 cur.execute(consulta_31_0)
                 db.commit()
                 cur.close()
                 respuesta['status'] = 'ok'



             else:
                 cur = db.cursor()
                 consulta_31_1 = "UPDATE rondas SET comentario ='"+comentario+"' WHERE uuid ='"+uuid+"' LIMIT 1"
                 print(consulta_31_1)
                 cur.execute(consulta_31_1)
                 db.commit()
                 cur.close()
                 respuesta['status'] = 'ok'




    return respuesta

class Item(Resource):
    def get(self):
        items ='"hola","nada"'
        print("llego a GET")
        return items

    def post(self):
        print("llego post")
        dato = request.form.to_dict() or {}
        respuesta=valores(dato)
        print(respuesta)
        return jsonify(respuesta)


class ItemList(Resource):
    def get(self):
        print ("")

api.add_resource(Item, '/wsNuevo')
api.add_resource(ItemList, '/items')

app.run(host='0.0.0.0', debug=True,port=5005)