<?php

use yii\db\Migration;

class m210222_054707_024_create_table_order extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%order}}',
            [
                'id' => $this->primaryKey(),
                'number' => $this->string(32)->comment('Номер заказа'),
                'type' => $this->string(32)->comment('Тип'),
                'type_id' => $this->integer()->comment('id билет, виза,'),
                'operator_id' => $this->integer()->comment('Оператор'),
                'terminal_id' => $this->integer()->comment('Терминал'),
                'cashier_id' => $this->integer()->comment('Касса'),
                'count' => $this->integer()->defaultValue('1')->comment('Количество'),
                'summ' => $this->decimal(10, 2)->unsigned()->comment('Сумма'),
                'summ_terminal' => $this->decimal(10, 2)->unsigned()->comment('Сумма с карты'),
                'cheque_number' => $this->string(64)->comment('Номер чека'),
                'nds' => $this->decimal(10, 2)->unsigned()->comment('НДС'),
                'payment_type' => $this->string(32)->comment('Способ оплаты'),
                'status' => $this->string(32)->comment('Статус'),
                'created_at' => $this->dateTime()->comment('Дата создания'),
                'modified_at' => $this->dateTime()->comment('Дата изменения'),
                'payment_at' => $this->dateTime()->comment('Дата оплаты'),
                'created_by' => $this->integer()->comment('Создал'),
                'modified_by' => $this->integer()->comment('Изменил'),
            ],
            $tableOptions
        );

        $this->addForeignKey(
            'fk-order-operator_id',
            '{{%order}}',
            ['operator_id'],
            '{{%operator}}',
            ['id'],
            'CASCADE',
            'NO ACTION'
        );
        $this->addForeignKey(
            'FK_order_cashier',
            '{{%order}}',
            ['cashier_id'],
            '{{%cashier}}',
            ['id'],
            'NO ACTION',
            'NO ACTION'
        );
        $this->addForeignKey(
            'FK_order_terminal',
            '{{%order}}',
            ['terminal_id'],
            '{{%terminal}}',
            ['id'],
            'CASCADE',
            'NO ACTION'
        );
    }

    public function down()
    {
        $this->dropTable('{{%order}}');
    }
}
