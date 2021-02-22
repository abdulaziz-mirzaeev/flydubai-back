<?php

namespace backend\models;

use app\traits\BaseModelTrait;
use app\validators\PriceValidator;
use Yii;

/**
 * This is the model class for table "tour_package".
 *
 * @property int $id
 * @property int|null $tour_operator_id Тур оператор
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

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tour_operator_id', 'tour_id', 'tour_partner_id', 'client_id', 'created_by', 'modified_by'], 'integer'],
            [['cost_price', 'sell_price'], 'number'],
            [['comment'], 'string'],
            [['created_at', 'modified_at', 'payment_at'], 'safe'],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::className(), 'targetAttribute' => ['client_id' => 'id']],
            [['tour_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tour::className(), 'targetAttribute' => ['tour_id' => 'id']],
            [['tour_operator_id'], 'exist', 'skipOnError' => true, 'targetClass' => TourOperator::className(), 'targetAttribute' => ['tour_operator_id' => 'id']],
            [['tour_partner_id'], 'exist', 'skipOnError' => true, 'targetClass' => TourPartner::className(), 'targetAttribute' => ['tour_partner_id' => 'id']],
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
            'tour_operator_id' => 'Тур оператор',
            'tour_id' => 'Тур',
            'tour_partner_id' => 'Партнер по турпакетам',
            'client_id' => 'Клиент',
            'cost_price' => 'Себестоимость',
            'sell_price' => 'Цена Реализации',
            'comment' => 'Комментарии',
            'payment_at' => 'Дата оплаты',
            'created_at' => 'Дата создания',
            'modified_at' => 'Дата изменения',
            'created_by' => 'Создал',
            'modified_by' => 'Изменил',
        ];
    }

    public function extraFields()
    {
        return ['client', 'order', 'tour', 'tourPartner', 'tourOperator'];
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
     * Gets query for [[TourOperator]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTourOperator()
    {
        return $this->hasOne(TourOperator::className(), ['id' => 'tour_operator_id']);
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
