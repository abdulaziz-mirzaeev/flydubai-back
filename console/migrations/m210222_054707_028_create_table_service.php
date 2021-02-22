<?php

use yii\db\Migration;

class m210222_054707_028_create_table_service extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%service}}',
            [
                'id' => $this->primaryKey(),
                'order_id' => $this->integer()->notNull()->comment('Заказ'),
                'name' => $this->string()->comment('Название'),
                'summ' => $this->decimal(10, 2)->unsigned()->comment('Стоимость'),
                'comission' => $this->decimal(10, 2)->unsigned()->comment('Комиссия'),
                'nds' => $this->decimal(10, 2)->unsigned()->comment('НДС'),
                'created_at' => $this->dateTime()->comment('Дата создания'),
                'modified_at' => $this->dateTime()->comment('Дата изменения'),
                'created_by' => $this->integer()->comment('Создал'),
                'modified_by' => $this->integer()->comment('Изменил'),
            ],
            $tableOptions
        );

        $this->addForeignKey(
            'FK_services_order',
            '{{%service}}',
            ['order_id'],
            '{{%order}}',
            ['id'],
            'CASCADE',
            'NO ACTION'
        );
    }

    public function down()
    {
        $this->dropTable('{{%service}}');
    }
}
