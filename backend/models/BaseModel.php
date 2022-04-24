<?php


namespace backend\models;


use Yii;
use yii\db\ActiveRecord;

class BaseModel extends ActiveRecord
{

    public function beforeSave($insert)
    {
        if ( parent::beforeSave($insert) ) {

            if ( $this->isNewRecord ) {
                $this->created_at = date('Y-m-d H:i:s', time());
                $this->created_by = Yii::$app->user->identity->id;
            } else {
                // у некоторых моделей нет возможности изменения, только создание
                if ( $this->hasAttribute('modified_at') ) {
                    $this->modified_at = date('Y-m-d H:i:s', time());
                    $this->modified_by = Yii::$app->user->identity->id;
                }
            }

            return true;

        }

        return false;
    }

    public function extraFields()
    {
        return [];
    }

}