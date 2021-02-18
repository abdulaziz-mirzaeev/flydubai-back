<?php
namespace backend\controllers;

use app\models\PlaceCity;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\data\Sort;


class PlacecityController extends BaseController
{
    public $modelClass = PlaceCity::class;

    // 10.10.3.30:9090/placecity/search?sort=id&page=2&per-page=5


    public function actionIndex(){

        die('hello');
    }
    public function actionSearch()
    {
        $query = PlaceCity::find();

        return new ActiveDataProvider([
            'query' => $this->getQuery($query),
            'sort' => new Sort(),
            'pagination' => new Pagination(['pageSize'=>100])
//            'pagination' => false
        ]);
    }

    public function actions()

    {

        $actions = parent::actions();
      //  unset($actions['create'], $actions['update'], $actions['delete']);

        $actions['index'] = [
            'class' => 'yii\rest\IndexAction',
            'modelClass' => $this->modelClass,
            'prepareDataProvider' => function () {
                return new ActiveDataProvider([
                    'query' => $this->modelClass::find(),
                    'pagination' => new Pagination(['pageSize'=>10000])
                ]);
            },
        ];

        return $actions;
    }

}