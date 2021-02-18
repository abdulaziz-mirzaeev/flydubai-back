<?php

namespace backend\controllers;

use backend\models\Balance;
use Yii;

/**
 * баланс с 1С
 */
class BalanceController extends BaseController
{
    public $modelClass = Balance::class;

    protected function verbs()
    {
        $verbs = parent::verbs();

        $verbs['add'] = ['POST'];

        return $verbs;
    }

    // получение баланса из 1с от пользователя balance1c
    public function actionAdd(){

        if(!Yii::$app->request->isPost) {
            $errors[] = 'Данные должны передаваться методом POST!';
            return ['status' => 0, 'errors' => $errors];
        }

        $postData = file_get_contents('php://input');
        $post = json_decode($postData, true);


        /*$filename = $_SERVER['DOCUMENT_ROOT'] . '/balance_'.time().'.txt';
        file_put_contents($filename,$post);*/

        if(!isset($post['summ'])) $errors[] = 'Сумма не задана!';
        if(!isset($post['invoice'])) $errors[] = 'Рассчетный счет не задан!';
        if(!isset($post['date'])) {
            $errors[] = 'Дата не задана!';
        }elseif(!strtotime($post['date'])) {
            $errors[] = 'Задана неверная дата!';
        }

        if(!$errors) {
            if (!$balance = Balance::findOne(1)) {
                $balance = new Balance();
            }
            $balance->id = 1; // всегда 1 , т.к. всего 1 запись в таблице - это баланс  рассчетного счета
            $balance->summ = str_replace(',','.',$post['summ']);
            $balance->invoice = $post['invoice'];
            $balance->date = date('Y-m-d H:i:s',strtotime($post['date']));
            if ($balance->save()) {
                return ['status' => 1];
            }
            $errors[] = $balance->getErrors();
        }

        return ['status'=>0,'info'=>$errors];

    }

}
