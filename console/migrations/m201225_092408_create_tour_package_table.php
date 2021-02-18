<?php

use common\models\traits\MigrationTrait;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%tour_package}}`.
 */
class m201225_092408_create_tour_package_table extends Migration
{
    use MigrationTrait;
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%tour_package}}', [
            'id' => $this->primaryKey(),
            'tour_operator_id' => $this->integer()->comment('Тур оператор'),
            'tour_id' => $this->integer()->comment('Тур'),
            'tour_partner_id' => $this->integer()->comment('Партнер по турпакетам'),
            'client_id' => $this->integer()->comment('Клиент'),
            'cost_price' => $this->float()->comment('Себестоимость визы'),
            'sell_price' => $this->float()->comment('Цена'),
            'comment' => $this->text()->comment('Комментарий'),
            'created_at'=>$this->dateTime()->comment('Дата создания'),
            'modified_at'=>$this->dateTime()->comment('Дата изменения'),
            'created_by'=>$this->integer()->comment('Создал'),
            'modified_by'=>$this->integer()->comment('Изменил'),
        ]);

        $this->addIndex('tour_package','tour_operator_id','tour_operator');
        $this->addIndex('tour_package','tour_id','tour');
        $this->addIndex('tour_package','tour_partner_id','tour_partner');
        $this->addIndex('tour_package','client_id','client');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->removeIndex('tour_package','tour_operator_id');
        $this->removeIndex('tour_package','tour_id');
        $this->removeIndex('tour_package','tour_partner_id');
        $this->removeIndex('tour_package','client_id');

        $this->dropTable('{{%tour_package}}');
    }
}
