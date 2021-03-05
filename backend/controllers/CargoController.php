<?php

namespace backend\controllers;

use backend\models\Cargo;
use backend\models\Client;
use backend\models\TourPackage;
use Yii;

/**
 * Class CargoController
 * @package backend\controllers
 * Карго
 */
class CargoController extends BaseController
{
    public $modelClass = Cargo::class;

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

        $cargo = new Cargo();
        $cargo->attributes = $post['cargo'];

        if ($post['clientExists'] === true) {
            $client_id = $post['client_id'];
            $client = Client::findOne(['id' => $client_id]);
            if (empty($client)) {
                $errors['client'][] = ['Клиент не найден'];
            }
        } else {
            $client = new Client();
            $client->attributes = $post['client'];
        }


        if ( empty($errors) ) {
            if ( $cargo->validate() && $client->validate() ) {
                $client->save();

                $cargo->client_id = $client->id;
                $cargo->save();

                Yii::$app->response->setStatusCode(204);
                return ['message' => 'Success'];

            } else {
                $client->validate();
                $cargo->validate();

                $errors['cargo'] = $cargo->getErrors() ?? [];
                $errors['client'] = $client->getErrors() ?? [];
            }
        }

        Yii::$app->response->setStatusCode(422);
        return $errors;
    }
}
