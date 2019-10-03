<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

// apiuser: h
// apipass: A

date_default_timezone_set("America/Santiago");

function local_db_host(){return "localhost";}
function local_db_user(){return "root";}
function local_db_pass(){return "";}
//function local_db_user(){return "root";}
//function local_db_pass(){return "18111979";}
function local_db_name(){return "bsa_registro_maquinas_gc";}
function local_db_port(){return "3306";}

function cloud_db_user(){return "root";}
function cloud_db_pass(){return "vesat18111979";}
function cloud_db_name(){return "bsa_registro_maquinas_gc";}
function cloud_db_socket(){return "/cloudsql/asistente-180018:southamerica-east1:bd-bsa-registro-maquinaria";}

$app_title = "BSA Lo Blanco Parametros de Operaci".chr(243)."n";

// Google
//$google_cloud = TRUE;
//use google\appengine\api\mail\Message;
// No Google
$google_cloud = FALSE;
require '../../PHPMailer/Exception.php';
require '../../PHPMailer/PHPMailer.php';
require '../../PHPMailer/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

$mysql = FALSE;

/*******************************************************************************/

function send_mail($emails,$subject,$body)
{
    $mailsended = false;
    if($GLOBALS['google_cloud'])
    {
       /* try {
            $message = new Message();
            $message->setSender('vesatingenieria@asistente-180018.appspotmail.com');
            $email_list = explode(",",$emails);
            foreach($email_list as $email_addr)
            {
                $message->addTo($email_addr);
            }
            $message->setSubject($subject);
            $message->setHtmlBody($body);
            $message->send();
            $mailsended = true;
        } catch(Exception $e) {}*/
    }
    else
    {
        $mail = new PHPMailer(true);// Passing `true` enables exceptions
        try {
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = 'smtp.gmail.com';                       // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = 'vesatserver@gmail.com';            // SMTP username
            $mail->Password = 'hfyvthiuidglxvsh';                 // SMTP password
            $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 587;                                    // TCP port to connect to
            //Recipients
            $mail->setFrom('vesatserver@gmail.com', "Vesat Ingenier".chr(237)."a");
            $email_list = explode(",",$emails);
            foreach($email_list as $email_addr)
            {
                $mail->addAddress($email_addr);
            }
            //Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->send();
            $mailsended = true;
        } catch (Exception $e) {}
    }
    return true;
}

function conectar_mysql()
{
    if($GLOBALS['google_cloud'])
    {
        /*$GLOBALS['mysql'] = new mysqli(null,cloud_db_user(),cloud_db_pass(),
            cloud_db_name(),null,cloud_db_socket());    */
    }
    else
    {
        $GLOBALS['mysql'] = new mysqli(local_db_host(),local_db_user(),local_db_pass(),
            local_db_name(),local_db_port(),null);
    }
    comando_mysql("SET NAMES utf8");
    comando_mysql("SET CHARACTER SET utf8");
    // yum install php-mbstring
}

/*******************************************************************************/

function mysql_abierto()
{
    return $GLOBALS['mysql'];
}

/*******************************************************************************/

function consulta_mysql($preparedstatement,$types = null,$params = null)
{
    $stmt = $GLOBALS['mysql']->prepare($preparedstatement);
    if(!is_null($types) && !is_null($params) && strlen($types) > 0 && count($params) > 0)
    {
        $paramRef = array();
        $paramRef[]=&$types;
        $psize = count($params);
        for($i = 0;$i < $psize;$i++)
        {
            $params[$i] = $GLOBALS['mysql']->real_escape_string($params[$i]);
            $paramRef[]=&$params[$i];
        }
        call_user_func_array(array($stmt, 'bind_param'), $paramRef);
    }
    $stmt->execute();
    $returnvar = array();
    $result = $stmt->get_result();
    if($result)
    {
        while(($row = $result->fetch_assoc()))
        {
            array_push($returnvar,$row);
        }
    }

    return $returnvar;
}

/*******************************************************************************/

