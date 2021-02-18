<?php
namespace app\validators;

use yii\validators\Validator;

class PriceValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        if ($model->$attribute < $model->cost_price) {
            $this->addError($model, $attribute, 'Сумма Реализации не должна быть меньше себестоимости!');
        }
    }

}