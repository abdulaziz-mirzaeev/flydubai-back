<?php

namespace backend\controllers;

use app\traits\BaseModelTrait;
use backend\models\Sms;
use backend\models\Ticket;
use backend\models\Visa;
use DateTime;
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
    public function actionSend()
    {

        $sms = new Sms();
        $errors = [];

        /*
         * Abdulaziz 4 March 2021
         */
        $sms->attributes = Yii::$app->request->post();
        //d(Yii::$app->request->post(),1);

        if ( $sms->save() ) {
            $message_id = 'FlyDubai_' . $sms->id;
            $sms->phone = Sms::correctPhone($sms->phone);
            // Отправка смс сообщения
            $result = Sms::send($sms->phone, $message_id, $sms->message);

            echo $result;

            if ( $result == 'Request is received' ) {
                return ['status' => 1];
            }
            $sms->error = json_encode($result);
            $sms->save(); // сохраняем ошибку в бд, при отправке смс
            $errors[] = $result;

        } else {
            $sms->validate();
            $errors[] = $sms->getErrors();
        }

        return ['status' => 0, 'errors' => $errors];

    }

    // рассылка смс нескольким клиентам, у которых установлен статус send_newsletter
    public function actionNewsletter()
    {

        $sms = new Sms();
        $errors = [];

        if ( $sms->load(Yii::$app->request->post()) && $sms->save() ) {

            $message_id = 'FlyDubai_' . $sms->id;

            // Отправка смс рассылки для клиентов, у которых status_newsletter = 1
            $result = Sms::newsletter($sms->message, $message_id);
            if ( $result['status'] ) {
                return ['status' => 1];
            }

            $sms->error = json_encode($result);
            $sms->save(); // сохраняем ошибку в бд, при отправке смс
            $errors[] = $result;

        }
        $errors[] = $sms->getErrors();
        return ['status' => 0, 'errors' => $errors];

    }

    public function actionChecksms()
    {
        $tomorrow = new DateTime('tomorrow');

        $flights = Ticket::find()
            ->select([
                'flight_route',
                'DATE(flight_date) as flight_date',
                'client.first_name',
                'client.last_name',
                'client.send_newsletter'
            ])
            ->joinWith('client', false, 'LEFT JOIN')
            ->where([
                'client.send_newsletter' => 1,
                'flight_date' => $tomorrow
            ])
            ->asArray()->all();


        return print_r($flights);
    }


}
