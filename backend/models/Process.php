<?php

namespace backend\models;

use app\traits\BaseModelTrait;
use Yii;

/**
 * This is the model class for table "process".
 *
 * @property int $id
 * @property float $summ Сумма
 * @property float $summ_terminal Сумма с терминала
 * @property float $nds НДС
 * @property string $cheque_number Номер чека
 * @property int|null $cashier_from Касса откуда
 * @property int $cashier_id Касса куда
 * @property int|null $order_id Заказ
 * @property int $process_type Тип процесса
 * @property int $payment_type Тип процесса
 * @property int $terminal_id Терминал
 * @property int|null $status_director Статус директора
 * @property int|null $status Статус вывода
 * @property string|null $comment Комментарий
 * @property string|null $created_at Дата создания
 * @property int|null $created_by Создал
 * @property string|null $modified_at Дата создания
 * @property int|null $modified_by Создал
 *
 * @property Cashier $cashier
 * @property Order $order
 */
class Process extends \backend\models\BaseModel
{
    use BaseModelTrait;

    const TYPE_ENTER = 'ENTER'; //1; // ПРИХОД
    const TYPE_EXIT = 'EXIT'; //2; // РАСХОД
    const TYPE_TRANSFER = 'TRANSFER'; //3; // ПЕРЕМЕЩЕНИЕ ИЗ КАССЫ В КАССУ
    const TYPE_RETURNED = 'RETURNED'; //4; // возврат денежных средств  ВДС

    // тип оплаты
    const PAYMENT_TYPE_CASH = 'CASH';  // НАЛИЧНЫЕ
    const PAYMENT_TYPE_TERMINAL = 'TERMINAL'; // ТЕРМИНАЛ (БЕЗНАЛИЧНЫЙ)
    const PAYMENT_TYPE_VALUTE = 'VALUTE'; // ВАЛЮТА
    const PAYMENT_TYPE_TRANSFER = 'TRANSFER'; // ПЕРЕЧИСЛЕНИЕ (БЕЗНАЛИЧНЫЙ)
    const PAYMENT_TYPE_CASH_TERMINAL = 'CASH_TERMINAL'; // смешанный, наличные и терминал

    const DIRECTOR_CONFIRM_TRUE = 1;
    const DIRECTOR_CONFIRM_FALSE = 0;
    public const paymentType = [
        self::PAYMENT_TYPE_CASH => 'НАЛИЧНЫЕ',
        self::PAYMENT_TYPE_TERMINAL => 'ТЕРМИНАЛ',
        self::PAYMENT_TYPE_VALUTE => 'ВАЛЮТА',
        self::PAYMENT_TYPE_TRANSFER => 'ПЕРЕЧИСЛЕНИЕ',
        self::PAYMENT_TYPE_CASH_TERMINAL => 'СМЕШАННЫЙ (НАЛИЧНЫЕ И ТЕРМИНАЛ)'
    ];
    public const processType = [
        self::TYPE_ENTER => 'ПРИХОД',
        self::TYPE_EXIT => 'РАСХОД',
        self::TYPE_TRANSFER => 'ПЕРЕМЕЩЕНИЕ',
        self::TYPE_RETURNED => 'ВДС',
    ];
    public static $payment_types = [
        'CASH', // => 'НАЛИЧНЫЕ',
        'TERMINAL', // => 'ТЕРМИНАЛ',
        'TRANSFER', // => 'ВАЛЮТА',
        'VALUTE', // => 'ПЕРЕЧИСЛЕНИЕ',
        'CASH_TERMINAL' // => 'СМЕШАННЫЙ'
    ];
    public static $types = [
        'TICKET',
        'VISA',
        'CARGO',
        'TOUR_PACKAGE',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'process';
    }

    // связанные данные  ?expand=order,operator
    public function extraFields()
    {
        return ['order', 'cashier', 'cashierFrom', 'cashier'];
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cashier_id', 'process_type'], 'required'],
            [['summ'], 'number'],
            [['cashier_from', 'terminal_id', 'cashier_id', 'order_id', 'status_director', 'status', 'created_by', 'modified_by'], 'integer'],
            [['comment', 'process_type', 'payment_type'], 'string'],
            [['created_at', 'modified_at'], 'safe'],
            [['cashier_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cashier::className(), 'targetAttribute' => ['cashier_id' => 'id']],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::className(), 'targetAttribute' => ['order_id' => 'id']],
            [['terminal_id'], 'exist', 'skipOnError' => true, 'targetClass' => Terminal::class, 'targetAttribute' => ['terminal_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'summ' => 'Сумма',
            'summ_terminal' => 'Сумма терминала',
            'cheque_number' => 'Номер чека',
            'cashier_from' => 'Касса откуда',
            'cashier_id' => 'Касса куда',
            'order_id' => 'Заказ',
            'nds' => 'НДС',
            'process_type' => 'Тип процесса',
            'payment_type' => 'Тип оплаты',
            'terminal_id' => 'Терминал',
            'status_director' => 'Подтверждение директора',
            'status' => 'Статус вывода',
            'comment' => 'Комментарий',
            'created_at' => 'Дата создания',
            'created_by' => 'Создал',
        ];
    }

    /**
     * Gets query for [[Cashier]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCashier()
    {
        return $this->hasOne(Cashier::className(), ['id' => 'cashier_id']);
    }

    /**
     * Gets query for [[Cashier]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCashierFrom()
    {
        return $this->hasOne(Cashier::className(), ['id' => 'cashier_from']);
    }

    /**
     * Gets query for [[Order]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }
}
