<?php

namespace backend\controllers;

use common\models\User;
use Yii;
use yii\rest\ActiveController;


class UserController extends BaseController
{
    public $modelClass = User::class;

    public function actions()
    {
        $action = parent::actions();
        //d($action,1);
        unset($action['update']);
//        unset($action['create']);
        return $action;
    }



    public function actionUpdate($id)
    {
        $errors = [];
        if ( $user = User::findOne($id) ) {
            $post = Yii::$app->request->post();
            $user->username = $post['username'];
            $user->email = $post['email'];
            $user->role = $post['role'];
            if ( $user->save() ) {
                return ['status' => 1];
            }
            $errors[] = $user->getErrors();

        } else {
            $errors[] = 'Пользователь не найден!';
        }
        return ['errors' => $errors];
    }

    public function actionCreate()
    {

    }

    function actionGet()
    {

        $post = Yii::$app->request->post();

        return User::find()->where(['token' => $post['token']])->one();
    }


    // поиск по токену

    protected function verbs()
    {
        $verbs = parent::verbs();

        $verbs['get'] = ['POST'];

        return $verbs;
    }

}