function comando_mysql($preparedstatement,$types = null,$params = null)
{
    $stmt = $GLOBALS['mysql']->
    paramRef
    ($preparedstatement);
    if(!is_null($types) && !is_null($params) && strlen($types) > 0 && count($params) > 0)
    {
        $paramRef = array();
        $paramRef[]=&$types;
        $psize = count($params);
        for($i = 0;$i < $psize;$i++)
        {
            $params[$i] = $GLOBALS['mysql']->real_escape_string($params[$i]);
            $paramRef[]=&$params[$i];
        }
        call_user_func_array(array($stmt, 'bind_param'), $paramRef);
    }
    return $stmt->execute();
}

/*******************************************************************************/

function comando_mysql_return_id($preparedstatement,$types = null,$params = null)
{
    $stmt = $GLOBALS['mysql']->prepare($preparedstatement);
    if(!is_null($types) && !is_null($params) && strlen($types) > 0 && count($params) > 0)
    {
        $paramRef = array();
        $paramRef[]=&$types;
        $psize = count($params);
        for($i = 0;$i < $psize;$i++)
        {
            $params[$i] = $GLOBALS['mysql']->real_escape_string($params[$i]);
            $paramRef[]=&$params[$i];
        }
        call_user_func_array(array($stmt, 'bind_param'), $paramRef);
    }
    if($stmt->execute())
    {
        return $GLOBALS['mysql']->insert_id;
    }
    else
    {
        return null;
    }
}

/*******************************************************************************/

function cerrar_mysql()
{
    $GLOBALS['mysql']->close();
    $GLOBALS['mysql'] = FALSE;
}

/*******************************************************************************/

function check_basic_auth()
{
    // apiuser: h9gau89ioZ12398Zaoe1278oe@@aoeaz!
    // apipass: AOZEOERK01@12euced((oeu09u8ueooaZ
    $headers = getallheaders();
    foreach($headers as $key => $header)
    {
        if($key == "Authorization")
        {
            $nobasic = substr($header,6);
            $decoded = base64_decode($nobasic);
            list($client_user, $client_pass) = explode(':',$decoded);
            if ($client_user == "h" &&
                $client_pass == "A")
            {
                return true;
            }
            else
            {
                return false;
            }
        }
    }
    return false;
}

/*******************************************************************************/

function check_login() // Previamente abrir conexión a bd
{
    $user_id = intval($_POST['user']);
    $hashpass = $_POST['pass'];
    $res = consulta_mysql("SELECT NULL FROM usuarios WHERE id = ? AND hashpass = ? AND ".
        " activo = 1 LIMIT 1",
        "is",array($user_id,$hashpass));
    if(count($res) > 0)
    {
        return true;
    }
    else
    {
        return false;
    }
}

/*******************************************************************************/

function check_device() // Previamente abrir conexión a bd
{
    $device_reg_id = intval($_POST['device_reg_id']);
    $device_id = $_POST['device_id'];
    $res = consulta_mysql("SELECT NULL FROM dispositivos WHERE id = ? AND device_id = ? AND ".
        " activo = 1 LIMIT 1",
        "is",array($device_reg_id,$device_id));
    if(count($res) > 0)
    {
        return true;
    }
    else
    {
        return false;
    }
}

/*******************************************************************************/

define("PING",0);
define("QUERY_LOGIN",1);
define("REGISTER_LOGIN",2);
define("SEND_RECOVERY_CODE",3);
define("CHECK_RECOVERY_CODE",4);
define("CHANGE_PASS_WITH_RECOVERY_CODE",5);
define("CHANGE_PASS_WITH_CURRENT_PASS",6);
define("CREATE_DEVICE",10);
define("GET_FORMULARIOS_LIST",20);
define("SYNC_REGISTROS_DATA_FROM_DEVICE",30);
define("SYNC_RONDAS_DATA_FROM_DEVICE",31);
define("LOG_EXCEPTION",100);

$ret = array();
$ret['status'] = "error";

