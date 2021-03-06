<?php

namespace backend\controllers;

use backend\models\Uzcassa;
use common\helpers\Curl;
use Yii;

/**
 * билеты
 */
class UzcassaController extends BaseController
{
    public $modelClass = Uzcassa::class;

    /*protected function verbs()
    {
        $verbs = parent::verbs();

        $verbs['terminal'] = ['GET'];
        $verbs['company'] = ['GET'];
        $verbs['branch'] = ['GET'];
        $verbs['cashier'] = ['GET'];
        $verbs['getreceipt'] = ['GET'];

        $verbs['receipt'] = ['POST'];

        return $verbs;
    } */

    // проверка данных
    /*private function validate(&$post, $type = null)
    {

        $errors = [];

        if (!isset($post['Receipt'])) {
            $errors[] = 'Данные не найдены (Receipt[])!';
            return $errors; // сразу выход, т.к. ключ Receipt не найден!
        }

        if (!isset($post['Receipt']['order_id'])) $errors[] = 'Не задан ID заказа!';
        if (!isset($post['Receipt']['summ'])) $errors[] = 'Не задана сумма для расхода (summ)!';
        if (!isset($post['Receipt']['cashier_id'])) $errors[] = 'Не задана касса (cashier_id)!';
        if (isset($post['Receipt']['cashier_id']) && $post['Process']['cashier_id'] == 0) $errors[] = 'Не задана касса (cashier_id)!';
        if (!isset($post['Receipt']['terminalModel'])) $errors[] = 'Не задана модель терминала!';
        if (!isset($post['Receipt']['terminalSN'])) $errors[] = 'Не задан серийный номер терминала!'; //$terminal_serial, // "N5W00000000000",
        //if(!isset($post['Receipt']['totalCard'])) $errors[] = ; //$total_card, // Оплачено с банковской карты
        //if(!isset($post['Receipt']['totalCash'])) $errors[] = ''; //$total_cash, // Оплачено наличными
        if (!isset($post['Receipt']['totalCost'])) $errors[] = 'Не задана общая стоимость, с учетом скидки!';
        if (!isset($post['Receipt']['totalNds'])) $errors[] = 'Не задана НДС!';
        if (!isset($post['Receipt']['totalPaid'])) $errors[] = 'Не задана сумма оплаты клиента!';
        //if(!isset($post['Receipt']['userId'])) $errors[] = 'Не задан user_id ';
        if (!isset($post['Receipt']['productId'])) $errors[] = 'Не задан id товара!';
        if (!isset($post['Receipt']['productName'])) $errors[] = 'Не указан тип (ticket, visa, cargo, tour_package)! ';

        return $errors;

    }*/

    public function init()
    {
        //parent::init(); // TODO: Change the autogenerated stub

        echo json_encode(['status'=>0,'errors'=>'Функции API не доступны!']);
        exit;
    }


    // +информация о компании
    // branchId - id филиала для выбора терминалов
    // id=companyId - id компании
    public function actionCompany()
    {

        // получаем токен
        if ($token = Uzcassa::getToken()) {

            /**
             * результат запроса: api/company/current
             *
             * {
             * "id": 95148,
             * "createdBy": "anonymousUser",
             * "createdDate": "2020-02-11T13:53:41.979702",
             * "lastModifiedBy": "998946968835",
             * "lastModifiedDate": "2020-09-25T12:54:31.301",
             * "contactFullName": {
             * "firstName": "Ниц 5",
             * "lastName": "Ниц 2",
             * "patronymic": "Ниц 1",
             * "phone": null,
             * "name": "Ниц 2 Ниц 5"
             * },
             * "email": null,
             * "phone": "998971490083",
             * "userId": null,
             * "inn": "201589463",
             * "name": "Test",
             * "businessType": {
             * "code": "ZAO",
             * "nameRu": "ЗАО",
             * "nameUzCyrillic": "ЗАО",
             * "nameUzLatin": "ZAO"
             * },
             * "address": "МУКИМИЙ 166888",
             * "region": {
             * "id": 8,
             * "nameRu": "Сурхандарьинская Область",
             * "nameUz": "Сурхондарё Вилояти"
             * },
             * "city": {
             * "id": 63,
             * "regionId": 8,
             * "nameRu": "Кизирикский Район",
             * "nameUz": "Қизириқ Тумани"
             * },
             * "types": [
             * {
             * "id": 1,
             * "name": "Торговая деятельность",
             * "parentId": null
             * },
             * {
             * "id": 2,
             * "name": "Продажа алкогольной продукции",
             * "parentId": 1
             * }
             * ],
             * "paysNds": false,
             * "ndsPercent": null,
             * "warehouseEnabled": true,
             * "fiscal": true,
             * "syncDate": "2020-09-09T17:24:24.864",
             * "dataEntries": []
             * }
             */

            $result = Curl::run('/api/company/current', 'get', $token);

            if (isset($result['id'])) {
                return $result;
            }

        }

        return ['status' => 0, 'errors' => ['Компания не найдена!']];

    }

    // +информация о филиалах компании
    // branchId - id филиала для выбора терминалов

    public function actionBranch()
    {

        // получаем токен
        if ($token = Uzcassa::getToken()) {

            /**
             * результат запроса: /api/branches/selectbox
             *
             * [{
             * "id": 44462976,
             * "name": "tssssss",
             * "description": null,
             * "price": null,
             * "selected": null
             * },
             * {
             * "id": 95149,
             * "name": "YANGI TEXNOLOGIYALAR ILMIY-AXBOROT MARKAZI",
             * "description": null,
             * "price": null,
             * "selected": null
             * }
             * ]
             */

            $result = Curl::run('/api/branches/selectbox', 'get', $token);

            if (is_array($result) && isset($result[0]['id'])) {
                return $result;
            }

        }

        return ['status' => 0, 'errors' => ['Филиалы не найдены!']];

    }










}
