<?php

namespace backend\controllers;

use Yii;
use yii\filters\auth\HttpBearerAuth;
use common\models\User;
use common\models\Login;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT');
header("Access-Control-Allow-Headers: *");

class Bearer extends HttpBearerAuth
{
    public function handleFailure($response)
    {
        Yii::$app->response->setStatusCode(403);
    }
}

class SiteController extends \yii\rest\Controller
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();


        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
            'cors' => [
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => '*',
                'Access-Control-Allow-Credentials' => true,
                'Access-Control-Max-Age' => 86400,
                'Access-Control-Expose-Headers' => [],
            ]
        ];

        /* $behaviors['authenticator'] = [
             'class' => Bearer::className(),
             'except' => ['registration','login']
         ];*/

        return $behaviors;
    }


    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function init()
    {

        if (Yii::$app->request->isOptions) {
            Yii::$app->response->setStatusCode(200);
            exit;
        }

    }

    public function actionChangepassword()
    {

        $data = Yii::$app->request->post();

        if(!isset($data['id'])) return ['status'=>0,'ID пользователя не задан!'];

        if ($user = User::findOne((int)$data['id'])) {

            $user->generateAuthKey();
            $user->setPassword($data['password']);
            $user->save();
            return ['status' => 1];

        }

        return ['status' => 0, 'errors' => 'Пользователь не найден!'];

    }


    public function actionRegistration()
    {
        $model = new User();

        if (Yii::$app->request->isPost) {

            $data = Yii::$app->request->post();

            //d($data,1);

            $model->username = !empty($data['username']) ? $data['username'] : 'no data';
            $model->email = !empty($data['email']) ? $data['email'] : 'noemail@mail.ru';
            $model->setPassword($data['password_hash']);
            $model->generateAuthKey();
            $model->status = 10;

            if($data['role']) {
                $model->role = $data['role'];
            }

            $token = substr(Yii::$app->getRequest()->getCsrfToken(), 0, 10);
            $model->token = $token;

            if ($model->validate()) {
                $model->save();
                Yii::$app->response->setStatusCode(201);

                return ['user_id' => $model->id, 'token' => $token, 'username' => $model->username];
            } else {
                Yii::$app->response->setStatusCode(422);
                return $model->getErrors();
            }
        }


    }


    public function actionLogin()
    {
        $model = new Login();

        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();

            $model->username = $data['username'];
            $model->password_hash = $data['password_hash'];

            $user = User::findOne([
                'username' => $model->username
            ]);

            if (!$model->validate()) {
                Yii::$app->response->setStatusCode(422);
                return $model->getErrors();
            }

            if ( empty($user) || !$user->validatePassword($data['password_hash']) ){
                Yii::$app->response->setStatusCode(404);
                return ['status' => 0, 'Incorrect login or password'];
            } else {
                //$user = User::findOne($user['id']);
                $token = substr(Yii::$app->getRequest()->getCsrfToken(), 0, 10);
                $user->token = $token;
                $user->save();
                return ['token' => $token,'user_id'=>$user->id];
            }
        }

        return ['status' => 0,'errors'=>'Request is not post'];
    }

    public function actionLogout()
    {
        $token = substr(Yii::$app->request->headers->get('authorization'), 7);
        $user = User::findOne(['token' => $token]);
        $user->token = '';
        $user->save(false);
        Yii::$app->response->setStatusCode(200);

        return 'Logout success';
    }


    public function beforeAction($action)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return parent::beforeAction($action);
    }

    public function actionTest(){
        return ['status'=>200];
    }

}
