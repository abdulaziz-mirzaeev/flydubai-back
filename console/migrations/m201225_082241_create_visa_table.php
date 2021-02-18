<?php

use common\models\traits\MigrationTrait;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%visa}}`.
 */
class m201225_082241_create_visa_table extends Migration
{
    use MigrationTrait;

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%visa}}', [
            'id' => $this->primaryKey(),
            'number' => $this->string(32)->comment('Номер'),
            'cost_price' => $this->float()->comment('Себестоимость визы'),
            'sell_price' => $this->float()->comment('Цена'),
            'client_id' => $this->integer()->comment('Клиент'),
            'visa_type_id' => $this->integer()->comment('Тип визы'),
            'visa_partner_id' => $this->integer()->comment('Партнер'),
            'comment' => $this->text()->null()->comment('Комментарий'),
            'flight_date'=>$this->dateTime()->comment('Дата вылета'),
            'flight_return_date'=>$this->dateTime()->comment('Дата возвращения'),
            'created_at'=>$this->dateTime()->comment('Дата создания'),
            'modified_at'=>$this->dateTime()->comment('Дата изменения'),
            'created_by'=>$this->integer()->comment('Создал'),
            'modified_by'=>$this->integer()->comment('Изменил'),
        ]);

        $this->addIndex('visa','client_id','client');
        $this->addIndex('visa','visa_type_id','visa_type');
        $this->addIndex('visa','visa_partner_id','visa_partner');


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addIndex('visa','client_id');
        $this->removeIndex('visa','visa_type_id');
        $this->removeIndex('visa','visa_partner_id');

        $this->dropTable('{{%visa}}');
    }
}
