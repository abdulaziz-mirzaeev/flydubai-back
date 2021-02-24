<?php


namespace backend\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\data\Sort;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;

die('hello');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT');
header("Access-Control-Allow-Headers: *");
header('Content-Type: application/json');

//$method = $_SERVER['REQUEST_METHOD'];
//d($method,1);

//$method = $_SERVER['REQUEST_METHOD'];
//
//d($method,1);

class BaseController extends ActiveController
{
    public $enableCsrfValidation = false;
    public $modelClass = self::class;

    public $defaultPageSize = 15;

    // сохраняем поля типа string
    public $stringFields = [];

    public $get;
    public $post;


    // авторизация по токену
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['authMethods'] = [
            HttpBearerAuth::class
        ];

        return $behaviors;
    }


    public function init()
    {

        if ( Yii::$app->request->isOptions ) {
            Yii::$app->response->setStatusCode(200);
            exit;
        }

    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionSearch()
    {
        $query = $this->modelClass::find();
        return new ActiveDataProvider([
            'query' => $this->getQuery($query),
            'pagination' => false,
            'sort' => new Sort()
        ]);
    }

    public function getQuery(&$query)
    {

        $this->getWhere($query);

        $this->getWhereLike($query);

        // условие в запросе
        $this->getConditions($query);

        return $query;

    }

    public function getWhere(&$query)
    {

        $arr_query = $this->getQueryAttributes();

        $result = [];

        if ( !$arr_query ) return $result;

        $fields = $this->getStringFields();

        if ( $arr_query ) {
            foreach ( $arr_query as $k => $v ) {
                if ( !in_array($k, $fields) ) {
                    // берем точное совпадение
                    //$result[$k] = $v;
                    $query->andWhere([$k => $v]);
                }
            }
        }

        return true; // $result;

    }

    /** получаем список всех атрибутов выбранной модели в get запросе Model[id]=..&Model[attr]=.. */
    public function getQueryAttributes()
    {

        $this->get = \Yii::$app->request->get(trim($this->modelClass::baseModelClass()));
        return $this->get;
    }

    /** поиск строковых значений */
    public function getStringFields()
    {
        if ( $this->stringFields ) {
            return $this->stringFields;
        }

        $result = [];
        foreach ( (new $this->modelClass())->rules() as $rule ) {
            if ( $rule[1] == 'string' ) {
                foreach ( $rule[0] as $name ) {
                    $result[] = $name;
                }
            }
        }
        $this->stringFields = $result;
        return $result;
    }

    // поиск по значению

    public function getWhereLike(&$query)
    {

        $fields = $this->getQueryAttributes();

        if ( empty($fields) ) return false;

        $strings = $this->getStringFields();

        foreach ( $strings as $name ) {

            if ( key_exists($name, $fields) ) {
                $query->andWhere(['like', "$name", $fields[$name]]);
            }

        }

        //q($query,1);

        return true;
    }

    // для поиска строковых значений

    /**  формат для выборочного условия в фильтрации
     * пример:
     * ?condition[field]=id&condition[condition]=>&condition[value]=10
     * /placecity/search?condition[field]=name&condition[condition]=like&condition[value]=район
     * condition[
     *      field = id,
     *      condition = >,
     *      value = 10
     * ]
     * */
    public function getConditions(&$query)
    {

        $conditions = Yii::$app->request->get('condition'); //   несколько условий like, >, <, =, !=, <>

        // если есть условие
        if ( isset($conditions) ) {
            if ( is_array($conditions['field']) && count($conditions['field']) > 1 ) {
                // запрос несколько условий AND ?condition[field][]=id&condition[condition][]=>&condition[value][]=2&condition[field][]=cost_price&condition[condition][]=>&condition[value][]=100
                for ( $i = 0; $i < count($conditions['field']); $i++ ) {
                    $query->andWhere([$conditions['condition'][$i], $conditions['field'][$i], $conditions['value'][$i]]);
                }
            } else {
                if ( is_array($conditions['field']) ) {
                    // запрос 1 элемент в массиве ?condition[field][]=id&condition[condition][]=>&condition[value][]=2
                    $i = 0;
                    $query->andWhere([$conditions['condition'][$i], $conditions['field'][$i], $conditions['value'][$i]]);
                } else {
                    // запрос 1 элемент без массива ?condition[field]=id&condition[condition]=>&condition[value]=2
                    $query->andWhere([$conditions['condition'], $conditions['field'], $conditions['value']]);
                }
            }
            return true;
        }

        return false;
    }


    // формирование query запроса

    public function getPost()
    {
        $this->post = \Yii::$app->request->post();
        return $this->post;
    }

    public function getPageSize()
    {
        return \Yii::$app->request->get('page') ?? $this->defaultPageSize;
    }

    public function actionAll()
    {

        $provider = new ActiveDataProvider([
            'query' => $this->modelClass::find(),
            'pagination' => false,
        ]);

        return $provider;

    }

    // получить все записи в таблице выбранной модели

    public function actionIndex()
    {
        $provider = new ActiveDataProvider([
            'query' => $this->modelClass::find(),
            'pagination' => false,
        ]);

        return $provider;
    }

    public function actionInfo()
    {
        return $this->modelClass::getInfo();
    }

    // получение информации о моделе

    protected function verbs()
    {
        return [
            'index' => ['GET'],
            'view' => ['GET'],
            'create' => ['POST', 'OPTIONS'],
            'update' => ['POST'],
            'delete' => ['POST']
        ];
    }


}