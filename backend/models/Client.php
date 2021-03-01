<?php

namespace backend\models;

use app\traits\BaseModelTrait;
use common\models\User;
use Yii;

/**
 * This is the model class for table "client".
 *
 * @property int $id
 * @property string|null $first_name Имя
 * @property string|null $last_name Фамилия
 * @property string|null $patronym Отчество
 * @property int|null $client_type_id Тип клиента
 * @property string|null $client_number Номер клиента
 * @property string|null $passport_serial Серия паспорта
 * @property string|null $passport_number Номер паспорта
 * @property string|null $created_at Дата создания
 * @property string|null $modified_at Дата изменения
 * @property int|null $created_by Создал
 * @property int|null $modified_by Изменил
 *
 * @property Call[] $calls
 * @property ClientType $clientType
 * @property User $user
 * @property Order[] $orders
 * @property Ticket[] $tickets
 * @property TourPackage[] $tourPackages
 * @property Visa[] $visas
 * @property string|null $name Полное имя
 */
class Client extends \backend\models\BaseModel
{
    use BaseModelTrait;

    const CLIENT_TYPE_INDIVIDIAL = 1;
    const CLIENT_TYPE_ENTITY = 2;
    public const clientTypes = [
        self::CLIENT_TYPE_INDIVIDIAL => 'Физическое лицо',
        self::CLIENT_TYPE_ENTITY => 'Юридическое лицо'
    ];
    private $_name;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'client';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['client_type_id', 'created_by', 'modified_by', 'passport_number'], 'integer'],
            [['created_at', 'modified_at'], 'safe'],
            [['first_name', 'patronym', 'last_name'], 'string', 'max' => 255],
            [['passport_serial', 'passport_number', 'first_name', 'last_name'], 'required'],
            [['client_number'], 'string', 'max' => 32],
            [['passport_serial'], 'string', 'max' => 16],
            [['email'], 'email'],
            ['phone', 'string', 'max' => 24],
//            [['client_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClientType::className(), 'targetAttribute' => ['client_type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'first_name' => 'Имя',
            'patronym' => 'Отчество',
            'last_name' => 'Фамилия',
            'email' => 'Э-почта',
            'phone' => 'Номер телефона',
            'client_type_id' => 'Тип клиента',
            'client_number' => 'Номер клиента',
            'passport_serial' => 'Серия паспорта',
            'passport_number' => 'Номер паспорта',
            'created_at' => 'Дата создания',
            'modified_at' => 'Дата изменения',
            'created_by' => 'Создал',
            'modified_by' => 'Изменил',
        ];
    }

    public function extraFields()
    {
        return ['clientType'];
    }

    /**
     * Gets query for [[Calls]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCalls()
    {
        return $this->hasMany(Call::className(), ['client_id' => 'id']);
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
     * Gets query for [[Orders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Order::className(), ['client_id' => 'id']);
    }

    /**
     * Gets query for [[Tickets]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTickets()
    {
        return $this->hasMany(Ticket::className(), ['client_id' => 'id']);
    }

    /**
     * Gets query for [[TourPackages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTourPackages()
    {
        return $this->hasMany(TourPackage::className(), ['client_id' => 'id']);
    }

    /**
     * Gets query for [[Visas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVisas()
    {
        return $this->hasMany(Visa::className(), ['client_id' => 'id']);
    }

    public function getName()
    {
        if (empty($this->first_name) || empty($this->last_name)) {
            return null;
        }

        if ($this->_name === null) {
            $this->setName($this->first_name, $this->last_name);
        }

        return $this->_name;
    }

    public function setName($fname, $lname)
    {
        $this->_name = (string)$fname . ' ' . $lname;
    }
}
