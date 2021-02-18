<?php

namespace app\models;

use app\traits\BaseModelTrait;
use Yii;

/**
 * This is the model class for table "place_country".
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $numeric
 * @property string|null $phone_code
 * @property int|null $currency_id
 * @property string|null $created_at
 * @property string|null $modified_at
 * @property int|null $created_by
 * @property int|null $modified_by
 *
 * @property PlaceRegion[] $placeRegions
 */
class PlaceCountry extends \backend\models\BaseModel
{
    use BaseModelTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'place_country';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['numeric', 'currency_id', 'created_by', 'modified_by'], 'integer'],
            [['created_at', 'modified_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['phone_code'], 'string', 'max' => 11],
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
            'numeric' => 'Numeric',
            'phone_code' => 'Phone Code',
            'currency_id' => 'Currency ID',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
            'created_by' => 'Created By',
            'modified_by' => 'Modified By',
        ];
    }

    /**
     * Gets query for [[PlaceRegions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPlaceRegions()
    {
        return $this->hasMany(PlaceRegion::className(), ['place_country_id' => 'id']);
    }
}
