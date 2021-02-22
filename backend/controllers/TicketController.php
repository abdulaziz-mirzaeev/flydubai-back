<?php

namespace backend\controllers;

use backend\models\Ticket;
use Yii;

/**
 * билеты
 */
class TicketController extends BaseController
{
    public $modelClass = Ticket::class;

    public function actionTest()
    {
        return Ticket::findOne(310)->commission;
    }
}