if(check_basic_auth())
{
    if(array_key_exists('query',$_POST) && array_key_exists('auth',$_POST) &&
        array_key_exists('user',$_POST) && array_key_exists('pass',$_POST) &&
        array_key_exists('device_reg_id',$_POST) && array_key_exists('device_id',$_POST))
    {
        //conectar_mysql();
        //if(mysql_abierto())
        //{
	//	comando_mysql("INSERT INTO rawinfo (fecha,raw) VALUES (NOW(),?)","s",array(print_r($_POST,true)));
	//	cerrar_mysql();
        //}
        $authkeypast = hash("sha512","[".date("Y-m-d",time()-(3*3600))."]");
        $authkeynow = hash("sha512","[".date("Y-m-d")."]");
        $authkeyfuture = hash("sha512","[".date("Y-m-d",time()+(3*3600))."]");
        if(strtoupper($authkeynow) == strtoupper($_POST['auth']) ||
            strtoupper($authkeypast) == strtoupper($_POST['auth']) ||
            strtoupper($authkeyfuture) == strtoupper($_POST['auth']))
        {
            switch(intval($_POST['query']))
            {
                case constant("LOG_EXCEPTION"):
                {
                    conectar_mysql();
                    if(mysql_abierto())
                    {
                        if(check_login() && check_device() && array_key_exists('exceptiondata',$_POST))
                        {
                            $user_id = intval($_POST['user']);
                            $device_reg_id = intval($_POST['device_reg_id']);
                            $exceptiondata = base64_decode($_POST['exceptiondata']);
                            if(comando_mysql("INSERT INTO exceptions (usuarios,dispositivos,fecha,data) VALUES (?,?,NOW(),?)",
                                "sss",array($user_id,$device_reg_id,$exceptiondata)))
                            {
                                $ret['status'] = "ok";
                            }
                        }
                        cerrar_mysql();
                    }
                }
                break;
                case constant("PING"):
                {
                    $ret['status'] = "ok";
                }
                break;
                case constant("QUERY_LOGIN"):
                {
                    conectar_mysql();
                    if(mysql_abierto())
                    {
                        $email = $_POST['user']; // Email solo en este caso
                        $hashpass = $_POST['pass'];
                        $res = consulta_mysql("SELECT id,nombre FROM usuarios WHERE email = ? AND hashpass = ? AND ".
                            " activo = '1' LIMIT 1",
                            "ss",array($email,$hashpass));
                        if(count($res) > 0)
                        {
                            $ret['id'] = $res[0]['id'];
                            $ret['nombre'] = $res[0]['nombre'];
                            $ret['status'] = "ok";
                        }
                        cerrar_mysql();
                    }
                }
                break;
                case constant("REGISTER_LOGIN"):
                {
                    conectar_mysql();
                    if(mysql_abierto())
                    {
                        if(check_login() && check_device())
                        {
                            $user_id = intval($_POST['user']);
                            $device_reg_id = intval($_POST['device_reg_id']);
                            if(comando_mysql("INSERT INTO login_log_app (usuarios,dispositivos,fecha) VALUES (?,?,NOW())",
                                "ss",array($user_id,$device_reg_id)))
                            {
                                $ret['status'] = "ok";
                            }
                        }
                        cerrar_mysql();
                    }
                }
                break;
                case constant("SEND_RECOVERY_CODE"):
                {
                    conectar_mysql();
                    if(mysql_abierto())
                    {
                        $user_id = $_POST['user']; // Email
                        $res = consulta_mysql("SELECT NULL FROM usuarios WHERE email = ? LIMIT 1","s",array($user_id));
                        if(count($res) > 0)
                        {
                            $rec_code = rand(100000000,999999999);
                            $subject = $GLOBALS['app_title']." - Recuperaci".chr(243)."n de Cuenta";
                            $body = "<br/>Su C".chr(243)."digo de Recuperaci".chr(243)."n Es: <b>".$rec_code.
                                    "</b>.<br/><br/>El C".chr(243)."digo Debe Ser Ingresado En Las Pr".chr(243)."ximas 3 Horas<br/>";
                            $mailsended = send_mail($user_id,$subject,$body);
                            if($mailsended)
                            {
                                if(comando_mysql("UPDATE usuarios SET current_recovery_code = ?, current_recovery_code_expire ".
                                    " = DATE_ADD(NOW(),INTERVAL 3 HOUR) WHERE email = ? LIMIT 1","is",array($rec_code,$user_id)))
                                {
                                    $ret['status'] = "ok";
                                }
                            }
                        }
                        cerrar_mysql();
                    }
                }
                break;
                case constant("CHECK_RECOVERY_CODE"):
                {
                    if(array_key_exists('recovery_code',$_POST))
                    {
                        conectar_mysql();
                        if(mysql_abierto())
                        {
                            $user_id = $_POST['user']; // Email
                            $rec_code = intval($_POST['recovery_code']);
                            $res = consulta_mysql("SELECT NULL FROM usuarios WHERE email = ? AND ".
                                " current_recovery_code = ? AND current_recovery_code_expire >= NOW() LIMIT 1",
                                "ii",array($user_id,$rec_code));
                            if(count($res) > 0)
                            {
                                $ret['status'] = "ok";
                            }
                            cerrar_mysql();
                        }
                    }
                }
                break;
                case constant("CHANGE_PASS_WITH_RECOVERY_CODE"):
                {
                    if(array_key_exists('recovery_code',$_POST))
                    {
                        conectar_mysql();
                        if(mysql_abierto())
                        {
                            $user_id = $_POST['user']; // Email
                            $new_hashpass = $_POST['pass'];
                            $rec_code = intval($_POST['recovery_code']);
                            $sta = comando_mysql("UPDATE usuarios SET hashpass = ?, current_recovery_code_expire = ".
                                " DATE_SUB(NOW(),INTERVAL 1 DAY) WHERE email = ? AND ".
                                " current_recovery_code = ? AND current_recovery_code_expire >= NOW() LIMIT 1",
                                "sii",array($new_hashpass,$user_id,$rec_code));
                            if($sta)
                            {
                                $ret['status'] = "ok";
                            }
                            cerrar_mysql();
                        }
                    }
                }
                break;
                case constant("CHANGE_PASS_WITH_CURRENT_PASS"):
                {
                    if(array_key_exists('new_pass',$_POST))
                    {
                        conectar_mysql();
                        if(mysql_abierto())
                        {
                            if(check_login() && check_device())
                            {
                                $user_id = intval($_POST['user']); // User ID
                                $hashpass = $_POST['pass'];
                                $new_hashpass = $_POST['new_pass'];
                                $res = consulta_mysql("SELECT NULL FROM usuarios WHERE id = ? AND hashpass = ? LIMIT 1",
                                    "is",array($user_id,$hashpass));
                                if(count($res) > 0)
                                {
                                    if(comando_mysql("UPDATE usuarios SET hashpass = ? WHERE id = ? LIMIT 1","si",
                                        array($new_hashpass,$user_id)))
                                    {
                                        $ret['status'] = "ok";
                                    }
                                }
                            }
                            cerrar_mysql();
                        }
                    }
                }
                break;
                case constant("CREATE_DEVICE"):
                {
                    conectar_mysql();
                    if(mysql_abierto())
                    {
                        if(check_login())
                        {
                            $device_id = $_POST['device_id'];
                            if(strlen($device_id) > 1)
                            {
                                $new_id = comando_mysql_return_id("INSERT INTO dispositivos (device_id,".
                                    "assigned) VALUES (?,NOW())","s",array($device_id));
                                if($new_id)
                                {
                                    $ret['device_reg_id'] = $new_id;
                                    $ret['status'] = "ok";
                                }
                            }
                        }
                        cerrar_mysql();
                    }
                }
                break;
                case constant("GET_FORMULARIOS_LIST"):
                {
                    conectar_mysql();
                    if(mysql_abierto())
                    {
                        if(check_login() && check_device())
                        {
                            $res = consulta_mysql("SELECT id,nombre,definicion,primary_fields FROM formularios WHERE activo = 1");
                            if(count($res) > 0)
                            {
                                $ret['formularios'] = $res;
                                $ret['status'] = "ok";
                            }
                            $res2 = consulta_mysql("SELECT id,valor FROM variables WHERE id LIKE \"turno_%\"");
                            if(count($res2) > 0)
                            {
                                $ret['turno_vars'] = $res2;
                            }
                        }
                        cerrar_mysql();
                    }
                }
                break;
                case constant("SYNC_REGISTROS_DATA_FROM_DEVICE"):
                {
                    if(array_key_exists('extra',$_POST))
                    {
                        conectar_mysql();
                        if(mysql_abierto())
                        {
                            if(check_login() && check_device())
                            {
                                $usuarios = intval($_POST['user']);
                                $dispositivos = intval($_POST['device_reg_id']);
                                $extra = json_decode($_POST['extra']);
                                $formularios = isset($extra->formularios) ? $extra->formularios : null;
                                $android_bd_id = isset($extra->android_bd_id) ? $extra->android_bd_id : null;
                                $fecha = isset($extra->fecha) ? $extra->fecha : null;
                                $alerta_nivel = isset($extra->alerta_nivel) ? $extra->alerta_nivel : null;
                                $latitud = isset($extra->latitud) ? $extra->latitud : null;
                                $longitud = isset($extra->longitud) ? $extra->longitud : null;
                                $rondas_uuid = isset($extra->rondas_uuid) ? $extra->rondas_uuid : null;
                                $datos = isset($extra->datos) ? base64_decode($extra->datos) : null;
                                $args_toput = "android_bd_id,dispositivos,formularios,usuarios,".
                                                "fecha,datos,alerta_nivel";
                                $args_mark = "?,?,?,?,?,?,?";
                                $args_types = "sssssss";
                                $args_values = array($android_bd_id,$dispositivos,$formularios,$usuarios,$fecha,$datos,$alerta_nivel);
                                if(!is_null($latitud))
                                {
                                    $args_toput .= ",latitud";
                                    $args_mark .= ",?";
                                    $args_types .= "s";
                                    array_push($args_values,$latitud);
                                }
                                if(!is_null($longitud))
                                {
                                    $args_toput .= ",longitud";
                                    $args_mark .= ",?";
                                    $args_types .= "s";
                                    array_push($args_values,$longitud);
                                }
                                if(!is_null($rondas_uuid))
                                {
                                    $args_toput .= ",rondas_uuid";
                                    $args_mark .= ",?";
                                    $args_types .= "s";
                                    array_push($args_values,$rondas_uuid);
                                }
                                // echo("-- ".$args_toput." --\n");
                                // echo("-- ".$args_mark." --\n");
                                // echo("-- ".$args_types." --\n");
                                // echo("-- ".print_r($args_values,true)." --\n");
                                //comando_mysql("INSERT INTO rawinfo2 (fecha,rawquery,rawtypes,rawdata) VALUES (NOW(),'".
                                //"REPLACE INTO registros (".$args_toput.") VALUES (".$args_mark.")"."','".$args_types."','".
                                //    print_r($args_values,true)."')");
                                if(comando_mysql("REPLACE INTO registros (".$args_toput.") VALUES (".$args_mark.")",
                                    $args_types,$args_values))
                                {
                                    $ret['status'] = "ok";
                                }
                            }
                            cerrar_mysql();
                        }
                    }
                }
                break;
                case constant("SYNC_RONDAS_DATA_FROM_DEVICE"):
                {
                    if(array_key_exists('extra',$_POST))
                    {
                        conectar_mysql();
                        if(mysql_abierto())
                        {
                            if(check_login() && check_device())
                            {
                                $usuarios = intval($_POST['user']);
                                $dispositivos = intval($_POST['device_reg_id']);
                                $extra = json_decode($_POST['extra']);
                                $android_bd_id = isset($extra->android_bd_id) ? $extra->android_bd_id : null;
                                $fecha = isset($extra->fecha) ? $extra->fecha : null;
                                $comentario = isset($extra->comentario) ? $extra->comentario : null;
                                $uuid = isset($extra->uuid) ? $extra->uuid : null;
                                $synced = isset($extra->synced) ? $extra->synced : "0";
                                if($synced == "0")
                                {
                                    if(comando_mysql("REPLACE INTO rondas (android_bd_id,dispositivos,usuarios,".
                                        "fecha,comentario,uuid) VALUES (?,?,?,?,?,?)","ssssss",
                                        array($android_bd_id,$dispositivos,$usuarios,$fecha,$comentario,$uuid)))
                                    {
                                       $ret['status'] = "ok";
                                    }
                                }
                                else
                                {
                                    if(comando_mysql("UPDATE rondas SET comentario = ? WHERE uuid = ? LIMIT 1",
                                        "ss",array($comentario,$uuid)))
                                    {
                                       $ret['status'] = "ok";
                                    }
                                }
                            }
                            cerrar_mysql();
                        }
                    }
                }
                break;
                default: break;
            }
        }
    }
}

echo json_encode($ret);
?>
