<?php

use yii\db\Migration;

class m210222_054707_027_create_table_receipt extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%receipt}}',
            [
                'id' => $this->primaryKey(),
                'order_id' => $this->integer()->comment('Заказ'),
                'uid' => $this->string(64)->comment('ID чека'),
                'data' => $this->text()->comment('Данные чека'),
                'status' => $this->string(24)->comment('Статус'),
                'created_at' => $this->dateTime()->comment('Дата создания'),
                'modified_at' => $this->dateTime()->comment('Дата изменения'),
                'created_by' => $this->integer()->comment('Создал'),
                'modified_by' => $this->integer()->comment('Изменил'),
            ],
            $tableOptions
        );

        $this->addForeignKey(
            'fk-receipt-order_id',
            '{{%receipt}}',
            ['order_id'],
            '{{%order}}',
            ['id'],
            'CASCADE',
            'NO ACTION'
        );
    }

    public function down()
    {
        $this->dropTable('{{%receipt}}');
    }
}
