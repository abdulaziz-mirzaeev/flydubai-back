<?php
namespace common\helpers;

class Curl extends CurlBase
{

    // тестовые данные url и login для получения токена
    private static $url_test = 'https://api-dev.smartpos.uz';
    private static $username_test = '998946968835';
    private static $password_test = 'aA1234567';

    // продакшн
    private static $url = 'https://api.smartpos.uz';
    private static $username = '';
    private static $password = '';

    private static $test_mode = true;

    public static function run($api_url,$method='get',$token=null,$data=null,$ssl_verify=false){

        $url = self::$test_mode ? self::$url_test : self::$url;

        if(!$token) {
            if (strpos($api_url, '/login') > 0) {
                $header = [
                    'Content-Type: application/json'
                ];
                $data = [
                    'username' => self::$test_mode ? self::$username_test : self::$username,
                    'password' => self::$test_mode ? self::$password_test : self::$password,
                    // 'rememberMe'=> true
                ];
            }else{
                return false;
            }
        }else{ // если передан токен
            $header = [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json'
            ];
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url . $api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYHOST => $ssl_verify,
            CURLOPT_SSL_VERIFYPEER => $ssl_verify,
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_POSTFIELDS => json_encode($data,JSON_UNESCAPED_UNICODE),
            CURLOPT_HTTPHEADER => $header,
        ));

        $result = curl_exec($curl);

        //d(curl_error($curl));

        curl_close($curl);

        return json_decode($result,true);

    }


}