<?php

namespace backend\controllers;

use backend\models\Client;
use yii\data\ActiveDataProvider;

/**
 * Class ClientController
 * @package backend\controllers
 * Клиент
 */
class ClientController extends BaseController
{
    public $modelClass = Client::class;
}
