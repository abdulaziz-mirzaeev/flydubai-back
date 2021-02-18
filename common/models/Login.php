<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class Login extends ActiveRecord
{
    public static function tableName()
    {
        return 'user';
    }

    public function rules()
    {
        return [
            [['username', 'password_hash'], 'required'],
            [['username'], 'string', 'max' => 14],
            [['password_hash'], 'string', 'max' => 512],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Имя пользователя',
            'password_hash' => 'Пароль'
        ];
    }
}