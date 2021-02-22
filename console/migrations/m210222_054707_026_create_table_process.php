<?php

use yii\db\Migration;

class m210222_054707_026_create_table_process extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%process}}',
            [
                'id' => $this->primaryKey(),
                'order_id' => $this->integer()->comment('Заказ'),
                'summ' => $this->float()->comment('Сумма'),
                'summ_terminal' => $this->float()->defaultValue('0')->comment('Сумма с карты'),
                'cheque_number' => $this->string(64)->comment('Номер чека'),
                'nds' => $this->float()->defaultValue('0')->comment('НДС'),
                'cashier_from' => $this->integer()->comment('Касса откуда'),
                'cashier_id' => $this->integer()->comment('Касса куда'),
                'process_type' => $this->string()->comment('Тип процесса'),
                'status_director' => $this->boolean()->defaultValue('0')->comment('Статус директора'),
                'status' => $this->boolean()->defaultValue('0')->comment('Статус вывода'),
                'terminal_id' => $this->integer(),
                'payment_type' => $this->string(32)->comment('Тип оплаты'),
                'currency_id' => $this->integer()->comment('Тип валюты'),
                'currency_rate' => $this->float()->comment('Курс'),
                'comment' => $this->text()->comment('Комментарий'),
                'created_at' => $this->dateTime()->comment('Дата создания'),
                'created_by' => $this->integer()->comment('Создал'),
                'modified_at' => $this->dateTime(),
                'modified_by' => $this->integer(),
            ],
            $tableOptions
        );

        $this->createIndex('idx-process-cashier_id', '{{%process}}', ['cashier_id']);

        $this->addForeignKey(
            'fk-process-cashier_id',
            '{{%process}}',
            ['cashier_id'],
            '{{%cashier}}',
            ['id'],
            'CASCADE',
            'RESTRICT'
        );
        $this->addForeignKey(
            'fk-process-currency_id',
            '{{%process}}',
            ['currency_id'],
            '{{%currency}}',
            ['id'],
            'CASCADE',
            'RESTRICT'
        );
        $this->addForeignKey(
            'fk-process-order_id',
            '{{%process}}',
            ['order_id'],
            '{{%order}}',
            ['id'],
            'CASCADE',
            'RESTRICT'
        );
        $this->addForeignKey(
            'fk-process-terminal_id',
            '{{%process}}',
            ['terminal_id'],
            '{{%terminal}}',
            ['id'],
            'CASCADE',
            'RESTRICT'
        );
    }

    public function down()
    {
        $this->dropTable('{{%process}}');
    }
}
