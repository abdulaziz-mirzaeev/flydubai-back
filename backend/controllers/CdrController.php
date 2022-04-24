<?php

namespace backend\controllers;

use backend\models\Cdr;
use backend\models\Operator;
use common\models\User;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * билеты
 */
class CdrController extends BaseController
{


    public $modelClass = Cdr::class;


    public function actions()
    {
        $actions = parent::actions();
        unset($actions['update']);
        return $actions;
    }


    // get extention's missed calls
    public function actionGetMissedCalls()
    {

        $request = Yii::$app->request;

        $token = $request->get('token');

        $user = User::findIdentityByAccessToken($token);

        if ($user) {
            $number = Operator::findOne(['user_id' => $user->id]);
        }

        if ($number) {
            $exten = $number->number;
        }

        $time = Cdr::getDateTime('first');

        $from = ArrayHelper::getValue($time, 'date_from');

        $to = ArrayHelper::getValue($time, 'date_to');

        // $missedCalls = Cdr::getMissedCallsByExt($from, $to, $exten);

        $missedCalls = Cdr::getMissedCallsByExt($from, $to, $exten);

        return $missedCalls;

    }

    public function actionStats()
    {

        $request = Yii::$app->request;

        $from = $request->post('from');
        $to = $request->post('to');

        if (!$from && !$to) {

            $to = date('Y-m-d H:i:s', strtotime('today'));

            $from = date('Y-m-d H:i:s', strtotime($to . '-1000 days'));

            $missedCalls = Cdr::getStats($from, $to);

        } else
            $missedCalls = Cdr::getStats($to, $from);

        return $missedCalls;
    }

    public function actionUpdate()
    {

        $request = Yii::$app->request;
        $uniqueid = $request->post('uniqueid');
        $status = $request->post('status');
        $cdr = Cdr::findOne(['uniqueid' => $uniqueid]);

        if ($cdr) {
            $cdr->userfield = $status;
            $cdr->save(false);
            return $cdr;
        }

        return [];

    }


    public function actionGetStatuses()
    {
        return Cdr::getStatuses();
    }

    // OLD ONE
    // call_center logic that we are not using in flydubai
    public function actionGetMissedCallsCallCenter()
    {

        $user_id = Yii::$app->user->id;

        $user = User::findOne(['id' => $user_id]);
        if ($user) {
            $missedCalls = Cdr::check($user->number);

            return $missedCalls;
        }
        return [];
    }


}
