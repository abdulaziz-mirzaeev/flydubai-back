<?php

namespace backend\models;

use app\traits\BaseModelTrait;
use app\validators\PriceValidator;
use Yii;

/**
 * This is the model class for table "cargo".
 *
 * @property int $id
 * @property string|null $name Название
 * @property int|null $air_waybill Авианакладная
 * @property int|null $client_type_id Тип клиента
 * @property int|null $company_id Компания
 * @property int|null $client_id Клиент
 * @property int|null $package_amount Количество
 * @property int|null $package_weight Вес
 * @property int|null $package_type_id Тип
 * @property float|null $cost_price Себестоимость
 * @property float|null $sell_price Цена продажи
 * @property string|null $comment Комментарий
 * @property string|null $created_at Дата создания
 * @property string|null payment_at Дата оплаты
 * @property string|null $modified_at Дата изменения
 * @property int|null $created_by Создал
 * @property int|null $modified_by Изменил
 *
 * @property ClientType $clientType
 * @property Company $company
 * @property PackageType $packageType
 */
class Cargo extends \backend\models\BaseModel
{
    use BaseModelTrait;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cargo';
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ( $insert ) {
            $order = new Order();
            $order->type = 'cargo';
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
            [[ 'client_type_id', 'company_id','client_id', 'package_amount', 'package_weight', 'package_type_id', 'created_by', 'modified_by'], 'integer'],
            [['cost_price', 'sell_price'], 'number'],
            [['comment','air_waybill'], 'string'],
            [['created_at', 'modified_at','payment_at'], 'safe'],
            [['client_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClientType::className(), 'targetAttribute' => ['client_type_id' => 'id']],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'id']],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::className(), 'targetAttribute' => ['client_id' => 'id']],
            [['package_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => PackageType::className(), 'targetAttribute' => ['package_type_id' => 'id']],
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
            'air_waybill' => 'Авианакладная',
            'client_type_id' => 'Тип клиента',
            'company_id' => 'Компания',
            'client_id' => 'Клиент',
            'package_amount' => 'Количество',
            'package_weight' => 'Вес',
            'package_type_id' => 'Тип',
            'cost_price' => 'Себестоимость',
            'sell_price' => 'Цена продажи',
            'comment' => 'Комментарий',
            'created_at' => 'Дата создания',
            'modified_at' => 'Дата изменения',
            'created_by' => 'Создал',
            'modified_by' => 'Изменил',
        ];
    }

    public function extraFields()
    {
        return ['client','company', 'packageType', 'clientType'];
    }

    /**
     * Gets query for [[ClientType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClientType()
    {
        return $this->hasOne(ClientType::className(), ['id' => 'client_type_id']);
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
     * Gets query for [[Company]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['id' => 'company_id']);
    }

    /**
     * Gets query for [[PackageType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPackageType()
    {
        return $this->hasOne(PackageType::className(), ['id' => 'package_type_id']);
    }

    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['type_id' => 'id'])->where(['type' => Order::TYPE_CARGO]);
    }
}
