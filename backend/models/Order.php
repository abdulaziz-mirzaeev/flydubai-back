<?php

namespace backend\models;

use app\traits\BaseModelTrait;
use Yii;

/**
 * This is the model class for table "order".
 *
 * @property int $id
 * @property string|null $number Номер счета
 * @property float|null $summ Сумма
 * @property float|null $summ_terminal Сумма доп способом
 * @property float|null $nds НДС
 * @property int|null $type Тип
 * @property int|null $type_id id билет, виза,
 * @property int|null $operator_id оператор
 * @property int|null $terminal_id Терминал
 * @property int|null $cashier_id Касса
 * @property int|null $currency_id Тип валюты
 * @property float|null $currency Курс
 * @property float|null $cheque_number Номер чека
 * @property float|null $converted Сумма по курсу
 * @property int|null $payment_type Тип оплаты
 * @property int|null $payment_type_add Дополнительный тип оплаты
 * @property int|null $status Статус
 * @property string|null $created_at Дата создания
 * @property string|null $payment_at Дата создания
 * @property string|null $modified_at Дата изменения
 * @property int|null $created_by Создал
 * @property int|null $modified_by Изменил
 * @property int|null $count Количество
 *
 * @property string $receiptdata Количество
 * @property bool $hasReturn ВДС?
 *
 * @property Cashier $cashier
 * @property Client $client
 * @property Receipt $receipt
 * @property Terminal $terminal
 * @property Currency $currency0
 * @property Process[] $processes
 * @property Ticket|Visa|TourPackage|Cargo $order
 * @property int | string $profit
 * @property int | string $cash
 */
class Order extends \backend\models\BaseModel
{

    use BaseModelTrait;


    // статусы заявки - заказа
    /* const status_process = [
         'new',
         'process',
         'booking',
         'paid',
         'cancel',
         'return'
     ];

     // тип
     const type = [
         'ticket',
         'visa',
         'tour',
         'cargo'
     ];

     // тип оплаты
     const payment_type = [
         'cash',
         'terminal',
         'valute',
         'transfer',
         'cash_terminal'
     ];*/

    //public $consts = ['asd','asdd'];

    const STATUS_NEW = 'NEW';
    const STATUS_PROCESS = 'PROCESS';      // новая
    const STATUS_BOOKED = 'BOOKED';  // в обработке
    const STATUS_PAID = 'PAID';  // бронирован
    const STATUS_CANCEL = 'CANCEL';     // оплачено
    const STATUS_RETURNED = 'RETURNED';   // отменено
    const STATUS_DELETED = 'DELETED';   // возврат ВДС
    const TYPE_TICKET = 'ticket';   // удален

    // тип
    const TYPE_VISA = 'visa';  // билет
    const TYPE_TOUR = 'tour_package'; // виза
    const TYPE_CARGO = 'cargo'; // турпакет
    public const order_statuses = [
        self::STATUS_NEW => 'НОВАЯ',
        self::STATUS_PROCESS => 'В ОБРАБОТКЕ',
        self::STATUS_BOOKED => 'БРОНИРОВАН',
        self::STATUS_PAID => 'ОПЛАЧЕНО',
        self::STATUS_CANCEL => 'ОТМЕНЕНО',
        self::STATUS_RETURNED => 'ВДС',
        self::STATUS_DELETED => 'УДАЛЕН',
    ]; // Cargo - грузы
    public const order_types = [
        self::TYPE_TICKET => 'БИЛЕТ',
        self::TYPE_VISA => 'ВИЗА',
        self::TYPE_TOUR => 'ТУРПАКЕТ',
        self::TYPE_CARGO => 'КАРГО',
    ];
    public $modelNames = [
        'ticket' => 'Ticket',
        'tour_package' => 'TourPackage',
        'visa' => 'Visa',
        'cargo' => 'Cargo'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order';
    }

    public function beforeSave($insert)
    {
        if ( parent::beforeSave($insert) ) {
            /**
             * This is new for March 11, 2021
             * modified by Abdulaziz Mirzaev
             * created at 11 March 2021
             */

            if ( $this->isNewRecord ) {
                $operator = Operator::findOne(['user_id' => Yii::$app->user->identity->id]);
                if ( $operator ) {
                    $this->operator_id = $operator->id;
                }
            }

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
            [['type_id', 'created_by', 'modified_by', 'operator_id', 'count'], 'integer'],
            [['created_at', 'modified_at', 'operator_id'], 'safe'],
            [['number', 'type', 'status', 'payment_type'], 'string', 'max' => 32],
            [['summ', 'summ_terminal', 'nds'], 'number'],
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
            'number' => 'Номер счета',
            'type' => 'Тип заявки',
            'type_id' => 'Номер заявки',
            'operator_id' => 'Оператор',
            'terminal_id' => 'Терминал',
            'count' => 'Количество',
            'summ' => 'Сумма',
            'summ_terminal' => 'Сумма с карты',
            'nds' => 'НДС',
            'status' => 'Статус',
            'hasReturn' => 'Обработан?',
            'cheque_number' => 'Номер чека',
            'payment_type' => 'Способ оплаты',
            'created_at' => 'Дата создания',
            'payment_at' => 'Время Оплаты',
            'modified_at' => 'Дата изменения',
            'created_by' => 'Создал',
            'modified_by' => 'Изменил',
        ];
    }

    // связанные данные  ?expand=order,operator
    public function extraFields()
    {
        return ['order', 'operator', 'processes', 'receipt', 'terminal', 'service', 'hasReturn', 'profit'];
    }

    /*public function beforeDelete()
    {
        $order = $this->order;
        $order->delete();

        return parent::beforeDelete();
    }*/

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
     * Gets query for [[Currency0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCurrency0()
    {
        return $this->hasOne(Currency::className(), ['id' => 'currency_id']);
    }

    /**
     * Gets query for [[Processes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProcesses()
    {
        return $this->hasMany(Process::className(), ['order_id' => 'id']);
    }

    /**
     * Gets query for [[Receipt]]. чек
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReceipt()
    {
        return $this->hasOne(Receipt::className(), ['order_id' => 'id']);
    }

    /**
     * Gets query for [[Terminal]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTerminal()
    {
        return $this->hasOne(Terminal::className(), ['id' => 'terminal_id']);
    }

    /**
     * Gets query for [[Service]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getService()
    {
        return $this->hasMany(Service::className(), ['order_id' => 'id']);
    }

    // связь к нужному типу - ticket, visa, cargo, tour_package
    public function getOrder()
    {
        $model = 'backend\models\\' . $this->modelNames[$this->type];
        return $this->hasOne($model, ['id' => 'type_id']);
    }


    // оператор
    public function getOperator()
    {
        return $this->hasOne(Operator::className(), ['id' => 'operator_id']);
    }


    public function getProfit()
    {
        $costPrice = $this->order->cost_price;
        $sellPrice = $this->order->sell_price;

        if ( $costPrice && $sellPrice ) {
            return $sellPrice - $costPrice;
        }

        return 'N/A';
    }


    /**
     * {@inheritdoc}
     * @return OrderQuery the active query used by this AR class.
     */
    /* public static function find()
     {
         return new OrderQuery(get_called_class());
     } */


    /**
     * Identifies whether the Returned order was processed by cashier
     * @return bool hasReturn
     */
    public function getHasReturn(): bool
    {
        return Process::find()
            ->where([
                'order_id' => $this->id,
                'process_type' => Process::TYPE_RETURNED
            ])
            ->exists();
    }

    public function getCash()
    {
        return $this->summ - $this->summ_terminal;
    }

}
