<?php

namespace backend\models;

use app\traits\BaseModelTrait;
use app\validators\PriceValidator;
use Yii;

/**
 * This is the model class for table "visa".
 *
 * @property int $id
 * @property string|null $number Номер
 * @property float|null $cost_price Себестоимость визы
 * @property float|null $sell_price Цена
 * @property int|null $visa_type_id Тип визы
 * @property int|null $visa_partner_id Партнер
 * @property int|null $client_id
 * @property string|null $comment Комментарий
 * @property string|null $flight_date Дата вылета
 * @property string|null $flight_return_date Дата возвращения
 * @property string|null $payment_at Дата оплаты
 * @property string|null $created_at Дата создания
 * @property string|null $modified_at Дата изменения
 * @property int|null $created_by Создал
 * @property int|null $modified_by Изменил
 *
 * @property Client $client
 * @property VisaPartner $visaPartner
 * @property VisaType $visaType
 */
class Visa extends \backend\models\BaseModel
{
    use BaseModelTrait;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'visa';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cost_price', 'sell_price'], 'number'],
            [['visa_type_id', 'visa_partner_id', 'client_id', 'created_by', 'modified_by'], 'integer'],
            [['comment'], 'string'],
            [['flight_date', 'flight_return_date', 'created_at', 'modified_at', 'payment_at'], 'safe'],
            [['number'], 'string', 'max' => 32],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::className(), 'targetAttribute' => ['client_id' => 'id']],
            [['visa_partner_id'], 'exist', 'skipOnError' => true, 'targetClass' => VisaPartner::className(), 'targetAttribute' => ['visa_partner_id' => 'id']],
            [['visa_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => VisaType::className(), 'targetAttribute' => ['visa_type_id' => 'id']],
            [['sell_price'], PriceValidator::class]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'number' => 'Номер',
            'cost_price' => 'Себестоимость визы',
            'sell_price' => 'Цена',
            'visa_type_id' => 'Тип визы',
            'visa_partner_id' => 'Партнер',
            'client_id' => 'Клиент',
            'comment' => 'Комментарий',
            'flight_date' => 'Дата вылета',
            'flight_return_date' => 'Дата возвращения',
            'created_at' => 'Дата создания',
            'modified_at' => 'Дата изменения',
            'created_by' => 'Создал',
            'modified_by' => 'Изменил',
        ];
    }

    public function extraFields()
    {
        return ['visaType', 'client', 'visaPartner', 'order'];
    }

    /**
     * Gets query for [[Client]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }

    /**
     * Gets query for [[VisaPartner]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVisaPartner()
    {
        return $this->hasOne(VisaPartner::className(), ['id' => 'visa_partner_id']);
    }

    /**
     * Gets query for [[VisaType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVisaType()
    {
        return $this->hasOne(VisaType::className(), ['id' => 'visa_type_id']);
    }

    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['type_id' => 'id'])->where(['type' => Order::TYPE_VISA]);
    }
}
