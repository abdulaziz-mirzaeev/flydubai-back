<?php

namespace backend\controllers;

use backend\models\Notification;
use backend\models\Process;
use Yii;

/**
 * билеты
 */
class NotificationController extends BaseController
{
    public $modelClass = Notification::class;

    // исправление для CamelCase методов
    public function createAction($id)
    {
        if ($id === '') {
            $id = $this->defaultAction;
        }
        // d($id,1);

        $actionMap = $this->actions();
        if (isset($actionMap[$id])) {
            return Yii::createObject($actionMap[$id], [$id, $this]);
        } elseif (preg_match('/^[a-zA-Z0-9\\-_]+$/', $id) && strpos($id, '--') === false && trim($id, '-') === $id) {
            $methodName = 'action' . str_replace(' ', '', ucwords(implode(' ', explode('-', $id))));
            if (method_exists($this, $methodName)) {
                $method = new \ReflectionMethod($this, $methodName);
                if ($method->isPublic() && $method->getName() === $methodName) {
                    return new \yii\base\InlineAction($id, $this, $methodName);
                }
            }
        }
        return null;
    }

    public function actionInProcess(){
        if($notifications = Notification::find()->where(['status'=>Notification::STATUS_PROCESS])->all()){
            return $notifications;
        }
        return ['status'=>0,'errors'=>'Уведомления не найдены!'];
    }

    // подтверждение денежных средств
    public function actionAdd($id){
        if($process = Process::find()->with(['cashier'])->where(['id'=>$id])->one() ){
            $message = 'Произведен вывод средст из кассы ' . $process->cashier->name . ' на сумму ' . $process->summ;
            Notification::send(Notification::EXIT_SUCCESS,$id,Notification::STATUS_PROCESS,$message);
            return ['status'=>1];
        }

        return ['status'=>0,'errors'=>'Процесс не найден!'];
    }

    public function actionRead($id){
         Notification::read($id);
         return ['status'=>1];

    }

}
