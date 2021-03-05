<?php

namespace backend\controllers;

use backend\models\Notification;
use backend\models\Order;
use backend\models\Process;
use backend\models\Uzcassa;
use common\helpers\Curl;
use common\models\User;
use Yii;
use backend\models\Cashier;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

/**
 * Class CashierController
 * @package backend\controllers
 * Касса
 */
class CashierController extends BaseController
{

    public $modelClass = Cashier::class;


//    public function actions()
//    {
//        $actions = parent::actions();
//
//        unset($actions['index']);
//
//        return $actions; // TODO: Change the autogenerated stub
//    }

    // проверка данных

    public function actionIndexSub()
    {

        // получаем токен
        if ( $token = Uzcassa::getToken() ) {

            /**
             * результат запроса: /api/cashiers
             *
             * [    {
             * "id": 45354160,
             * "createdBy": null,
             * "createdDate": null,
             * "lastModifiedBy": null,
             * "lastModifiedDate": null,
             * "login": "998932553645",
             * "telegramLogin": null,
             * "password": null,
             * "activated": true,
             * "langKey": "ru",
             * "authorities": [
             * "ROLE_CASHIER"
             * ],
             * "permissions": null,
             * "fullName": {
             * "firstName": "Favid",
             * "lastName": "Davidov",
             * "patronymic": "Covid",
             * "phone": null,
             * "name": "Davidov Favid"
             * },
             * "dismissed": true,
             * "owner": false,
             * "started": false,
             * "branchId": 95149,
             * "branchName": "YANGI TEXNOLOGIYALAR ILMIY-AXBOROT MARKAZI",
             * "companyId": null,
             * "companyName": null,
             * "companyInn": null,
             * "activationKey": null,
             * "resetKey": null,
             * "telegramName": null,
             * "superAdmin": false
             * }
             * ]
             */

            $result = Curl::run('/api/cashiers', 'get', $token);

            if ( is_array($result) && isset($result[0]['id']) ) {
                return $result;
            }

        }

        return ['status' => 0, 'errors' => ['Кассиры не найдены!']];

    }


    // +информация о кассирах филиала

    public function actionTransferconfirms()
    {

        $provider = new ActiveDataProvider([
            'query' => Process::find()->with(['cashierFrom', 'cashier'])->where(['process_type' => Process::TYPE_TRANSFER, 'status_director' => 1]),
            'pagination' => false,
        ]);

        return $provider;
    }

    // список подтвержденных Выведенных средств из кассы

    public function actionExitconfirms()
    {

        $provider = new ActiveDataProvider([
            'query' => Process::find()
                ->with(['cashier'])
                ->where([
                    'process_type' => Process::TYPE_EXIT,
                    'status_director' => Process::DIRECTOR_CONFIRM_TRUE,
                    'status' => 0,
                ]),
            'pagination' => false,
        ]);

        return $provider;
    }


    // список подтвержденных Директором приходов в кассу

    public function actionReturnconfirms()
    {

        $provider = new ActiveDataProvider([
            'query' => Process::find()->with(['cashier'])->where(['process_type' => Process::TYPE_RETURNED, 'status_director' => 1, 'status' => 0]),
            'pagination' => false,
        ]);

        return $provider;
    }

    // список подтвержденных Директором приходов в кассу

    /**
     * Приход в кассу.
     * оплата за услугу (билет, карго, турапкет, виза)
     * @return mixed
     */
    public function actionEnter()
    {

        $post = $this->getPost();
        $errors = $this->validate($post);
        if ( !isset($post['cashier_id']) || !$cashier = Cashier::findOne($post['cashier_id']) )
            $errors[] = 'Касса на найдена!';

        if ( empty($errors) ) {
            $cashier->summ += $post['summ'];

            // приход в кассу
            $process = new Process();
            $process->cashier_id = $cashier->id;
            $process->summ = $post['summ'];
            $process->comment = $post['comment'];
            $process->process_type = Process::TYPE_ENTER;

            if ( $cashier->save() && $process->save() ) {
                $message = 'Поступили средства в кассы ' . $cashier->name . ' на сумму ' . $process->summ;
                Notification::send(Notification::ENTER, $process->id, Notification::STATUS_PROCESS, $message);
                return ['message' => 'Success'];
            }

            if ( $cashier->hasErrors() ) $errors[] = $cashier->getErrors();
            if ( $process->hasErrors() ) $errors[] = $process->getErrors();

        }
        Yii::$app->response->statusCode = 422;
        return ['errors' => $errors];

    }

