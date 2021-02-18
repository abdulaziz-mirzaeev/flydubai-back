<?php

namespace backend\controllers;

use backend\models\Process;
use backend\models\Ticket;
use backend\models\Visa;
use Yii;
use backend\models\Cashier;
use yii\data\ActiveDataProvider;
use yii\data\Sort;
use yii\web\NotFoundHttpException;

/**
 * отчеты по кассе.
 */
class ReportcashierController extends BaseController
{

    public $modelClass = Cashier::class;

    /**
     * Отчет - остатки в кассах.
     * 10.10.3.30:9090/reportcashier/remains
     * @return mixed
     */
    public function actionRemains()
    {

        $query = Cashier::find()
            ->select('id,name,summ')
            ->orderBy('name');

        return new ActiveDataProvider([
            'query' => $query,
            'sort' => new Sort()
        ]);

    }


    /**
     * Отчет - по билетам
     * 10.10.3.30:9090/reportcashier/ticket?Filter[date_from]=2021-01-01&Filter[class]=EV&Filter[type]=cash
     * @return mixed
     */
    public function actionTicket()
    {


        // дата выписки билета
        $date_in = date('Y-m-d', strtotime(Yii::$app->request->get('Filter')['date_from']));
        $date_to = date('Y-m-d', strtotime(Yii::$app->request->get('Filter')['date_to'] . ' + 1 day'));

        $class = Yii::$app->request->get('Filter')['class']; // класс бронирования

        $payment_type = Yii::$app->request->get('Filter')['payment_type']; // тип оплаты

        $errors = [];

        // если указан неверный тип оплаты
        if($payment_type && !in_array($payment_type,Process::$payment_types)){
            $errors[] = 'Укажите тип оплаты!';
        }


        if ($date_in != '1970-01-01' && isset($date_to)) {

            $query = Ticket::find()
                //->select('*')
                ->leftJoin('order o',"o.type_id=ticket.id AND o.`type`='ticket'")
                ->leftJoin('process p','p.order_id=o.id')
                ->where(['between', 'ticket.created_at', $date_in, $date_to])
                ->orderBy('ticket.created_at');

            if($class) $query->andWhere(['ticket.class'=>$class]);
            if($payment_type) $query->andWhere(['p.payment_type'=>$payment_type]);

            return new ActiveDataProvider([
                'query' => $query,
                'sort' => new Sort()
            ]);
        }

        $errors[] = 'Укажите дату!';

        return ['status'=>0,'errors'=>$errors];

    }


    /**
     * Отчет - по визам
     * 10.10.3.30:9090/reportcashier/visa?Filter[date_from]=2021-01-01&Filter[visa_type]=1&Filter[payment_type]=cash&Filter[visa_partner]=1
     * @return mixed
     */
    public function actionVisa()
    {

        // дата выписки билета
        $date_in = date('Y-m-d', strtotime(Yii::$app->request->get('Filter')['date_from']));
        $date_to = date('Y-m-d', strtotime(Yii::$app->request->get('Filter')['date_to'] . ' + 1 day'));

        $visa_type = Yii::$app->request->get('Filter')['visa_type']; // тип визы
        $visa_partner = Yii::$app->request->get('Filter')['visa_partner']; // партнер

        $payment_type = Yii::$app->request->get('Filter')['payment_type']; // тип оплаты

        $errors = [];

        // если указан неверный тип оплаты
        if($payment_type && !in_array($payment_type,Process::$payment_types)){
            $errors[] = 'Укажите тип оплаты!';
        }

        //d($date_to,1);
        if (!$errors && $date_in != '1970-01-01' && isset($date_to)) {

            $query = Visa::find()
                //->select('*')
                ->leftJoin('order o',"o.type_id=visa.id AND o.`type`='visa'")
                ->leftJoin('process p','p.order_id=o.id')
                ->where(['between', 'visa.created_at', $date_in, $date_to])
                ->orderBy('visa.created_at');

            if($visa_type) $query->andWhere(['visa.visa_type'=>$visa_type]);
            if($visa_partner) $query->andWhere(['visa.visa_type'=>$visa_partner]);
            if($payment_type) $query->andWhere(['p.payment_type'=>$payment_type]);

            return new ActiveDataProvider([
                'query' => $query,
                'sort' => new Sort()
            ]);
        }

        $errors[] = 'Укажите дату!';

        return ['status'=>0,'errors'=>$errors];

    }


