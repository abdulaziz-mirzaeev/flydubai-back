<?php

namespace backend\controllers;

use ReflectionClass;
use Yii;

/**
 * билеты
 */
class InfoController extends BaseController
{
    // public $modelClass = Ticket::class;



    public function actionGet(){

        $model = Yii::$app->request->get('model');
        //d($model,1);
        //$model = new Order();
        if(class_exists($model)) {

            //$const = new ReflectionClass($model::class);

           // d([$const->getConstants()], 1);

        }else{
            d('class not found',1);
        }

    }

}