    public function validate(&$post, $type = null)
    {

        $errors = [];

        if ( !isset($post['summ']) ) $errors[] = 'Не задана сумма для расхода (summ)!';
        if ( !isset($post['cashier_id']) ) $errors[] = 'Не задана касса (cashier_id)!';
        if ( isset($post['cashier_id']) && $post['cashier_id'] == 0 ) $errors[] = 'Не задана касса (cashier_id)!';

        /* switch($type) {
            case Process::TYPE_ENTER:
                break;
            case Process::TYPE_EXIT: //
                break;
            case Process::TYPE_TRANSFER:
                if(!isset($post['cashier_from'])) $errors[] = 'Не задана касса откуда!';
                if(isset($post['cashier_from']) && $post['cashier_from']==0 ) $errors[] = 'Не задана касса откуда (cashier_from)!';
                break;
            case Process::TYPE_RETURNED:
                if(!isset($post['order_id'])) $errors[] = 'Не задан заказ (order_id)!';
                break;
            case Cashier::CONFIRM_EXIT:
                if(!isset($post['id'])) $errors[] = 'Не найден процесс (id)!';
                if(!isset($post['status_director'])) $errors[] = 'Расход не подтвержден директором (status_director)!';
                if(isset($post['status_director']) && $post['status_director']==0) $errors[] = 'Расход не подтвержден директором (status_director)!';
                break;
        } */
        return $errors;

    }

