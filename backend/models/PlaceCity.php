<?php

namespace app\models;

use app\traits\BaseModelTrait;
use Yii;

/**
 * This is the model class for table "place_city".
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $place_region_id
 * @property string|null $created_at
 * @property string|null $modified_at
 * @property int|null $created_by
 * @property int|null $modified_by
 *
 * @property PlaceRegion $placeRegion
 * @property array $columns;
 */
class PlaceCity extends \backend\models\BaseModel
{

    use BaseModelTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'place_city';
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['place_region_id', 'created_by', 'modified_by'], 'integer'],
            [['created_at', 'modified_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['place_region_id'], 'exist', 'skipOnError' => true, 'targetClass' => PlaceRegion::className(), 'targetAttribute' => ['place_region_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'place_region_id' => 'Place Region ID',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
            'created_by' => 'Created By',
            'modified_by' => 'Modified By',
        ];
    }


    public function fields()
    {
        return [
            'id',
            'created_at',
            'name',
//            'place_region_id' => function ($model) {
//                return $model->placeRegion;
//            },
        ];
    }

    /**
     * Gets query for [[PlaceRegion]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPlaceRegion()
    {
        return $this->hasOne(PlaceRegion::className(), ['id' => 'place_region_id']);
    }
}
