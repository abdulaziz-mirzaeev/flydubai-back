<?php

namespace backend\controllers;

use backend\models\Client;
use backend\models\Company;
use yii\data\ActiveDataProvider;

/**
 * Class ClientController
 * @package backend\controllers
 * Клиент
 */
class CompanyController extends BaseController
{
    public $modelClass = Company::class;
}
