<?php

namespace backend\models;

use app\traits\BaseModelTrait;
use Yii;

/**
 * This is the model class for table "terminal".
 *
 * @property int $id
 * @property int|null $terminalID ID терминала в uzcassa
 * @property string|null $terminalSN Серия
 * @property string|null $terminalModel Модель
 * @property int|null $branch_id Филиал в uzcassa
 * @property int|null $company_id Компания в uzcassa
 * @property string|null $created_at Дата создания
 * @property string|null $modified_at Дата изменения
 * @property int|null $created_by Создал
 * @property int|null $modified_by Изменил
 * @property string $typeName Изменил
 *
 * @property Receipt[] $receipts
 */
class Terminal extends \backend\models\BaseModel
{

    use BaseModelTrait;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'terminal';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['branch_id', 'company_id', 'created_by', 'modified_by'], 'integer'],
            [['created_at', 'modified_at'], 'safe'],
            [['terminalID', 'terminalSN', 'terminalModel'], 'string', 'max' => 24],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'terminalID' => 'ID терминала в uzcassa',
            'terminalSN' => 'Серия',
            'terminalModel' => 'Модель',
            'branch_id' => 'Филиал в uzcassa',
            'company_id' => 'Компания в uzcassa',
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
     * Gets query for [[Receipts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReceipts()
    {
        return $this->hasMany(Receipt::className(), ['terminal_id' => 'id']);
    }

    public function isInternal()
    {
        return $this->terminalID === 0;
    }

    public function getTypeName()
    {
        if ( $this->terminalID == 0 ) {
            return '(Внутренний)';
        }

        return '(UzCassa)';
    }
}
