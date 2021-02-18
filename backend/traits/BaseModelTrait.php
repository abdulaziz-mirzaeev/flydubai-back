<?php


namespace app\traits;


use backend\models\Order;
use backend\models\Process;
use ReflectionClass;
use Yii;

trait BaseModelTrait
{

    public static function baseModelClass()
    {
        $array = explode("\\", self::class);
        return end($array);
    }

    // информация о модели
    public static function getInfo(){
        $const = new ReflectionClass(self::class);

        $extra_fields = self::extraFields();

        $globals = [
            'orderStatuses' => Order::order_statuses,
            'paymentTypes' => Process::paymentType,
            'processTypes' => Process::processType,
        ];

       return [
           'constants' => $const->getConstants(),
           'attributes' => self::attributeLabels(),
           'globals' => $globals,
           'extraFields' => $extra_fields,
           'rules' => self::rules(),
       ];

    }




}