<?php

use yii\db\Migration;

class m210222_054708_031_create_table_visa extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%visa}}',
            [
                'id' => $this->primaryKey(),
                'number' => $this->string(32)->comment('Номер'),
                'cost_price' => $this->float()->comment('Себестоимость визы'),
                'sell_price' => $this->float()->comment('Цена'),
                'visa_type_id' => $this->integer()->comment('Тип визы'),
                'visa_partner_id' => $this->integer()->comment('Партнер'),
                'client_id' => $this->integer(),
                'comment' => $this->text()->comment('Комментарий'),
                'flight_date' => $this->dateTime()->comment('Дата вылета'),
                'flight_return_date' => $this->dateTime()->comment('Дата возвращения'),
                'payment_at' => $this->dateTime()->comment('Дата оплаты'),
                'created_at' => $this->dateTime()->comment('Дата создания'),
                'modified_at' => $this->dateTime()->comment('Дата изменения'),
                'created_by' => $this->integer()->comment('Создал'),
                'modified_by' => $this->integer()->comment('Изменил'),
            ],
            $tableOptions
        );

        $this->createIndex('idx-visa-visa_partner_id', '{{%visa}}', ['visa_partner_id']);
        $this->createIndex('idx-visa-visa_type_id', '{{%visa}}', ['visa_type_id']);

        $this->addForeignKey(
            'fk-visa-client_id',
            '{{%visa}}',
            ['client_id'],
            '{{%client}}',
            ['id'],
            'CASCADE',
            'NO ACTION'
        );
        $this->addForeignKey(
            'fk-visa-visa_partner_id',
            '{{%visa}}',
            ['visa_partner_id'],
            '{{%visa_partner}}',
            ['id'],
            'CASCADE',
            'NO ACTION'
        );
        $this->addForeignKey(
            'fk-visa-visa_type_id',
            '{{%visa}}',
            ['visa_type_id'],
            '{{%visa_type}}',
            ['id'],
            'CASCADE',
            'NO ACTION'
        );
    }

    public function down()
    {
        $this->dropTable('{{%visa}}');
    }
}
