<?php

namespace backend\models;

use app\traits\BaseModelTrait;
use common\models\User;
use Yii;

/**
 * This is the model class for table "notification".
 *
 * @property int $id
 * @property int|null $user_id Кому
 * @property int|null $process_id
 * @property string|null $message Сообщение
 * @property int|null $type Тип сообщения
 * @property int|null $status Статус
 * @property string|null $created_at Дата создания
 * @property string|null $modified_at Дата изменения
 * @property int|null $created_by Создал
 * @property int|null $modified_by Изменил
 *
 * @property User $user
 */
class Notification extends \backend\models\BaseModel
{

    use BaseModelTrait;

    const STATUS_PROCESS = 0; // в процессе
    const STATUS_COMPLETE = 1; // завершен

    const NEW_MESSAGE = 0;      // новое сообщение
    const CONFIRMED = 1;      // подтвержден вывод д/с
    const CONFIRM_EXIT = 2;      // подтверждение вывода д/с
    const CONFIRM_RETURN = 3;    // подтверждение возврата д/с
    const CONFIRM_TRANSFER = 4;    // подтверждение перемещения д/с
    const EXIT_SUCCESS = 5;    // подтверждение перемещения д/с
    const ENTER = 6;    // приход д/с
    const TRANSFERED = 7;    // перенос д/с

    public static $notifications = [
        0 => 'Поступило новое сообщение',
        1 => 'Подтверждено',
        2 => 'Необходимо подтвердить расход д/с',
        3 => 'Необходимо подтвердить возврат д/с',
        4 => 'Необходимо подтвердить перемещения д/с',
        5 => 'Произведен вывод д/с',
        6 => 'В кассу поступили д/с',
        7 => 'Произведен перенос д/с',

    ];

    // отправка уведомлений
    public static function send($type=Notification::NEW_MESSAGE, $process_id, $status=Notification::STATUS_PROCESS, $message=null, $user_id=null){

        $notification = new Notification();
        $notification->process_id = $process_id;
        $notification->type = $type;
        $notification->message = $message;
        $notification->user_id = $user_id;
        $notification->status = $status ; // обработан
        if($notification->save()){
            return true;
        }
        //d($notification->getErrors(),1);
        return false;

    }

    // прочитать сообщение
    public static function read($id){

        if($notification = Notification::findOne($id)){
            $notification->status = Notification::STATUS_COMPLETE;
            if($notification->save()) return true;
        }

        return false;

    }

    // отключаем уведомление
    public static  function complete($process_id){

        if( $notification = Notification::find()->where(['process_id'=>$process_id,'status'=>Notification::STATUS_PROCESS])->one() ){
            $notification->status = Notification::STATUS_COMPLETE;
            if( $notification->save() ) return true;
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notification';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'type', 'status', 'created_by', 'modified_by','process_id'], 'integer'],
            [['created_at', 'modified_at'], 'safe'],
            [['message'], 'string', 'max' => 512],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['process_id'], 'exist', 'skipOnError' => true, 'targetClass' => Process::className(), 'targetAttribute' => ['process_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Кому',
            'process_id' => 'Процесс',
            'message' => 'Сообщение',
            'type' => 'Тип сообщения',
            'status' => 'Статус',
            'created_at' => 'Дата создания',
            'modified_at' => 'Дата изменения',
            'created_by' => 'Создал',
            'modified_by' => 'Изменил',
        ];
    }

    public function extraFields()
    {
        return ['user', 'process'];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Gets query for [[Process]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProcess()
    {
        return $this->hasOne(Process::className(), ['id' => 'process_id']);
    }


}
