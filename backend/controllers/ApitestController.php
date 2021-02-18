<?php

namespace backend\controllers;

use backend\models\ApiTest;
use backend\models\Client;

class ApitestController extends BaseController
{
    public $modelClass = ApiTest::class;

    public function actionWrite($num) {
        $cell = ApiTest::findOne(1);
        $cell->number = $num;
        $cell->save();

        if ( !empty($cell->getErrors() ) ) {
            return $cell->getErrors();
        }

        \Yii::$app->response->statusCode = 200;
        return ['message' => 'success'];
    }

    protected function verbs()
    {
        $verbs = parent::verbs();

        $verbs['write'] = ['GET'];

        return $verbs;
    }
}
