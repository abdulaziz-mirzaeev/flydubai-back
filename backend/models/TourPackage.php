<?php

namespace backend\models;

use app\traits\BaseModelTrait;
use app\validators\PriceValidator;
use Yii;

/**
 * This is the model class for table "tour_package".
 *
 * @property int $id
 * @property int|null $operator_id Тур оператор
 * @property int|null $tour_id Тур
 * @property int|null $tour_partner_id Партнер по турпакетам
 * @property int|null $client_id Клиент
 * @property float|null $cost_price Себестоимость визы
 * @property float|null $sell_price Цена
 * @property string|null $comment Комментарий
 * @property string|null $payment_at Дата оплаты
 * @property string|null $created_at Дата создания
 * @property string|null $modified_at Дата изменения
 * @property int|null $created_by Создал
 * @property int|null $modified_by Изменил
 *
 * @property Client $client
 * @property Tour $tour
 * @property TourOperator $tourOperator
 * @property TourPartner $tourPartner
 */
class TourPackage extends \backend\models\BaseModel
{
    use BaseModelTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tour_package';
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ( $insert ) {
            $order = new Order();
            $order->type = 'tour_package';
            $order->type_id = $this->id;
            $order->status = Order::STATUS_BOOKED;
            $order->save();

            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['operator_id', 'tour_id', 'tour_partner_id', 'client_id', 'created_by', 'modified_by'], 'integer'],
            [['tour_id', 'tour_partner_id', 'cost_price', 'sell_price'], 'required'],
            [['cost_price', 'sell_price'], 'number'],
            [['comment'], 'string'],
            [['departure_date', 'return_date'], 'required'],
            [['created_at', 'modified_at', 'payment_at', 'departure_date', 'return_date'], 'safe'],
            [['client_id'], 'exist', 'targetClass' => Client::className(), 'targetAttribute' => ['client_id' => 'id']],
            [['tour_id'], 'exist', 'targetClass' => Tour::className(), 'targetAttribute' => ['tour_id' => 'id']],
            [['tour_partner_id'], 'exist', 'targetClass' => TourPartner::className(), 'targetAttribute' => ['tour_partner_id' => 'id']],
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
            'operator_id' => 'Оператор',
            'tour_id' => 'Тур',
            'tour_partner_id' => 'Партнер по турпакетам',
            'client_id' => 'Клиент',
            'cost_price' => 'Себестоимость',
            'sell_price' => 'Цена Реализации',
            'comment' => 'Комментарии',
            'payment_at' => 'Дата оплаты',
            'departure_date' => 'Дата выезда',
            'return_date' => 'Дата возвращения',
            'created_at' => 'Дата создания',
            'modified_at' => 'Дата изменения',
            'created_by' => 'Создал',
            'modified_by' => 'Изменил',
        ];
    }

    public function extraFields()
    {
        return ['client', 'order', 'tour', 'tourPartner'];
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
     * Gets query for [[Tour]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTour()
    {
        return $this->hasOne(Tour::className(), ['id' => 'tour_id']);
    }


    /**
     * Gets query for [[TourPartner]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTourPartner()
    {
        return $this->hasOne(TourPartner::className(), ['id' => 'tour_partner_id']);
    }

    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['type_id' => 'id'])->where(['type' => Order::TYPE_TOUR]);
    }


}
