<?php

namespace backend\controllers;

use backend\models\Cdr;
use backend\models\Notification;
use backend\models\Process;
use common\models\User;
use Yii;

/**
 * билеты
 */
class CdrController extends BaseController
{

    public function actionGetMissedCalls()
    {
        $user_id =  Yii::$app->user->id;

        $user = User::findOne(['id' => $user_id]);

        $missedCalls =  Cdr::check($user->number);

        return $missedCalls;
    }


    public function actionStats()
    {

        $request = Yii::$app->request;
        $from = $request->post('from');
        $to = $request->post('to');

        if(!$from && !$to){

              $to = date('Y-m-d H:i:s', strtotime('today'));

              $from = date('Y-m-d H:i:s', strtotime($to . '+1 days'));

              $missedCalls =  Cdr::getStats($to,$from);

        } else {
            $missedCalls =  Cdr::getStats($to,$from);
        }
        return $missedCalls;
    }

}
