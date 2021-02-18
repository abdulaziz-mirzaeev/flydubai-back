<?php
namespace common\helpers;

class CurlBase
{
    // задать все параметры вручную
    public static function runex($url,$header,$method='get',$data=null,$ssl_verify=false){

        $curl = curl_init();

        if(is_array($data)) $data = json_encode($data,JSON_UNESCAPED_UNICODE);

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYHOST => $ssl_verify,
            CURLOPT_SSL_VERIFYPEER => $ssl_verify,
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => $header,
        ));

        $result = curl_exec($curl);
        d($result);
        d(curl_error($curl));

        curl_close($curl);

        return json_decode($result,true);


        /*$curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://91.204.239.44/broker-api/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
 "messages":
 [{"recipient":"998903285426",
  "message-id":"flyDubai",
  "sms":{
  "originator": "3700",
  "content": {"text": "hello"}}}]}',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Basic Zmx5ZHViYWl1ejpNNVU1c3k4Vmk5'
            ),
        ));*/

    }




}