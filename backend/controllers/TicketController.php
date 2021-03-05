<?php

namespace backend\controllers;

use backend\models\Client;
use backend\models\Ticket;
use backend\models\TourPackage;
use Yii;

/**
 * билеты
 */
class TicketController extends BaseController
{
    public $modelClass = Ticket::class;

    public function actionTest()
    {
        return Ticket::findOne(310)->commission;
    }

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

        // Туда билет
        $ticketDep = new Ticket();
        $ticketDep->attributes = $post['ticketDep'];

        /*
         * Checking client for existence
         * if the client exists, the $post['clientExists'] is true,
         * and $post['client_id'] is sent
         */
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

        $isTwoEnded = $post['twoEnded'];

        if (empty($errors)) {
            if ($isTwoEnded === true) {
                // Обратно билет
                $ticketRet = new Ticket();
                $ticketRet->attributes = $post['ticketRet'];

                if ($ticketDep->validate() && $client->validate() && $ticketDep->validate()) {
                    $client->save();

                    $ticketDep->client_id = $client->id;
                    $ticketDep->save();

                    $ticketRet->client_id = $client->id;
                    $ticketRet->parent_id = $ticketDep->id;
                    $ticketRet->save();

                    Yii::$app->response->setStatusCode(204);
                    return ['message' => 'Success'];

                } else {
                    $client->validate();
                    $ticketDep->validate();
                    $ticketRet->validate();

                    $errors['ticket'] = $ticketDep->getErrors() ?? [];
                    $errors['ticket'][] = $ticketRet->getErrors() ?? [];
                    $errors['client'] = $client->getErrors() ?? [];
                }

            } else {
                if ($client->validate() && $ticketDep->validate()) {
                    $client->save();

                    $ticketDep->client_id = $client->id;
                    $ticketDep->save();

                    Yii::$app->response->setStatusCode(204);
                    return ['message' => 'Success'];

                } else {
                    $client->validate();
                    $ticketDep->validate();

                    $errors['ticket'] = $ticketDep->getErrors() ?? [];
                    $errors['client'] = $client->getErrors() ?? [];
                }
            }
        }

        Yii::$app->response->setStatusCode(422);
        return $errors;
    }
}
