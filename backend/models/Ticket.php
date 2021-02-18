<?php

namespace backend\models;

use app\traits\BaseModelTrait;
use app\validators\PriceValidator;
use Yii;

/**
 * This is the model class for table "ticket".
 *
 * @property int $id
 * @property int $parent_id
 * @property string|null $flight_number Номер рейса
 * @property string|null $flight_route Направление рейса
 * @property float|null $cost_price Себестоимость визы
 * @property float|null $sell_price Цена
 * @property int|null $tariff_id Тариф
 * @property int|null $tariff_type Тип тарифа
 * @property string|null $pnr ID (prn) билета
 * @property int|null $client_id Клиент
 * @property int|null $passenger_count Пассажиров
 * @property string|null $comment Комментарий
 * @property string|null $flight_date Дата вылета
 * @property string|null $payment_at Дата оплаты
 * @property string|null $created_at Дата создания
 * @property string|null $modified_at Дата изменения
 * @property int|null $created_by Создал
 * @property int|null $modified_by Изменил
 *
 * @property Client $client
 * @property Tariff $tariff
 * @property TariffType $tariffType
 * @property Order order
 * @property Ticket parent
 * @property Ticket child
 */
class Ticket extends \backend\models\BaseModel
{
    use BaseModelTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ticket';
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert) {
            $order = new Order();
            $order->type = 'ticket';
            $order->type_id = $this->id;
            $order->status = Order::STATUS_BOOKED;
            $order->save();

            return true;
        }

        return false;
    }

    public function beforeDelete()
    {
        $order = $this->order;
        $order->delete();

        return parent::beforeDelete();
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['flight_number', 'flight_route', 'cost_price', 'sell_price', 'tariff_id', 'pnr', 'passenger_count', 'flight_date'], 'required'],
            [['cost_price', 'sell_price'], 'number'],
            [['tariff_id', 'parent_id', 'client_id', 'passenger_count', 'created_by', 'modified_by'], 'integer'],
            [['comment'], 'string'],
            [['flight_date', 'payment_at', 'created_at', 'modified_at'], 'safe'],
            [['flight_number'], 'string', 'max' => 32],
            [['flight_route', 'tariff_type'], 'string', 'max' => 255],
            [['pnr'], 'string', 'max' => 24],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::className(), 'targetAttribute' => ['client_id' => 'id']],
            [['tariff_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tariff::className(), 'targetAttribute' => ['tariff_id' => 'id']],
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
            'flight_number' => 'Номер рейса',
            'parent_id' => 'Билет туда/обратно',
            'flight_route' => 'Направление рейса',
            'cost_price' => 'Себестоимость визы',
            'sell_price' => 'Цена ',
            'tariff_id' => 'Тариф',
            'tariff_type_id' => 'Тип тарифа',
            'pnr' => 'ID (prn) билета',
            'client_id' => 'Клиент',
            'passenger_count' => 'Пассажиров',
            'comment' => 'Комментарий',
            'flight_date' => 'Дата вылета',
            'payment_at' => 'Дата оплаты',
            'created_at' => 'Дата создания',
            'modified_at' => 'Дата изменения',
            'created_by' => 'Создал',
            'modified_by' => 'Изменил',
        ];
    }

    public function extraFields()
    {
        return ['client', 'order', 'child', 'parent'];
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
     * Gets query for [[Tariff]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTariff()
    {
        return $this->hasOne(Tariff::className(), ['id' => 'tariff_id']);
    }

    /**
     * Gets query for [[TariffType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTariffType()
    {
        return $this->hasOne(TariffType::className(), ['id' => 'tariff_type_id']);
    }

    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['type_id' => 'id'])->where(['type' => Order::TYPE_TICKET]);
    }

    public function isChild()
    {
        return !empty($this->parent_id);
    }

    public function getParent()
    {
        return $this->hasOne(Ticket::class, ['id' => 'parent_id']);
    }

    public function getChild()
    {
        return $this->hasOne(Ticket::class, ['parent_id' => 'id']);
    }
}
