<?php


namespace backend\models;

use Yii;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[ShopOrder]].
 *
 * @see ShopOrder
 */
class OrderQuery extends ActiveQuery
{

    public function status($status = null)
    {
        return $this->andWhere(['status' => $status]);
    }


    /**
     * {@inheritdoc}
     * @return Order[]|array
     */
    public function all($db = null)
    {
        // всегда присоединять к заказу тип
        $this->with(['order']);
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Order|array|null
     */
    public function one($db = null)
    {
        $this->with(['order']);
        return parent::one($db);
    }
}