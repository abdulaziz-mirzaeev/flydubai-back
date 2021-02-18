<?php

namespace backend\controllers;

use backend\models\Ticket;
use backend\models\TourPackage;
use Yii;
use backend\models\Cashier;
use yii\data\ActiveDataProvider;
use yii\data\Sort;
use yii\web\NotFoundHttpException;

/**
 * билеты
 */
class TourpackageController extends BaseController
{
    public $modelClass = TourPackage::class;
}
