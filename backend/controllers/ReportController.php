<?php

namespace backend\controllers;

use backend\models\Order;
use backend\models\Process;
use Yii;
use backend\models\Cashier;
use yii\data\ActiveDataProvider;
use yii\data\Sort;
use yii\web\NotFoundHttpException;

/**
 * билеты
 */
class ReportController extends BaseController
{

    public $modelClass = Order::class;

    //  отчет по продажам каждого оператора
    public function actionSales()
    {

        $date_to = Yii::$app->request->get('Filter')['date_to'];

        if (!$date_to) $date_to = date('Y-m-d', time());

        if ($date_to) {
            // дата заказа-заявки
            $date_in = date('Y-m-d', strtotime(Yii::$app->request->get('Filter')['date_from']));
            $date_to = date('Y-m-d', strtotime($date_to . ' + 1 day'));
        }

        $errors = [];

        $operator = Yii::$app->request->get('Filter')['operator']; // оператор

        $payment_type = Yii::$app->request->get('Filter')['payment_type']; // тип оплаты

        /*
        // если указан неверный тип оплаты
        if($payment_type && !in_array($payment_type,Process::$payment_types)){
            $errors[] = 'Укажите тип оплаты!';
        }*/

        if (!$errors) {

            $query = Order::find()
                ->leftJoin('process p', 'p.order_id=order.id')
                ->where(['between', 'order.created_at', $date_in, $date_to])
                ->andWhere(['order.status' => Order::STATUS_PAID])
                ->orderBy('order.created_at');

            if ($operator) $query->andWhere(['order.operator_id' => $operator]);
            if ($payment_type) $query->andWhere(['p.payment_type' => $payment_type]);

            return new ActiveDataProvider([
                'query' => $this->getQuery($query),
                'sort' => new Sort()
            ]);
        }
        return ['status' => 0, 'errors' => $errors];


    }

    //  отчет по типам: билет, карго, виза, тур пакет
    public function actionType()
    {

        // дата заказа-заявки
        $date_in = date('Y-m-d', strtotime(Yii::$app->request->get('Filter')['date_from']));
        $date_to = date('Y-m-d', strtotime(Yii::$app->request->get('Filter')['date_to'] . ' + 1 day'));

        $operator = Yii::$app->request->get('Filter')['operator']; // оператор

        $type = Yii::$app->request->get('Filter')['type']; // тип - билет, cargo, visa, tour_package
        $payment_type = Yii::$app->request->get('Filter')['payment_type']; // тип оплаты

        $errors = [];

        // если указан неверный тип оплаты
        if ($payment_type && !in_array($payment_type, Process::$payment_types)) {
            $errors[] = 'Укажите тип оплаты!';
        }
        // если указан неверный тип
        if ($type && !in_array($type, Process::$types)) {
            $errors[] = 'Указан неверный тип!';
        } else { // все типы
            $type = Process::$types;
        }

        if (!$errors) {
            $query = Order::find()
                //->select('order.id,order.number,order.type,order.created_at,order.status,p.summ,p.cashier_id,p.process_type,p.status_director,p.status,p.payment_type,p.created_at as process_date,p.currency_id,p.currency_rate')
                ->select('SUM(p.summ) as summ, p.payment_type,order.type')
                ->leftJoin('process p', 'p.order_id=order.id')
                ->where(['between', 'order.created_at', $date_in, $date_to])
                ->andWhere(['order.status' => Order::STATUS_PAID]) // только оплаченные
                ->andWhere(['order.type' => $type]) // билет, виза...
                ->groupBy('order.type,p.payment_type');

            if ($operator) $query->andWhere(['order.operator_id' => $operator]);
            if ($payment_type) $query->andWhere(['p.payment_type' => $payment_type]);

            return $query->asArray()->all();
        }
        return ['status' => 0, 'errors' => $errors];

    }

    // информация для вывода статистики, информации для диаграмм и графиков для директора и в dashboard
    public function actionStat()
    {

        // за 12 месяцев
        $date_in = date('Y-m-d', strtotime(date('Y-m-d', time()) . '- 12 month'));
        $date_to = date('Y-m-d', time()); // текущий день

        $errors = [];

        /** SELECT SUM(summ) AS SUM, DATE(o.created_at), o.`type`,o.`status`
         * FROM process as p
         * LEFT JOIN `order` o ON o.id=p.order_id
         * WHERE p.id>0
         * GROUP BY process_type, DATE(o.created_at), o.`type`,o.`status`
         * order BY DATE(o.created_at)
         */
        $query = Order::find()
            //->select('order.id,order.number,order.type,order.created_at,order.status,p.summ,p.cashier_id,p.process_type,p.status_director,p.status,p.payment_type,p.created_at as process_date,p.currency_id,p.currency_rate')
            ->select('SUM(p.summ) as summ, Count(p.id) as cnt, order.type,year(order.created_at) as y,month(order.created_at) as m')
            ->leftJoin('process p', 'p.order_id=order.id')
            ->where(['between', 'order.created_at', $date_in, $date_to])
            ->andWhere(['order.status' => Order::STATUS_PAID]) // только оплаченные
            ->groupBy('order.type,year(order.created_at),month(order.created_at)')
            ->orderBy('y,m');

        //q($query);

        $orders_month = $query->asArray()->all();

        $types_month = [];

        // ИТОГО за все время
        $types = [];
        foreach ($orders_month as $order) {
            $types[$order['type']]['count'] += $order['cnt'];
            $types[$order['type']]['summ'] += $order['summ'];
            $types_month[$order['type']][] = $order;
        }

        // за неделю
        $date_in = date('Y-m-d', strtotime(date('Y-m-d', time()) . '- 7 day'));
        //$date_to = date('Y-m-d', time());

        // за неделю
        $query = Order::find()
            //->select('order.id,order.number,order.type,order.created_at,order.status,p.summ,p.cashier_id,p.process_type,p.status_director,p.status,p.payment_type,p.created_at as process_date,p.currency_id,p.currency_rate')
            ->select('SUM(p.summ) as summ, Count(p.id) as cnt, order.type,year(order.created_at) as y,month(order.created_at) as m')
            ->leftJoin('process p', 'p.order_id=order.id')
            ->where(['between', 'order.created_at', $date_in, $date_to])
            ->andWhere(['order.status' => Order::STATUS_PAID]) // только оплаченные
            ->groupBy('order.type,year(order.created_at),month(order.created_at)')
            ->orderBy('y,m');

        //q($query);

        $orders_week = $query->asArray()->all();

        $types_week = [];
        foreach ($orders_week as $order) {
            $types_week[$order['type']]['count'] += $order['cnt'];
            $types_week[$order['type']]['summ'] += $order['summ'];
        }

        //d([$types,$types_week],1);

        return $result = [
            //'paid_month' => $orders_month,
            'paid_month' => $types_month,
            //'week' => $orders_week,
            'paid_week' => $types_week,
            'paid_all' => $types,
        ];

    }

    public function actionPeriod()
    {
        $date_in = date('Y-m-d', strtotime(Yii::$app->request->get('date_from')));
        $date_to = date('Y-m-d', strtotime(Yii::$app->request->get('date_to')));

        $errors = [];
        if (!$errors) {

            $query = Order::find()
                ->leftJoin('process p', 'p.order_id=order.id')
                ->where(['between', 'order.created_at', $date_in, $date_to])
                ->andWhere(['order.status' => Order::STATUS_PAID])
                ->orderBy('order.created_at');

            return new ActiveDataProvider([
                'query' => $this->getQuery($query),
                'sort' => new Sort()
            ]);
        }
        return ['status' => 0, 'errors' => $errors];
    }


}
