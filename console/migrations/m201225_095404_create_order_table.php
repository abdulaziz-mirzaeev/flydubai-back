<?php

use common\models\traits\MigrationTrait;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%order}}`.
 */
class m201225_095404_create_order_table extends Migration
{
    use MigrationTrait;

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->createTable('{{%order}}', [
            'id' => $this->primaryKey(),
            'number' => $this->string(32)->comment('Номер счета'),
            'price' => $this->float()->comment('Сумма'),
            'price_add' => $this->float()->comment('Сумма доп способом'),
            'client_id' => $this->integer()->comment('Клиент'),
            'valute_id' => $this->integer()->comment('Валюта'),
            'cashier_id' => $this->integer()->comment('Касса'),
            'currency' => $this->float()->comment('Курс'),
            'converted' => $this->float()->comment('Сумма по курсу'),
            'payment_type' => $this->tinyInteger(1)->comment('Тип оплаты'),
            'payment_type_add' => $this->tinyInteger(1)->comment('Дополнительный тип оплаты'),
            'status' => $this->boolean()->comment('Статус' ),
            'created_at'=>$this->dateTime()->comment('Дата создания'),
            'modified_at'=>$this->dateTime()->comment('Дата изменения'),
            'created_by'=>$this->integer()->comment('Создал'),
            'modified_by'=>$this->integer()->comment('Изменил'),

        ]);

        $this->addIndex('order','valute_id','valute');
        $this->addIndex('order','cashier_id','cashier');
        $this->addIndex('order','client_id','client');


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->removeIndex('order','valute_id');
        $this->removeIndex('order','cashier_id');
        $this->removeIndex('order','client_id');

        $this->dropTable('{{%order}}');
    }
}
