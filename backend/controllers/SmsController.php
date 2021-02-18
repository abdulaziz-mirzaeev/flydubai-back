<?php

namespace backend\controllers;

use app\traits\BaseModelTrait;
use backend\models\Sms;
use backend\models\Visa;
use Yii;
use backend\models\Cashier;
use yii\data\ActiveDataProvider;
use yii\data\Sort;
use yii\web\NotFoundHttpException;

/**
 * билеты
 */
class SmsController extends BaseController
{
    use BaseModelTrait;

    public $modelClass = Sms::class;

    // отправка смс конкретному клиенту перед вылетом
    // в определенное время по планировщику CRON
    public function actionSend(){

        $sms = new Sms();
        $errors = [];

        if( $sms->load(Yii::$app->request->post()) && $sms->save() ){
            $message_id = 'FlyDubai_' . $sms->id;
            $sms->phone = Sms::correctPhone($sms->phone);
            // Отправка смс сообщения
            $result = Sms::send($sms->phone,$message_id,$sms->message);

            d($result);

            if($result=='Request is received'){
                return ['status'=>1];
            }
            $sms->error = json_encode($result);
            $sms->save(); // сохраняем ошибку в бд, при отправке смс
            $errors[] = $result;

        }else{
            $errors[] = $sms->getErrors();
        }

        return ['status'=>0,'errors'=>$errors];

    }

    // рассылка смс нескольким клиентам, у которых установлен статус send_newsletter
    public function actionNewsletter(){

        $sms = new Sms();
        $errors = [];

        if( $sms->load(Yii::$app->request->post()) && $sms->save() ){

            $message_id = 'FlyDubai_' . $sms->id;

            // Отправка смс рассылки для клиентов, у которых status_newsletter = 1
            $result = Sms::newsletter($sms->message,$message_id);
            if($result['status']){
                return ['status'=>1];
            }

            $sms->error = json_encode($result);
            $sms->save(); // сохраняем ошибку в бд, при отправке смс
            $errors[] = $result;

        }
        $errors[] = $sms->getErrors();
        return ['status'=>0,'errors'=>$errors];

    }




}
