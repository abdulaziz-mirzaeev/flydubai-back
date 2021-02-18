<?php

namespace app\models;

use app\traits\BaseModelTrait;
use Yii;

/**
 * This is the model class for table "place_region".
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $place_country_id
 * @property int|null $delivery_price
 * @property string|null $created_at
 * @property string|null $modified_at
 * @property int|null $created_by
 * @property int|null $modified_by
 *
 * @property PlaceCity[] $placeCities
 * @property PlaceCountry $placeCountry
 */
class PlaceRegion extends \backend\models\BaseModel
{
    use BaseModelTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'place_region';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['place_country_id', 'delivery_price', 'created_by', 'modified_by'], 'integer'],
            [['created_at', 'modified_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['place_country_id'], 'exist', 'skipOnError' => true, 'targetClass' => PlaceCountry::className(), 'targetAttribute' => ['place_country_id' => 'id']],
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
            'place_country_id' => 'Place Country ID',
            'delivery_price' => 'Delivery Price',
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
            'name',
            'place_country_id' => function ($model) {
                return $model->placeCountry; // Return related model property, correct according to your structure
            },
            'delivery_price',
            'created_at',
        ];
    }


    /**
     * Gets query for [[PlaceCities]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPlaceCities()
    {
        return $this->hasMany(PlaceCity::className(), ['place_region_id' => 'id']);
    }

    /**
     * Gets query for [[PlaceCountry]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPlaceCountry()
    {
        return $this->hasOne(PlaceCountry::className(), ['id' => 'place_country_id']);
    }
}
