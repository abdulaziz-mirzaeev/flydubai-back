<?php

namespace backend\models;

use app\traits\BaseModelTrait;
use Yii;

/**
 * This is the model class for table "cashier".
 *
 * @property int $id
 * @property string $name Название
 * @property float $summ Сумма
 * @property float $summ_terminal Сумма
 * @property int $type
 * @property string|null $created_at Дата создания
 * @property string|null $modified_at Дата изменения
 * @property int|null $created_by Создал
 * @property int|null $modified_by Изменил
 *
 * @property Order[] $orders
 * @property Process[] $processes
 * @property string $typeName
 */
class Cashier extends \backend\models\BaseModel
{
    use BaseModelTrait;


    const CONFIRM_EXIT = 'confirm_exit';

    const CASHIER_TYPE_WHITE = 0;
    const CASHIER_TYPE_BLACK = 1;

    public const cashier_types = [
        self::CASHIER_TYPE_WHITE => 'Белая',
        self::CASHIER_TYPE_BLACK => 'Чёрная'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cashier';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'type'], 'required'],
            [['summ', 'summ_terminal'], 'number'],
            [['created_at', 'modified_at'], 'safe'],
            [['created_by', 'modified_by', 'type'], 'integer'],
            [['name'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'summ' => 'Сумма',
            'summ_terminal' => 'Сумма терминал',
            'type' => 'Тип кассы',
            'created_at' => 'Дата создания',
            'modified_at' => 'Дата изменения',
            'created_by' => 'Создал',
            'modified_by' => 'Изменил',
        ];
    }

    public function extraFields()
    {
        return ['typeName'];
    }

    /**
     * Gets query for [[Orders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Order::className(), ['cashier_id' => 'id']);
    }

    /**
     * Gets query for [[Processes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProcesses()
    {
        return $this->hasMany(Process::className(), ['cashier_id' => 'id']);
    }

    public function isBlack()
    {
        return $this->type == 1;
    }

    public function getTypeName()
    {
        return self::cashier_types[$this->type];
    }

}
