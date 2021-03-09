<?php

namespace backend\models;

use app\traits\BaseModelTrait;
use common\helpers\Curl;
use Yii;

/**
 * This is the model class for table "receipt".
 *
 * @property int $id
 * @property int|null $order_id Заказ
 * @property string|null $data Данные чека
 * @property string|null $uid uid чека
 * @property string|null $status Статус
 * @property string|null $created_at Дата создания
 * @property string|null $modified_at Дата изменения
 * @property int|null $created_by Создал
 * @property int|null $modified_by Изменил
 *
 * @property Order $order
 * @property Terminal $terminal
 */
class Receipt extends \backend\models\BaseModel
{

    use BaseModelTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'receipt';
    }

    /**
     * API:
     * - в Вашей системе формируется чек,
     * - отправляется на кассу
     * - кассир выбирает чек и завершает операцию
     * (кассир выводит список заказов, выбирает нужный заказ и нажимает подтвердить заказ,
     * ККМ-терминал через API распечатывает чек!)
     * - информация об успешной операции отправляется нам
     * - мы сообщаем вам
     * */
    public static function create($post)
    {

        //return ['status'=>0,'erorrs'=>'Действие не требуется! Чек формируется с помощью ККМ!'];

        $errors = [];

        /* if (!Yii::$app->request->isPost) {
            $errors[] = 'Данные должны передаваться методом POST!';
            return ['status' => 0, 'errors' => $errors];
        } */

        /** рабочий пример post данных в json
         * terminal_id - 95148  - id терминала в системе API
         * order_id - 1025 - id заказа
         * status - PAID, RETURNED, DELETED - тип чека
         */

        /** // old - пример
         * {
         * "Receipt[terminalModel]":"A2021",
         * "Receipt[terminalSN]":"AABB1122",
         * "Receipt[totalCost]":"100000",
         * "Receipt[totalNds]":"0",
         * "Receipt[totalPaid]":"100000",
         * "Receipt[count]":"1",
         * "Receipt[amount]":"100000",
         * "Receipt[price]":"90000",
         * "Receipt[productId]":"ticket",
         * "Receipt[productName]":"aaa111",
         * "Receipt[order_id]":"1",
         * "Receipt[terminal_id]":"1",
         * "Receipt[status]":"PAID"
         * }
         */

        //$postData = file_get_contents('php://input');
        //$post = json_decode($postData, true);

        // валидация

        // проверяем переданный терминал
        if ( isset($post['terminal_id']) ) {
            if ( !$terminal = Terminal::find()->where(['id' => (int)$post['terminal_id']])->one() ) {
                $errors[] = 'Задан не верный ID терминала!';
            }
        } else {
            $errors[] = 'Не задан ID терминала!';
        }

        $order = null;

        if ( isset($post['order_id']) ) {

            // поиск заказа
            if ( $order = Order::find()
                ->with(['receipt', 'terminal', 'order'])
                ->where(['id' => (int)$post['order_id']])
                ->one() ) {

                // распечатать чек можно только для неоплаченныхх заказов
                if ( $order->status == Order::STATUS_PAID ) {
                    return ['status' => 0, 'errors' => 'Невозможно создать чек. Заказ уже был оплачен!'];
                }

                /*if (!in_array($order->status, [Order::STATUS_PAID, Order::STATUS_RETURNED, Order::STATUS_DELETED])) {
                    return ['status' => 0, 'errors' => 'Задан неверный тип заказа!'];
                }*/

                if ( isset($post['status']) ) {
                    $status = $post['status'];
                } else {
                    $status = $order->status;
                }

                $total_card = 0;
                $total_cash = 0;

                // проверка переданной суммы с терминала или налички
                if ( in_array($order->payment_type, [Process::PAYMENT_TYPE_TERMINAL, Process::PAYMENT_TYPE_CASH_TERMINAL]) ) {
                    $total_card = $order->summ_terminal;
                }
                if ( in_array($order->payment_type, [Process::PAYMENT_TYPE_CASH, Process::PAYMENT_TYPE_CASH_TERMINAL]) ) {
                    $total_cash = $order->summ - $order->summ_terminal; // если часть оплачена терминалом
                }

            } else {
                $errors[] = 'Заказ не найден!';
            }

        } else {
            $errors[] = 'Не задан ID заказа!';
        }

        // можно задать статус, либо статус берется из заказа order.status
        //if (!isset($post['status'])) $errors[] = 'Не задан статус чека!'; // PAID, RETURNED

        // получаем токен
        if ( !$errors && $order && $token = Uzcassa::getToken() ) {

            // создаем чек

            // d($terminal,1);

            // $data - данные для формирования чека, которые подтверждает кассир
            /** @var Terminal $terminal */
            $data = [
                //"discountPercent" => 0,
                "terminalModel" => $terminal->terminalModel, // "N5",
                "terminalSN" => $terminal->terminalSN, // "N5W00000000000",
                "totalCard" => $total_card, // Оплачено с банковской карты
                "totalCash" => $total_cash, // Оплачено наличными
                "totalCost" => $order->summ, // $order->summ,  // Общая стоимость, с учетом скидки
                //"totalDiscount" => 0,
                //"totalLoyaltyCard" => 0,
                "totalNds" => $order->nds, // ндс
                "totalPaid" => $order->summ, // Сколько заплатил клиент
                "userId" => null, // Yii::$app->user->id,  // id кассира в системе API
                "status" => [
                    "code" => strtoupper($status), // "PAID", "RETURNED"
                ],
                "receiptDetails" => [
                    [
                        "qty" => $order->count, // колво товара
                        "amount" =>  $order->count, // сумма за все  цена * qty
                        // "discount" => 0,
                        // "discountPercent" => 0,
                        // "excise" => 0,
                        // "exciseRate" => 0,
                        //"nds" => $or['nds'],
                        //"ndsPercent" => 0,
                        "price" => $order->summ, //$order->summ, // оплачено
                        "productId" => $order->type_id,
                        "productName" => $order->type,
                        "status" => strtoupper($status), // "PAID", "RETURNED"
                    ]
                ]
            ];


            /**
             *
             * {
             * "discountPercent": 0,
             * "terminalModel": "N5",
             * "terminalSN": "N500W214906",
             * "totalCard": 0,
             * "totalCash": 0,
             * "totalCost": 1000,
             * "totalDiscount": 11,
             * "totalLoyaltyCard": 0,
             * "totalNds": 0,
             * "totalPaid": 1100,
             * "userId": null,
             * "status": {"code": "PAID"},
             * "receiptDetails": [
             * {
             * "amount": 1100,
             * "discount": 0,
             * "discountPercent": 0,
             * "excise": 0,
             * "exciseRate": 0,
             * "nds": 10,
             * "ndsPercent": 10,
             * "price": 1100,
             * "productId": 1,
             * "productName": "string",
             * "qty": 1,
             * "status": "PAID"
             * }
             * ]
             * }
             */

            // создание чека в системе API
            // возвращает uid чека
            if ( $uid = self::createReceipt($token, $data) ) {

                // $uid = '966831608903581554'; // для теста существующий uid в API для распечатанноого чека

                $receipt = new Receipt();
                $receipt->order_id = $post['order_id']; // id заказа
                // $receipt->terminal_id = $terminal->id; //  $post['terminal_id']; // с какаго терминала оплата
                // $receipt->uid = $uid; // id чека
                $receipt->status = strtoupper($post['status']); // PAID, RETURNED

                if ( $data_receipt = self::getReceipt($uid, $token, false) ) { // получение чека из API
                    $receipt->uid = $data_receipt['uid'];
                    $receipt->data = json_encode($data_receipt, JSON_UNESCAPED_UNICODE);
                } else { // чек еще не создан в API системе
                    $receipt->data = null;
                }

                // меняем статус заказа на оплачен - paid
                $order->status = Order::STATUS_PAID;
                $order->save();

                if ( $receipt->save() ) { // сохраняем данные запроса при создании чека
                    return $receipt;
                }

                $errors[] = $receipt->getErrors();


            } else {
                $errors[] = 'Ошибка при создании чека!';
            }

        }
        Yii::$app->response->statusCode = 422;
        return ['status' => 0, 'errors' => $errors];

    }

    private static function createReceipt($token, $data)
    {
        $result = Curl::run('/api/receipt/send', 'post', $token, $data);

        //d($result,1);

        if ( isset($result['statusCode']) && $result['statusCode'] == 200 ) {
            return $result['data']; // uid
        }
        return false;

    }

    private static function getReceipt($condition, $token = null, $asJson = false)
    {

        $result = Curl::run('/api/receipt/list?' . $condition, 'get', $token);

        if ( isset($result['total']) && $result['total'] > 0 ) {
            $result = $result['items'][0];
            if ( $asJson ) $result = json_encode($result, JSON_UNESCAPED_UNICODE);
            return $result;
        }
        return false;

    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'created_by', 'modified_by'], 'integer'],
            [['data'], 'string'],
            [['created_at', 'modified_at'], 'safe'],
            [['status'], 'string', 'max' => 24],
            [['uid'], 'string', 'max' => 64],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::className(), 'targetAttribute' => ['order_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Заказ',
            'data' => 'Данные чека',
            'uid' => 'ID чека',
            'status' => 'Статус',
            'created_at' => 'Дата создания',
            'modified_at' => 'Дата изменения',
            'created_by' => 'Создал',
            'modified_by' => 'Изменил',
        ];
    }

    // получить чек по terminalId

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
