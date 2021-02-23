<?php

namespace backend\controllers;

use backend\models\Order;
use backend\models\Process;
use common\models\User;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * билеты
 */
class ProcessController extends BaseController
{
    public $modelClass = Process::class;


    // оплата перечислением
    public function actionTransfers()
    {

        $provider = new ActiveDataProvider([
            'query' => Process::find()->with(['cashier'])->where(['payment_type' => Process::PAYMENT_TYPE_TRANSFER]),
            'pagination' => false,
        ]);

        return $provider;
    }

    // список перенесенных средств из кассы в кассу
    public function actionTransfer()
    {

        $provider = new ActiveDataProvider([
            'query' => Process::find()->with(['cashierFrom', 'cashier'])->where(['process_type' => Process::TYPE_TRANSFER]),
            'pagination' => false,
        ]);

        return $provider;
    }

    // список приходов в кассу
    public function actionEnter()
    {

        $provider = new ActiveDataProvider([
            'query' => Process::find()->with(['cashier'])->where(['process_type' => Process::TYPE_ENTER]),
            'pagination' => false,
        ]);

        return $provider;
    }

    // список оплаченных заказов
    public function actionPaid()
    {

        $provider = new ActiveDataProvider([
            'query' => Process::find()->with(['cashier'])->where(['process_type' => Process::TYPE_PAID]),
            'pagination' => false,
        ]);

        return $provider;
    }

    // список расхода средств из кассы
    public function actionExit()
    {

        $provider = new ActiveDataProvider([
            'query' => Process::find()->with(['cashier'])->where(['process_type' => Process::TYPE_EXIT, 'status_director' => Process::DIRECTOR_CONFIRM_FALSE]),
            'pagination' => false,
        ]);

        return $provider;
    }

    public function actionApprove($id)
    {
        $user = User::findOne(Yii::$app->user->id);
        if ( $user->role !== 'director' ) {
            Yii::$app->response->statusCode = 403;
            return 'Forbidden';
        }

        $process = Process::findOne($id);
        $process->status_director = 1;
        $process->save();

        return ['message' => 'Success'];

    }

}
