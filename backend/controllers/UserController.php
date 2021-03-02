<?php

namespace backend\controllers;

use common\models\User;
use Yii;
use yii\rest\ActiveController;


class UserController extends BaseController
{
    public $modelClass = User::class;


    protected function verbs()
    {
        $verbs = parent::verbs();

        $verbs['get'] = ['POST'];

        return $verbs;
    }

    public function actions()
    {
        $action = parent::actions();
        //d($action,1);
        unset($action['update']);
        return $action;
    }

    public function actionUpdate($id)
    {
        $errors = [];
        if ($user = User::findOne($id)) {
            $post = Yii::$app->request->post();
            $user->username = $post['username'];
            $user->email = $post['email'];
            $user->role = $post['role'];
            if ($user->save()) {
                return ['status' => 1];
            }
            $errors[] = $user->getErrors();

        } else {
            $errors[] = 'Пользователь не найден!';
        }
        return ['status' => 0, 'errors' => $errors];


    }


    // поиск по токену
    function actionGet()
    {

        $post = Yii::$app->request->post();

        return User::find()->where(['token' => $post['token']])->one();
    }

}