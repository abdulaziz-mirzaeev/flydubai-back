<?php

namespace backend\controllers;

use backend\models\Cashier;
use backend\models\Notification;
use backend\models\Order;
use backend\models\Process;
use common\models\User;
use SebastianBergmann\CodeCoverage\Exception;
use yii\data\ActiveDataProvider;

/**
 * Class OrderController
 * @package backend\controllers
 * Заявки
 */
class OrderController extends BaseController
{
    public $modelClass = Order::class;

    public function actionIndex()
    {
        $provider = new ActiveDataProvider([
            'query' => Order::find()
                ->where('type!=""')
                ->andWhere('type_id!=""'),
            'pagination' => false,
        ]);

        return $provider;
    }

    // список возвращенных денежных средств ВДС
    public function actionReturn(){

        $provider = new ActiveDataProvider([
            'query' => Order::find()->where(['status'=>Order::STATUS_RETURNED]),
            'pagination' => false,
        ]);
        return $provider;
    }

    // список в процессе/забронироанных
    public function actionBooked(){

        $provider = new ActiveDataProvider([
            'query' => Order::find()->where(['status'=>Order::STATUS_BOOKED]),
            'pagination' => false,
        ]);

        return $provider;

    }

    // оплаченные заказы
    public function actionPaid(){

        $provider = new ActiveDataProvider([
            'query' => Order::find()->where(['status'=>Order::STATUS_PAID]),
            'pagination' => false,
        ]);

        return $provider;

    }

    // отмененные заказы
    public function actionCancel(){

        $provider = new ActiveDataProvider([
            'query' => Order::find()->where(['status'=>Order::STATUS_CANCEL]),
            'pagination' => false,
        ]);

        return $provider;

    }

    //
    /*public function actionSetPaymentDate(){

        $post = $this->getPost();

        if(!isset($post['cashier_id'])) $errors[] = 'Касса не задана!';

        if (!$cashier = Cashier::findOne($post['cashier_id'])) $errors[] = 'Касса на найдена!';

        if (!$errors) {

            if ($cashier->summ - $post['summ'] < 0) {
                return ['status' => 0, 'errors' => 'Расход превышает остаток в кассе!'];
            }

            // для исключения повторного снятия одной и той же суммы, проверка, пока не будет подтверждено директором
            if ($process = Process::find()->where(['summ' => $post['summ'], 'cashier_id' => $post['cashier_id'], 'process_type' => Process::TYPE_EXIT, 'status' => 0])->one()) {
                return ['status' => 0, 'errors' => 'Процесс уже создан, его необходимо подтвердить директором!'];
            }

            // расход из кассы
            // создаем процесс, но для расхода нужно подтверждение директора actionConfirmExit
            // поэтому из кассы деньги не снимаем, только формируем процесс
            $process = new Process();
            $process->cashier_id = $cashier->id;
            $process->summ = $post['summ'];
            $process->process_type = Process::TYPE_EXIT;

            // перед снятием средств нужно подтверждение директора status_director=1
            $process->status_director = 0;
            $process->status = 0;

            if ($process->save()) {
                $message = 'Произведен расход средств из кассы ' . $cashier->name . ' на сумму ' . $process->summ;
                Notification::send(Notification::CONFIRM_EXIT, $process->id,Notification::STATUS_PROCESS,$message); // отправляем уведомление
                return ['status' => 1];
            }

            if ($cashier->hasErrors()) $errors[] = $cashier->getErrors();
            if ($process->hasErrors()) $errors[] = $process->getErrors();

        }

        return ['status' => 0, 'errors' => $errors];

    } */



}
