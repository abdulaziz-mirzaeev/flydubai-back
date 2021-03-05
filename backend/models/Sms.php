<?php

namespace backend\models;

use app\traits\BaseModelTrait;
use common\helpers\Curl;
use Yii;

/**
 * This is the model class for table "sms".
 *
 * @property int $id
 * @property int $status
 * @property string|null $message Сообщение
 * @property string|null $error Ошибка
 * @property string|null $phone Телефон
 * @property string|null $created_at Дата создания
 * @property int|null $created_by Создал
 */
class Sms extends \backend\models\BaseModel
{
    use BaseModelTrait;

    //private static $auth_token = 'c2liZXh1ejpWdmF6NkRwem4';
    private static $username = 'flydubaiuz';
    private static $password = 'M5U5sy8Vi9';

    private static $url = 'http://91.204.239.44/broker-api/send';



    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sms';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at'], 'safe'],
            [['created_by','status'], 'integer'],
            [['message', 'error', 'phone'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'message' => 'Сообщение',
            'error' => 'Ошибка',
            'phone' => 'Телефон',
            'status' => 'Статус',
            'created_at' => 'Дата создания',
            'created_by' => 'Создал',
        ];
    }

    private static function getToken(){
        return base64_encode(self::$username .':'. self::$password );
    }

    public static function correctPhone($phone){
        return preg_replace('/[^0-9]/','',$phone);
    }

    public static function send($phone,$message_id,$message){


        $auth_token = self::getToken();
        //d($phone .' ' . $auth_token . ' ' . $message_id . ' ' . $message,0);

        $data = '{
 "messages":
 [{"recipient":"'.$phone.'",
  "message-id":"'.$message_id.'",
  "sms":{
  "originator": "3700",
  "content": {"text": "'.$message.'"}}}]}';

        $header = [
            "Authorization: Basic $auth_token",
            "Content-Type: application/json",
            "Accept: application/json"
        ];


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://91.204.239.44/broker-api/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>$data,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Basic Zmx5ZHViYWl1ejpNNVU1c3k4Vmk5'
            ),
        ));


        $result = Curl::runex(self::$url,$header,'POST',$data);

        //d($result,0);

        return $result; // " test    Request is received " .  $data);
        
        /*
        $options = [
            'http' => [
                'method'  => 'POST',
                'content' => $data,
                'header'=>
                    "Authorization: Basic $auth_token=\r\n" .
                    "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            ]
        ];

        $context  = stream_context_create( $options );
        $result = file_get_contents($url, false, $context);

        if($result=='Request is received'){
            return ['status'=>1];
        }

        return ['status'=>0];*/
    }


    // рассылка смс сообщений клиентам, у которых status_newsletter = 1
    public static function newsletter($message,$message_id,$date_from=null,$date_to=null,$delay=1){

        $auth_token = self::getToken();

        
        $date_from = date('Y-m-d 9-00',time());  // 2017-11-05 10-00 
        $date_to = date('Y-m-d 10-00',time()); // разослать в течении 1 часа // 2017-11-05 11-00 

        if( $clients = Client::find()->where(['status_newsletter'=>1])->all() ){
            $phones = [];
            $i=0;
            foreach ($clients as $client){
                if(!$client->phone) continue;
                //$time = time()+$i;
                $phones[] = '{"recipient": "'.$client->phone.'","message-id": "'.$message_id.'"}';
                $i++;
            }
            $phones = implode(',',$phones);
        }

        $data = '{priority": "", "timing": {
"localtime": "true",
"start-datetime": "'.$date_from.'",  
"end-datetime": "'.$date_to.'",
"allowed-starttime": "9-00", "allowed-endtime": "20-00"
"send-evenly": "false"
},
"sms": {
"originator": "3700", 
"content": {"text": "'.$message.'"}
},
"messages":['.$phones.']}}';

        $header = [
            "Authorization: Basic $auth_token",
            "Content-Type: application/json",
            "Accept: application/json"
        ];

        $result = Curl::runex(self::$url,$header,'post', $data );

        return $result;


        // return " test    Request is received " . json_encode( $data);
        
        /*$data = '{
 "messages":
 [{"recipient":"'.$phone.'",
  "message-id":"'.$message_id.'",
  "sms":{
  "originator": "3700",
  "content": {"text": "'.$message.'"}}}]}'; */

        /*$options = [
            'http' => [
                'method'  => 'POST',
                'content' => $data,
                'header'=>
                    "Authorization: Basic $auth_token=\r\n" .
                    "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            ]
        ];

        $context  = stream_context_create( $options );
        $result = file_get_contents($url, false, $context);

        if($result=='Request is received'){
            return ['status'=>1];
        }

        return ['status'=>0];*/
    }
}
