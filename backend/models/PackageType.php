<?php

namespace backend\models;

use app\traits\BaseModelTrait;
use Yii;

/**
 * This is the model class for table "package_type".
 *
 * @property int $id
 * @property string|null $name Название
 * @property string|null $created_at Дата создания
 * @property string|null $modified_at Дата изменения
 * @property int|null $created_by Создал
 * @property int|null $modified_by Изменил
 *
 * @property Cargo[] $cargos
 */
class PackageType extends \backend\models\BaseModel
{
    use BaseModelTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'package_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'modified_at'], 'safe'],
            [['created_by', 'modified_by'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'created_at' => 'Дата создания',
            'modified_at' => 'Дата изменения',
            'created_by' => 'Создал',
            'modified_by' => 'Изменил',
        ];
    }

    /**
     * Gets query for [[Cargos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCargos()
    {
        return $this->hasMany(Cargo::className(), ['package_type_id' => 'id']);
    }
}
