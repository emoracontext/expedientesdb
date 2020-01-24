<?php 
namespace gestorWeb\CustomClass;

use Illuminate\Http\Request;
use DB;
use Input;


class apiauth
{
    public function localtest(){
        print "test";
    }
    
    public static function ip_autorizada($ip) {
        $ips_autorizadas = array('127.0.0.1', 
            '83.44.92.239', //oficina dinamica
            '83.37.138.109' //casa dinamica
            );
        if(in_array($ip, $ips_autorizadas)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
       
    public static function key_autorizada($key){
        $keys_autorizadas = array('056476eff841ae09d1435531d0e74df4');
        if (in_array($key, $keys_autorizadas)) {
                 print "autorizado";
                 $resultado = TRUE;
             } else {
                 $resultado = FALSE;
             }
        return $resultado;
    }
           
        
    public static function test_request_api_key() {
        foreach (getallheaders() as $name => $value) {
            if ($name == "api-key") {
                $resultado = api_auth::key_autorizada($value);
            }
        }
        return $resultado;
    }
    
    public static function test_request_ip() {
        $ipaddress = get_client_ip();
        return ip_autorizada($ipaddress);
    }

    public function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP')) {
            $ipaddress = getenv('HTTP_CLIENT_IP');
        } else if (getenv('HTTP_X_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        } else if (getenv('HTTP_X_FORWARDED')) {
            $ipaddress = getenv('HTTP_X_FORWARDED');
        } else if (getenv('HTTP_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        } else if (getenv('HTTP_FORWARDED')) {
            $ipaddress = getenv('HTTP_FORWARDED');
        } else if (getenv('REMOTE_ADDR')) {
            $ipaddress = getenv('REMOTE_ADDR');
        } else {
            $ipaddress = 'UNKNOWN';
        }
        return $ipaddress;
    }
}