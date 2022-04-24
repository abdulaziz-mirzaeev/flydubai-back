<?php

namespace backend\controllers;

use backend\models\Client;
use backend\models\Ticket;
use backend\models\TicketClient;
use backend\models\TourPackage;
use Yii;
use yii\helpers\ArrayHelper;

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
        unset($actions['create'], $actions['update']);
        return $actions;
    }

    public function actionCreateOne()
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

        $isTwoEnded = $post['twoEnded'];

        if ( empty($errors) ) {
            if ( $isTwoEnded === true ) {
                // Обратно билет
                $ticketRet = new Ticket();
                $ticketRet->attributes = $post['ticketRet'];

                if ( $ticketDep->validate() && $client->validate() && $ticketDep->validate() ) {
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
                if ( $client->validate() && $ticketDep->validate() ) {
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


    public function actionCreate()
    {

        $post = $this->getPost();

        $errors = [];

        $clients = $post['clients'];

        // Туда билет
        $ticketDep = new Ticket();
        $ticketDep->attributes = ArrayHelper::getValue($post, 'ticketDep');

        $newClients = [];
        $clientIds = [];

        if ( !empty($clients) ) {
            foreach ( $clients as $id => $client ) {
                $exists = ArrayHelper::getValue($client, 'clientExists');
                if ( $exists ) {

                    $client_id = ArrayHelper::getValue($client, 'client_id');

                    $clientIds[] = $client_id['id'];

                    $clientObject = Client::findOne(['id' => $client_id['id']]);


                    if ( empty($clientObject) ) {
                        $errors['client']["$id"] = ['Клиент не найден'];
                    }

                } else {

                    $clientObject = new Client();
                    $clientObject->attributes = $client['model'];

                    if ( !$clientObject->validate() && !empty($clientObject->getErrors()) ) {
                        $errors['client']["$id"] = $clientObject->getErrors() ?? [];
                    }

                    $newClients[] = $clientObject;

                }

            }
        }


        $isTwoEnded = ArrayHelper::getValue($post, 'twoEnded');

        if ( empty($errors) ) {
            if ( $isTwoEnded ) {

                // Обратно билет
                $ticketRet = new Ticket();
                $ticketRet->attributes = $post['ticketRet'];


                $ticketDep = new Ticket();
                $ticketDep->attributes = $post['ticketDep'];


                if ( $ticketDep->validate() && $ticketRet->validate() ) {
                    if ( !empty($newClients) ) {
                        foreach ( $newClients as $client ) {
                            $client->save();
                            $clientIds[] = $client->id;
                        }
                    }
                    $ticketDep->passenger_count = count($clientIds);
                    $ticketDep->save();

                    $ticketRet->passenger_count = count($clientIds);
                    $ticketRet->parent_id = $ticketDep->id;
                    $ticketRet->save();

                    foreach ( $clientIds as $clientId ) {
                        $ticketClient = new TicketClient();
                        $ticketClient->ticket_id = $ticketDep->id;
                        $ticketClient->client_id = $clientId;
                        $ticketClient->save();
                    }

                    Yii::$app->response->setStatusCode(204);
                    return ['message' => 'Success'];

                } else {
                    $ticketDep->validate();
                    $ticketRet->validate();

                    $errors['ticket'] = $ticketDep->getErrors() ?? [];
                    $errors['ticket'][] = $ticketRet->getErrors() ?? [];

                }

            } else {
                if ( $ticketDep->validate() ) {

                    if ( !empty($newClients) ) {
                        foreach ( $newClients as $client ) {
                            $client->save();
                            $clientIds[] = $client->id;
                        }
                    }

                    $ticketDep->passenger_count = count($clientIds);
                    $ticketDep->save();

                    foreach ( $clientIds as $clientId ) {
                        $ticketClient = new TicketClient();
                        $ticketClient->ticket_id = $ticketDep->id;
                        $ticketClient->client_id = $clientId;
                        $ticketClient->save();
                    }

                    Yii::$app->response->setStatusCode(204);
                    return ['message' => 'Success'];

                } else {
                    $ticketDep->validate();

                    $errors['ticket'] = $ticketDep->getErrors() ?? [];
                }
            }
        }

        Yii::$app->response->setStatusCode(422);
        return $errors;
    }

    public function actionUpdate($id)
    {
        $errors = [];

        $post = $this->getPost();


        $ticketDep = Ticket::findOne($id);
        $ticketRet = Ticket::findOne($ticketDep->child->id);

        $ticketDep->attributes = $post['ticketDep'];
        $ticketRet->attributes = $post['ticketRet'];

        if ( !$ticketDep->validate() || !$ticketRet->validate() ) {
            $errors['ticket'] = $ticketDep->getErrors();
            $errors['ticket'][] = $ticketRet->getErrors();
        }

        /** @var Client[] $clients */
        $clients = [];
        foreach ( $post['clients'] as $id => $client ) {
            $clientOld = Client::findOne($client['id']);
            $clientOld->attributes = $client;

            if ( !$clientOld->validate() ) {
                $errors['client']["$id"] = $clientOld->getErrors();
            }

            $clients[] = $clientOld;
        }

        if ( empty($errors) ) {
            $ticketDep->save();
            $ticketRet->save();

            foreach ( $clients as $client ) {
                $client->save();
            }

            Yii::$app->response->setStatusCode(204);
            return ['message' => 'Success'];
        }

        Yii::$app->response->setStatusCode(422);
        return $errors;


    }

}
