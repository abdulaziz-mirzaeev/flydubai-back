<?php

namespace backend\models;

use app\traits\BaseModelTrait;
use common\models\User;
use Yii;

/**
 * This is the model class for table "operator".
 *
 * @property int $id
 * @property string $number
 * @property int|null $user_id
 * @property string|null $created_at
 * @property int|null $created_by
 *
 * @property User $user
 * @property Order[] $orders
 */
class Operator extends \backend\models\BaseModel
{
    use BaseModelTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'operator';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'created_by'], 'integer'],
            [['created_at'], 'safe'],
            [['number'], 'string', 'max' => 24],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'number' => 'Number',
            'user_id' => 'User ID',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }


    // связанные данные  ?expand=order,operator
    public function extraFields()
    {
        return ['user','orders'];
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
     * Gets query for [[Orders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Order::className(), ['operator_id' => 'id']);
    }
}
