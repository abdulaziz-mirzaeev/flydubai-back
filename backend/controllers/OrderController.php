<?php

namespace backend\controllers;

use Yii;
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
    public function actionReturn()
    {

        $provider = new ActiveDataProvider([
            'query' => Order::find()->where(['status' => Order::STATUS_RETURNED]),
            'pagination' => false,
        ]);
        return $provider;
    }

    // список в процессе/забронироанных
    public function actionBooked()
    {

        $provider = new ActiveDataProvider([
            'query' => Order::find()->where(['status' => Order::STATUS_BOOKED]),
            'pagination' => false,
        ]);

        return $provider;

    }

    // оплаченные заказы
    public function actionPaid()
    {

        $provider = new ActiveDataProvider([
            'query' => Order::find()->where(['status' => Order::STATUS_PAID]),
            'pagination' => false,
        ]);

        return $provider;

    }

    // отмененные заказы
    public function actionCancel()
    {

        $provider = new ActiveDataProvider([
            'query' => Order::find()->where(['status' => Order::STATUS_CANCEL]),
            'pagination' => false,
        ]);

        return $provider;

    }

    //
    public function actionPay()
    {

        $post = $this->getPost();
        $errors = [];

        if ( !isset($post['cashier_id']) ) {
            $errors['cashier_id'] = 'Касса не введена!';
        } else {
            $cashier = Cashier::findOne($post['cashier_id']);
            if ( empty($cashier) )
                $errors['cashier_id'] = 'Касса не найдена!';
        }

        if ( empty($post['order_id']) ) {
            $errors['order_id'] = 'Введите Номер заявки!';
        } else {
            $order = Order::findOne($post['order_id']);
            if ( empty($order) ) {
                $errors['order_id'] = 'Эта заявка не существует!';
            } else {
                if ($order->status === Order::STATUS_PAID) {
                    $errors['order_id'] = 'Оплата за эту заявку уже воспроизведена!';
                }
            }
        }

        if ( empty($post['payment_type']) ) $errors['payment_type'] = 'Способ оплаты на введена!';

        if ( empty($post['nds']) ) $errors['nds'] = 'НДС не введен';

        switch ( $post['payment_type'] ) {
            case Process::PAYMENT_TYPE_CASH:
                if ( empty($post['summ']) ) {
                    $errors['summ'] = 'Сумма наличными не введена!';
                }
                break;
            case Process::PAYMENT_TYPE_TERMINAL:
                if ( empty($post['summ_terminal']) ) {
                    $errors['summ_terminal'] = 'Сумма терминала не введена!';
                }
                if ( empty($post['terminal_id']) ) {
                    $errors['terminal_id'] = 'ID терминала не введен!';
                }
                break;
            case Process::PAYMENT_TYPE_CASH_TERMINAL:
                if ( empty($post['summ']) ) {
                    $errors['summ'] = 'Сумма наличными не введена!';
                }
                if ( empty($post['summ_terminal']) ) {
                    $errors['summ_terminal'] = 'Сумма терминалом не введена!';
                }
                if ( empty($post['terminal_id']) ) {
                    $errors['terminal_id'] = 'ID терминала не введен!';
                }
                break;
            case Process::PAYMENT_TYPE_TRANSFER:
                if ( empty($post['summ']) ) {
                    $errors['summ'] = 'Сумма не введена!';
                }
                if ( empty($post['cheque_number']) ) {
                    $errors['cheque_number'] = 'Номер квитанции не введена!';
                }
                break;
            default:
                $errors['payment_type'] = 'Такой способ оплаты не существует';
        }


        if ( empty($errors) ) {
            $order->cashier_id = $post['cashier_id'];
            $order->payment_type = $post['payment_type'];
            $order->nds = $post['nds'];
            $order->payment_at = date('Y-m-d H:i:s', time());
            $order->status = Order::STATUS_PAID;
            // приход в кассу
            $process = new Process();
            $process->cashier_id = $cashier->id;
            $process->nds = $post['nds'];
            $process->order_id = $order->id;
            $process->process_type = Process::TYPE_ENTER;
            $process->payment_type = $post['payment_type'];


            switch ( $post['payment_type'] ) {
                case Process::PAYMENT_TYPE_CASH:
                    // Cash Payment
                    $order->summ = $post['summ'];

                    $cashier->summ += $post['summ'];

                    $process->summ = $post['summ'];
                    break;
                case Process::PAYMENT_TYPE_TERMINAL:
                    // Terminal Payment
                    $order->summ_terminal = $post['summ_terminal'];
                    $order->terminal_id = $post['terminal_id'];
                    $order->summ_terminal = $post['summ_terminal'];

                    $cashier->summ_terminal += $post['summ_terminal'];

                    $process->summ_terminal = $post['summ_terminal'];
                    $process->terminal_id = $post['terminal_id'];
                    break;
                case Process::PAYMENT_TYPE_CASH_TERMINAL:
                    // Cash - Terminal Payment
                    $order->summ = $post['summ'];
                    $order->summ_terminal = $post['summ_terminal'];
                    $order->terminal_id = $post['terminal_id'];
                    $order->summ_terminal = $post['summ_terminal'];

                    $cashier->summ += $post['summ'];
                    $cashier->summ_terminal += $post['summ_terminal'];

                    $process->summ = $post['summ'];
                    $process->summ_terminal = $post['summ_terminal'];
                    $process->terminal_id = $post['terminal_id'];
                    break;
                case Process::PAYMENT_TYPE_TRANSFER:
                    $order->summ = $post['summ'];
                    $order->cheque_number = $post['cheque_number'];

                    $process->summ = $post['summ'];
                    $process->cheque_number = $post['cheque_number'];
                    break;
            }

            if ( $cashier->save() && $process->save() && $order->save() ) {
                $message = 'Поступили средства в кассы ' . $cashier->name . ' на сумму ' . $process->summ;
                Notification::send(Notification::ENTER, $process->id, Notification::STATUS_PROCESS, $message);
                Yii::$app->response->statusCode = 200;
                return [
                    'message' => 'Success',
                    'process' => $process->id,
                    'order' => $order->id,
                    'cashier' => $cashier->id
                ];
            }

            if ( $cashier->hasErrors() ) $errors[] = $cashier->getErrors();
            if ( $process->hasErrors() ) $errors[] = $process->getErrors();
            if ( $order->hasErrors() ) $errors[] = $order->getErrors();

        }
        Yii::$app->response->statusCode = 422;
        return $errors;

    }

    public function actionReturnback($id)
    {
        $order = Order::findOne($id);

        if ( empty($order) ) {
            Yii::$app->response->statusCode = 422;
            return ['message' => 'Заявка не существует'];
        }

        $order->status = Order::STATUS_RETURNED;

        if ( $order->save() ) {
            return ['message' => 'Успешно'];
        }

        return $order->getErrors();
    }


}
