<?php

namespace backend\models;

use app\traits\BaseModelTrait;
use Yii;

/**
 * This is the model class for table "balance".
 *
 * @property int $id
 * @property float|null $summ Сумма баланса из 1С
 * @property string|null $invoice
 * @property string|null $date Дата баланса
 * @property string|null $created_at
 * @property string|null $modified_at
 * @property int|null $created_by
 * @property int|null $modified_by
 */
class Balance extends \backend\models\BaseModel
{
    use BaseModelTrait;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'balance';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'created_by', 'modified_by'], 'integer'],
            [['summ'], 'number'],
            [['date', 'created_at', 'modified_at'], 'safe'],
            [['invoice'], 'string', 'max' => 64],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'summ' => 'Сумма баланса из 1С',
            'invoice' => 'Invoice',
            'date' => 'Дата баланса',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
            'created_by' => 'Created By',
            'modified_by' => 'Modified By',
        ];
    }
}
