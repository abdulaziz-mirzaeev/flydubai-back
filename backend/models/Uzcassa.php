<?php

namespace backend\models;

use common\helpers\Curl;
use Yii;

class Uzcassa extends BaseModel
{

    // получение токена
    public static function getToken()
    {

        $result = Curl::run('/api/login', 'post');

        if (isset($result['access_token'])) {

            return $result['access_token'];
        }
        return false;

    }


}
