<?php

use common\models\traits\MigrationTrait;
use yii\db\Migration;

/**
 * Handles adding columns to table `{{%process}}`.
 */
class m201228_051008_add_order_id_column_to_process_table extends Migration
{
    use MigrationTrait;
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%process}}', 'order_id', $this->integer()->notNull()->comment('Заказ'));
        $this->addIndex('process','order_id','order');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->removeIndex('process','order_id');

        $this->dropColumn('{{%process}}', 'order_id');
    }
}
