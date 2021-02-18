<?php

namespace backend\models;

use app\traits\BaseModelTrait;
use Yii;

/**
 * This is the model class for table "receipt".
 *
 * @property int $id
 * @property int|null $order_id Заказ
 * @property string|null $data Данные чека
 * @property string|null $uid uid чека
 * @property string|null $status Статус
 * @property string|null $created_at Дата создания
 * @property string|null $modified_at Дата изменения
 * @property int|null $created_by Создал
 * @property int|null $modified_by Изменил
 *
 * @property Order $order
 * @property Terminal $terminal
 */
class Receipt extends \backend\models\BaseModel
{

    use BaseModelTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'receipt';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'created_by', 'modified_by'], 'integer'],
            [['data'], 'string'],
            [['created_at', 'modified_at'], 'safe'],
            [['status'], 'string', 'max' => 24],
            [['uid'], 'string', 'max' => 64],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::className(), 'targetAttribute' => ['order_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Заказ',
            'data' => 'Данные чека',
            'uid' => 'ID чека',
            'status' => 'Статус',
            'created_at' => 'Дата создания',
            'modified_at' => 'Дата изменения',
            'created_by' => 'Создал',
            'modified_by' => 'Изменил',
        ];
    }

    /**
     * Gets query for [[Order]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }

}
