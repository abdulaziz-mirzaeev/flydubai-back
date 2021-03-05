<?php

namespace backend\controllers;

use backend\models\Client;
use backend\models\TourPackage;
use backend\models\Visa;
use Yii;

/**
 * Class VisaController
 * @package backend\controllers
 * Виза
 */
class VisaController extends BaseController
{
    public $modelClass = Visa::class;

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        return $actions;
    }

    public function actionCreate()
    {
        $post = $this->getPost();
        $errors = [];

        $visa = new Visa();
        $visa->attributes = $post['visa'];

        if ( $post['clientExists'] === true ) {
            $client_id = $post['client_id'];
            $client = Client::findOne(['id' => $client_id]);
            if ( empty($client) ) {
                $errors['client'][] = ['Клиент не найден'];
            }
        } else {
            $client = new Client();
            $client->attributes = $post['client'];
        }


        if ( empty($errors) ) {
            if ( $visa->validate() && $client->validate() ) {
                $client->save();

                $visa->client_id = $client->id;
                $visa->save();

                Yii::$app->response->setStatusCode(204);
                return ['message' => 'Success'];

            } else {
                $visa->validate();
                $client->validate();

                $errors['visa'] = $visa->getErrors() ?? [];
                $errors['client'] = $client->getErrors() ?? [];
            }
        }

        Yii::$app->response->setStatusCode(422);
        return $errors;
    }
}
