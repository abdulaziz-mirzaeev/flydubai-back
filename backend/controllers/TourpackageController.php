<?php

namespace backend\controllers;

use backend\models\Client;
use backend\models\Ticket;
use backend\models\TourPackage;
use Yii;
use backend\models\Cashier;
use yii\data\ActiveDataProvider;
use yii\data\Sort;
use yii\web\NotFoundHttpException;

/**
 * билеты
 */
class TourpackageController extends BaseController
{
    public $modelClass = TourPackage::class;

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

        $tourPackage = new TourPackage();
        $tourPackage->attributes = $post['tour_package'];

        if ( $post['clientExists'] ) {
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
            if ( $tourPackage->validate() && $client->validate() ) {
                $client->save();
                $tourPackage->client_id = $client->id;
                $tourPackage->save();

                Yii::$app->response->setStatusCode(204);
                return ['message' => 'Success'];

            } else {
                $tourPackage->validate();
                $client->validate();

                $errors['tour_package'] = $tourPackage->getErrors() ?? [];
                $errors['client'] = $client->getErrors() ?? [];
            }
        }

        Yii::$app->response->setStatusCode(422);
        return $errors;
    }
}