    /**
     * Расход из кассы
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionExit()
    {

        $post = $this->getPost();

        $errors = $this->validate($post);
        if ( !$cashier = Cashier::findOne($post['cashier_id']) ) $errors[] = 'Касса на найдена!';

        if ( !$errors ) {

            if ( $cashier->summ - $post['summ'] < 0 ) {
                Yii::$app->response->statusCode = 422;
                return ['errors' => 'Расход превышает остаток в кассе!'];
            }

            // для исключения повторного снятия одной и той же суммы, проверка, пока не будет подтверждено директором
            if ( $process = Process::find()->where(['summ' => $post['summ'], 'cashier_id' => $post['cashier_id'], 'process_type' => Process::TYPE_EXIT, 'status' => 0])->one() ) {
                Yii::$app->response->statusCode = 422;
                return ['errors' => 'Процесс уже создан, его необходимо подтвердить директором!'];
            }

            // расход из кассы
            // создаем процесс, но для расхода нужно подтверждение директора actionConfirmExit
            // поэтому из кассы деньги не снимаем, только формируем процесс
            $process = new Process();
            $process->cashier_id = $cashier->id;
            $process->summ = $post['summ'];
            $process->comment = $post['comment'];
            $process->process_type = Process::TYPE_EXIT;

            // перед снятием средств нужно подтверждение директора status_director=1
            $process->status_director = 0;
            $process->status = 0;

            if ( $process->save() ) {
                $message = 'Произведен расход средств из кассы ' . $cashier->name . ' на сумму ' . $process->summ;
                $user_id = User::findOne(['role' => 'director'])->id;
                Notification::send(Notification::CONFIRM_EXIT, $process->id, Notification::STATUS_PROCESS, $message, $user_id); // отправляем уведомление
                return ['message' => 'Success'];
            }

            if ( $cashier->hasErrors() ) $errors[] = $cashier->getErrors();
            if ( $process->hasErrors() ) $errors[] = $process->getErrors();

        }

        Yii::$app->response->statusCode = 422;
        return ['errors' => $errors];
    }


    // подтвреждение снятия д/с
    // GET запрос id - process_id
    public function actionExitcomplete(int $id)
    {

        //$post = $this->getPost();

        if ( !$id > 0 ) $errors[] = 'Не задан процесс (process_id)!';
        if ( !$process = Process::findOne($id) ) $errors[] = 'Процесс не найден!';

        if ( $errors ) return ['status' => 0, 'errors' => $errors];

        if ( $cashier = Cashier::findOne($process->cashier_id) ) {

            if ( $cashier->summ - $process->summ < 0 ) $errors[] = 'Расход превышает остаток в кассе!';

            if ( $process->status_director == 0 ) $errors[] = 'Для снятия д/с необходимо подтверждение Директора!';

            if ( $errors ) return ['status' => 0, 'errors' => $errors];

            $cashier->summ -= $process->summ;

            // расход из кассы
            $process->status = 1;

            if ( $cashier->save() && $process->save() ) {
                $message = 'Произведен вывод средств из кассы ' . $cashier->name . ' на сумму ' . $process->summ;
                Notification::send(Notification::EXIT_SUCCESS, $process->id, Notification::STATUS_PROCESS, $message); // отправляем новое уведомление
                return ['status' => 1];
            }

            if ( $cashier->hasErrors() ) $errors[] = $cashier->getErrors();
            if ( $process->hasErrors() ) $errors[] = $process->getErrors();

        }

        return ['status' => 0, 'errors' => $errors];
    }

    /**
     * Перенос из кассы в кассу
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionTransfer()
    {

        $post = $this->getPost();
        $errors = $this->validate($post);

        if ( !isset($post['cashier_from']) ) $errors[] = 'Не задана касса откуда!';
        if ( isset($post['cashier_from']) && $post['cashier_from'] == 0 ) $errors[] = 'Не задана касса откуда!';

        if ( !$cashier = Cashier::findOne($post['cashier_id']) ) $errors[] = '(Касса Куда) такой кассы не существует!';
        if ( !$cashier_from = Cashier::findOne($post['cashier_from']) ) $errors[] = '(Касса Откуда) такой кассы не существует!';

        if ( $errors ) {
            Yii::$app->response->statusCode = 422;
            return [$errors];
        }

        // если валидация прошла и кассы существуют
        if ( $cashier && $cashier_from ) {

            if ( $cashier_from->summ - $post['summ'] < 0 ) {
                Yii::$app->response->statusCode = 422;
                return ['errors' => 'Расход превышает остаток в кассе!'];
            }

            // для исключения повторного снятия одной и той же суммы, проверка, пока не будет подтверждено директором
            if ( $process = Process::find()->where(['summ' => $post['summ'], 'cashier_id' => $post['cashier_id'], 'cashier_from' => $post['cashier_from'], 'process_type' => Process::TYPE_TRANSFER, 'status_director' => 0, 'status' => 0])->one() ) {
                Yii::$app->response->statusCode = 422;
                return ['errors' => 'Процесс уже создан, его необходимо подтвердить директором!'];
            }

            // приход в кассу
            $process = new Process();
            $process->process_type = Process::TYPE_TRANSFER;
            $process->cashier_from = $cashier_from->id;
            $process->cashier_id = $cashier->id;
            $process->comment = $post['comment'];
            $process->summ = $post['summ'];
            $process->status_director = 0;
            $process->status = 0;

            if ( $cashier->save() && $cashier_from->save() && $process->save() ) {
                $message = 'Произведен перенос средств из кассы ' . $cashier_from->name . ' в кассу ' . $cashier->name . ' на сумму ' . $process->summ;
                Notification::send(Notification::CONFIRM_TRANSFER, $process->id, Notification::STATUS_PROCESS, $message); // отправляем новое уведомление

                return ['message' => 'Success'];
            }

            if ( $cashier->hasErrors() ) $errors[] = $cashier->getErrors();
            if ( $cashier_from->hasErrors() ) $errors[] = $cashier_from->getErrors();
            if ( $process->hasErrors() ) $errors[] = $process->getErrors();

        }

        return ['status' => 0, 'errors' => $errors];

    }

    /**
     * Перенос из кассы в кассу
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    // GET запрос id - process_id
    public function actionTransfercomplete(int $id)
    {
        //$post = $this->getPost();

        if ( !$id > 0 ) $errors[] = 'Не задан процесс (process_id)!';
        if ( !$process = Process::findOne($id) ) $errors[] = 'Процесс не найден!';
        if ( $process && !$cashier = Cashier::findOne($process->cashier_id) ) $errors[] = 'Касса Куда не задана!';
        if ( $process && !$cashier_from = Cashier::findOne($process->cashier_from) ) $errors[] = 'Касса Откуда не задана!';

        // если валидация прошла и кассы существуют
        if ( !$errors && $cashier && $cashier_from ) {

            if ( $cashier->summ - $process->summ < 0 ) $errors[] = 'Расход превышает остаток в кассе!';

            if ( $process->status_director == 0 ) $errors[] = 'Для переноса д/с необходимо подтверждение Директора!';

            if ( $errors ) return ['status' => 0, 'errors' => $errors];

            // перенос из кассы в кассу
            $cashier->summ += $process->summ; // приход
            $cashier_from->summ -= $process->summ; // расход

            $process->status = 1;

            if ( $cashier->save() && $cashier_from->save() && $process->save() ) {
                $message = 'Подтвержден перенос средств из кассы ' . $cashier_from->name . ' в кассу ' . $cashier->name . ' на сумму ' . $process->summ;
                Notification::send(Notification::TRANSFERED, $process->id, Notification::STATUS_PROCESS, $message); // отправляем новое уведомление

                return ['status' => 1];
            }

            if ( $cashier->hasErrors() ) $errors[] = $cashier->getErrors();
            if ( $cashier_from->hasErrors() ) $errors[] = $cashier_from->getErrors();
            if ( $process->hasErrors() ) $errors[] = $process->getErrors();

        }

        return ['status' => 0, 'errors' => $errors];

    }

    /**
     * Возврат денежных средств клиенту ВДС
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $process_id - процесс
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionReturn()
    {

        $post = $this->getPost();

        $errors = $this->validate($post);

        //if (!isset($post['process_id'])) $errors[] = 'Не задан процесс (process_id)!';
        //if (!$process = Process::findOne($post['process_id'])) $errors[] = 'Процесс не найден!';
        if ( !$cashier = Cashier::findOne($post['cashier_id']) ) $errors[] = 'Касса не найдена!';
        if ( !$order = Order::findOne($post['order_id']) ) $errors[] = 'Не найден заказ!';

        if ( Process::findOne([
            'order_id' => $post['order_id'],
            'process_type' => Process::TYPE_RETURNED
        ])
        ) {
            $errors[] = 'Процесс уже создан на эту заявку';
        }

        //if(!$process = Process::find()->where( ['order_id'=>$post['order_id'],'process_type'=>Process::TYPE_ENTER] )->one()) $errors[] = 'Процесс не найден!';

        if ( !$errors ) {

            // процесс по заказу и типу приход

            // вернуть из той же кассы, в которую поступили средства

            if ( $cashier->summ - $post['summ'] < 0 ) {
                Yii::$app->response->setStatusCode(422);
                return ['errors' => 'Расход превышает остаток в кассе!'];
            }

            // для исключения повторного снятия одной и той же суммы, проверка, пока не будет подтверждено директором
            if ( $process = Process::find()->where(['summ' => $post['summ'], 'cashier_id' => $post['cashier_id'], 'process_type' => Process::TYPE_RETURNED, 'status' => 0])->one() ) {
                Yii::$app->response->setStatusCode(422);
                return ['errors' => 'Процесс уже создан, его необходимо подтвердить директором!'];
            }

            // расход из кассы
            $process = new Process();

            $process->order_id = $order->id;
            $process->cashier_id = $cashier->id;
            $process->summ = $post['summ']; // ??? та же сумма, что и поступила ??? нужно ли учесть комиссию
            $process->process_type = Process::TYPE_RETURNED;

            //$order->status = Order::STATUS_RETURNED; // меняем статус заказа

            if ( $order->save() && $cashier->save() && $process->save() ) {
                $message = 'Произведен возврат средств из кассы ' . $cashier->name . ' на сумму ' . $process->summ;
                $user_id = User::findOne(['role' => 'director']);
                Notification::send(Notification::CONFIRM_RETURN, $process->id, Notification::STATUS_PROCESS, $message, $user_id);
                return ['status' => 1];
            }

            if ( $order->hasErrors() ) $errors[] = $order->getErrors();
            if ( $cashier->hasErrors() ) $errors[] = $cashier->getErrors();
            if ( $process->hasErrors() ) $errors[] = $process->getErrors();
        }

        Yii::$app->response->setStatusCode(422);
        return $errors;

    }

    /**
     * Возврат денежных средств клиенту ВДС
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $process_id - заказ
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    // GET запрос id - process_id
    public function actionReturncomplete(int $id)
    {

        //$post = $this->getPost();

        if ( !$id > 0 ) $errors[] = 'Не задан процесс (process_id)!';
        if ( !$process = Process::findOne($id) ) $errors[] = 'Процесс не найден!';
        if ( $process && !$cashier = Cashier::findOne($process->cashier_id) ) $errors[] = 'Касса не найдена!';
        if ( $process && !$order = Order::findOne($process->order_id) ) $errors[] = 'Не найден заказ!';

        if ( !$errors ) {

            // процесс по заказу и типу приход
            //$process = Process::find()->where( ['order_id'=>$post['order_id'],'process_type'=>Process::TYPE_ENTER] )->one();

            // вернуть из той же кассы, в которую поступили средства

            if ( $cashier->summ - $process->summ < 0 ) {
                return ['status' => 0, 'errors' => 'Расход превышает остаток в кассе!'];
            }
            if ( $process->status_director == 1 && $process->status == 1 ) {
                return ['status' => 0, 'errors' => 'Расход превышает остаток в кассе!'];
            }

            $cashier->summ -= $process->summ; // расход из кассы ВДС

            $process->status = 1;

            $order->status = Order::STATUS_RETURNED; // меняем статус заказа

            if ( $order->save() && $cashier->save() && $process->save() ) {
                $message = 'Подтвержден вывод средств из кассы ' . $cashier->name . ' на сумму ' . $process->summ;
                Notification::send(Notification::CONFIRM_RETURN, $process->id, Notification::STATUS_PROCESS, $message);
                return ['status' => 1];
            }

            if ( $order->hasErrors() ) $errors[] = $order->getErrors();
            if ( $cashier->hasErrors() ) $errors[] = $cashier->getErrors();
            if ( $process->hasErrors() ) $errors[] = $process->getErrors();

        }

        return ['status' => 0, 'errors' => $errors];

    }

    // подтверждение Директором всех выводов из кассы: расход, перенос, ВДС
    // GET запрос id - process_id
    public function actionConfirmexit($id)
    {
        $user = User::findOne(Yii::$app->user->id);
        if ( !in_array($user->role, ['director', 'accounter', 'admin']) ) { // доверенные для подтверждения вывода средств пользователи
            Yii::$app->response->statusCode = 403;
            return ['errors' => 'Доступ запрещен!'];
        }

        if ( !$id > 0 ) $errors[] = 'Не задан процесс (process_id)!';
        if ( !$process = Process::find()->with(['cashier'])->where(['id' => $id])->one() ) $errors[] = 'Процесс не найден!';

        if ( $process && $process->status_director == 1 ) $errors[] = 'Процесс уже подтвержден!';

        if ( !$errors ) {

            // подтверждение директора
            $process->status_director = 1;

            if ( $process->save() ) {
                // отключаем предыдущее уведомление
                Notification::complete($process->id);

                $processType = Process::processType[$process->process_type];
                $message = 'Подтвержден ' . $processType . ' из кассы ' . $process->cashier->name . ' на сумму ' . $process->summ;
                $user_id = $process->created_by;

                Notification::send(Notification::CONFIRMED, $process->id, Notification::STATUS_PROCESS, $message, $user_id); // отправляем новое уведомление
                return ['message' => $message];
            }

            if ( $process->hasErrors() ) $errors[] = $process->getErrors();

        }

        return ['status' => 0, 'errors' => $errors];

    }


}