    /**
     * Отчет - по cargo
     * 10.10.3.30:9090/reportcashier/cargo?Filter[date_from]=2021-01-01&Filter[client_type]=id&Filter[type]=cash
     * @var int client_type
     * @var int company
     * @var int package_type_id
     * @var string payment_type Process::$payment_type[]
     * @return mixed
     */
    public function actionCargo()
    {

        // дата выписки билета
        $date_in = date('Y-m-d', strtotime(Yii::$app->request->get('Filter')['date_from']));
        $date_to = date('Y-m-d', strtotime(Yii::$app->request->get('Filter')['date_to'] . ' + 1 day'));

        $client_type = Yii::$app->request->get('Filter')['client_type']; // тип клиента
        $company = Yii::$app->request->get('Filter')['company']; // компания

        $package_type = Yii::$app->request->get('Filter')['package_type']; // тип груза
        $payment_type = Yii::$app->request->get('Filter')['payment_type']; // тип оплаты

        $errors = [];

        // если указан неверный тип оплаты
        if($payment_type && !in_array($payment_type,Process::$payment_types)){
            $errors[] = 'Укажите тип оплаты!';
        }

        if (!$errors && $date_in != '1970-01-01' && isset($date_to)) {

            $query = Cargo::find()
                ->leftJoin('order o',"o.type_id=cargo.id AND o.`type`='cargo'")
                ->leftJoin('process p','p.order_id=o.id')
                ->where(['between', 'cargo.created_at', $date_in, $date_to])
                ->orderBy('cargo.created_at');

            if($client_type) $query->andWhere(['cargo.client_type_id'=>$client_type]);
            if($company) $query->andWhere(['cargo.company_id'=>$company]);
            if($package_type) $query->andWhere(['cargo.package_type_id'=>$package_type]);
            if($payment_type) $query->andWhere(['p.payment_type'=>$payment_type]);

            return new ActiveDataProvider([
                'query' => $query,
                'sort' => new Sort()
            ]);
        }

        $errors[] = 'Укажите дату!';

        return ['status'=>0,'errors'=>$errors];

    }
    /**
     * Отчет - по возвратам !!билетов-ticket!!
     * 10.10.3.30:9090/reportcashier/return?Filter[date_from]=2021-01-01&Filter[payment_type]=cash
     * @return mixed
     */
    public function actionReturn()
    {

        // проверка скрипта

        // дата выписки билета
        $date_in = date('Y-m-d', strtotime(Yii::$app->request->get('Filter')['date_from']));
        $date_to = date('Y-m-d', strtotime(Yii::$app->request->get('Filter')['date_to'] . ' + 1 day'));

        $payment_type = Yii::$app->request->get('Filter')['payment_type']; // тип оплаты

        $errors = [];

        // если указан неверный тип оплаты
        if($payment_type && !in_array($payment_type,Process::$payment_types)){
            $errors[] = 'Укажите тип оплаты!';
        }


        if (!$errors && $date_in != '1970-01-01' && isset($date_to)) {

            $query = Ticket::find()
                ->select("ticket.id,ticket.ticket_id,ticket.cost_price,p.summ as summ_return, (ticket.cost_price-p.summ) as comission,CONCAT(`c`.`first_name`,CONCAT(' ', `c`.`second_name`)) as client_name,p.created_at as return_date, p.payment_type as payment_type, p.comment as comment")
                ->leftJoin('client c',"c.id=ticket.client_id")
                ->leftJoin('order o',"o.type_id=ticket.id AND o.`type`='ticket'")
                ->leftJoin('process p','p.order_id=o.id')
                ->where(['between', 'ticket.created_at', $date_in, $date_to])
                ->andWhere(['p.process_type'=>'return'])
                ->orderBy('ticket.created_at');

            if($payment_type) $query->andWhere(['p.payment_type'=>$payment_type]);

            return $query->asArray()->all();

        }

        $errors[] = 'Укажите дату!';

        return ['status'=>0,'errors'=>$errors];

    }



}
