<?php

use yii\db\Migration;

class m210222_054705_010_create_table_product extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%product}}',
            [
                'id' => $this->primaryKey(),
                'productID' => $this->string(32)->comment('ID товара'),
                'name' => $this->string()->comment('Название'),
                'type' => $this->string(32)->comment('Тип'),
                'summ' => $this->decimal(10, 2)->comment('Стоимость'),
                'comission' => $this->decimal(10, 2)->comment('Комиссия'),
                'nds' => $this->decimal(10, 2)->comment('НДС'),
                'created_at' => $this->dateTime()->comment('Дата создания'),
                'modified_at' => $this->dateTime()->comment('Дата изменения'),
                'created_by' => $this->integer()->comment('Создал'),
                'modified_by' => $this->integer()->comment('Изменил'),
            ],
            $tableOptions
        );
    }

    public function down()
    {
        $this->dropTable('{{%product}}');
    }
}
