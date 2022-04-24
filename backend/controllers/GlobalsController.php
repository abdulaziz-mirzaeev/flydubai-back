<?php

namespace backend\controllers;

use backend\models\Cashier;
use backend\models\Cdr;
use backend\models\Client;
use backend\models\Order;
use backend\models\Process;
use common\models\User;
use yii\rest\Controller;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT');
header("Access-Control-Allow-Headers: *");
header('Content-Type: application/json');

class GlobalsController extends Controller
{
    public function actionIndex()
    {
        return [
            'orderStatuses' => Order::order_statuses,
            'paymentTypes' => Process::paymentType,
            'processTypes' => Process::processType,
            'clientTypes' => Client::clientTypes,
            'orderTypes' => Order::order_types,
            'cashierTypes' => Cashier::cashier_types,
            'statusCallCenter' => Cdr::status_callcenter,
            'roles' => User::roles
        ];
    }
}
