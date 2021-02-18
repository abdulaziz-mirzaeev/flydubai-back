<?php

namespace backend\controllers;

use backend\models\Cargo;
use backend\models\Order;
use backend\models\Process;
use backend\models\Ticket;
use backend\models\TourPackage;
use backend\models\Visa;
use common\helpers\ArrayObject;
use common\helpers\Timer;
use ReflectionClass;
use Yii;
use backend\models\Cashier;
use yii\data\ActiveDataProvider;
use yii\data\Sort;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * билеты
 */
class TestController extends BaseController
{

    public $modelClass = Process::class;
    /**
     *
     * 10.10.3.30:9090/test/add
     * @return mixed
     */
    public function actionAdd()
    {

        $types = [
            'VISA',
            'TICKET',
            'CARGO',
            'TOUR_PACKAGE'
        ];

        $payment = [
            'TRANSFER',
            'CASH',
            'TERMINAL'
        ];

        $statuses = [
          'BOOKED',
          'PAID',
          'CANCEL',
          'RETURN'
        ];

        $y = '2021'; // год

        // по месяцам
        for($m=1;$m<2;$m++){
            $cnt = mt_rand(30,75);
            for ($k=1;$k<$cnt;$k++){

                $_type = $types[mt_rand(0,3)];

                $d = mt_rand(7,31); // любой день
                $h = mt_rand(9,22); // любой час
                $date = date('Y-m-d H:i:s',strtotime($y .'-'.$m.'-'.$d .' '.$h.':00:00'));

                d($date . ' ' . $_type);

                switch($_type){
                    case 'TICKET':
                        $type = $this->createticket($date);
                        break;
                    case 'VISA':
                        $type = $this->createvisa($date);
                        break;
                    case 'CARGO':
                        $type = $this->createcargo($date);
                        break;
                    case 'TOUR_PACKAGE':
                        $type = $this->createTour($date);
                        break;
                }
                if($type) {

                    d('создание: ' . $_type . ' ' . $type->id);

                    $summ = mt_rand(100, 999) * 1000;
                    $payment_type = $payment[mt_rand(0, 2)];
                    $summ_terminal = (int)($summ * (mt_rand(0, 5) / 10));
                    $nds = 0;

                    $order = new Order();
                    $order->number = str_pad($m . '-' . $k, 10, '0');
                    $order->type = $_type;
                    $order->type_id = $type->id;
                    $order->operator_id = mt_rand(1, 3);
                    $order->status = $statuses[1]; // $statuses[mt_rand(0, 3)];
                    $order->created_at = $date;
                    $order->summ = $summ;
                    $order->summ_terminal = $summ_terminal;
                    $order->nds = $nds;
                    $order->payment_type = $payment_type;
                    $order->save();
                    // сменить дату на нужную
                    $order->created_at = $date;
                    $order->save();

                    d('создание: заказа ' . $order->id);


                    $process = new Process();
                    $process->order_id = $order->id;
                    $process->summ = $summ;
                    $process->summ_terminal = $summ_terminal;
                    $process->nds = $nds;
                    $process->cashier_id = 1;
                    $process->process_type = 'PAID';
                    $process->payment_type = $payment_type;
                    $process->status = 0;
                    $process->status_director = 0;
                    $process->comment = 'comment';
                    $process->created_at = $date;
                    $process->save();
                    // сменить дату на нужную, т.к beforesave заменяет на текущую
                    $process->created_at = $date;
                    $process->save();

                    d('создание: process ' . $process->id);
                }else{
                    d($type->getErrors(),1);
                }

            }

        }
        exit;


    }

    // создание билета
    private function createTicket($date){
        $price = mt_rand(10,95)*10000;
        $model = new Ticket();
        $model->created_at = $date;
        $model->flight_number = 'FN-'. mt_rand(1,100);
        $model->flight_route = 'Route-'.mt_rand(1,100);
        $model->cost_price  = $price;
        $model->sell_price = $price + mt_rand(1,9)*100000;
        $model->tariff_id  = 1;
        $model->tariff_type_id  = 1;
        $model->pnr  = uniqid();
        $model->client_id  = rand(59,130);
        $model->passenger_count = mt_rand(1,4);
        $model->class  = 'AA';
        $model->comment  = 'comment';
        $model->flight_date  = $date;
        $model->payment_at  = $date;


        if($model->save()) {
            $model->created_at = $date;
            $model->save();
            return $model;
        }
        d($model->getErrors(),1);


        return false;


    }

    // создание визы
    private function createVisa($date){
        $price = mt_rand(10,95)*10000;
        $model = new Visa();
        $model->created_at = $date;
        $model->number = 'VN-'. mt_rand(1,100);
        $model->cost_price  = $price;
        $model->sell_price = $price + mt_rand(1,9)*100000;
        $model->visa_type_id  = 1;
        $model->visa_partner_id  = 1;
        $model->client_id  = rand(59,130);;
        $model->comment  = 'comment';
        $model->flight_date  = $date;
        $model->flight_return_date  = $date;
        $model->payment_at  = $date;
        if($model->save()) {
            $model->created_at = $date;
            $model->save();
            return $model;
        }
        d($model->getErrors(),1);
        return false;

    }

    // создание cargo
    private function createCargo($date){

        $client_type = mt_rand(1,2);

        $price = mt_rand(10,95)*10000;
        $model = new Cargo();
        $model->created_at = $date;
        $model->air_waybill = 'ID-'. mt_rand(1,100);
        $model->cost_price  = $price;
        $model->sell_price = $price + mt_rand(1,9)*100000;
        $model->client_type_id  = $client_type;
        $model->client_id  = $client_type==1 ? rand(59,130) : null;
        $model->company_id  = $client_type==2 ? 1 : null;
        $model->comment  = 'comment';
        $model->package_amount  = mt_rand(1,10); // кол-во
        $model->package_weight  = mt_rand(1,1000); // вес в кг
        $model->package_type_id  = mt_rand(1,2);
        $model->payment_at  = $date;
        if($model->save()) {
            $model->created_at = $date;
            $model->save();
            return $model;
        }
        d($model->getErrors(),1);
        return false;

    }

    // создание турпакета
    private function createTour($date){
        $price = mt_rand(10,95)*10000;
        $model = new TourPackage();
        $model->created_at = $date;
        $model->cost_price  = $price;
        $model->sell_price = $price + mt_rand(1,9)*100000;
        $model->tour_id  = 1;
        $model->tour_partner_id  = 1;
        $model->tour_operator_id  = 1;
        $model->client_id  = rand(59,130);
        $model->comment  = 'comment';
        $model->payment_at  = $date;
        if($model->save()) {
            $model->created_at = $date;
            $model->save();
            return $model;
        }
        d($model->getErrors(),1);
        return false;

    }

    public function actionInnerTest(){

        //d('asdasdasd');

        return ['status'=>1];

    }



}




