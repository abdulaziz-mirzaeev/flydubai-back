<?php

use yii\db\Migration;

class m210222_054707_029_create_table_ticket extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%ticket}}',
            [
                'id' => $this->primaryKey()->unsigned(),
                'parent_id' => $this->integer()->unsigned()->comment('Туда и обратно'),
                'flight_number' => $this->string(32)->comment('Номер рейса'),
                'flight_route' => $this->string()->comment('Направление рейса'),
                'cost_price' => $this->float()->comment('Себестоимость '),
                'sell_price' => $this->float()->unsigned()->comment('Цена'),
                'tariff_id' => $this->integer()->comment('Тариф'),
                'tariff_type' => $this->string()->comment('Тип тарифа'),
                'pnr' => $this->string(24)->comment('ID (prn) билета'),
                'client_id' => $this->integer()->comment('Клиент'),
                'passenger_count' => $this->tinyInteger()->unsigned()->comment('Пассажиров'),
                'comment' => $this->text()->comment('Комментарий'),
                'flight_date' => $this->dateTime()->comment('Дата вылета'),
                'payment_at' => $this->dateTime()->comment('Дата оплаты'),
                'created_at' => $this->dateTime()->comment('Дата создания'),
                'modified_at' => $this->dateTime()->comment('Дата изменения'),
                'created_by' => $this->integer()->comment('Создал'),
                'modified_by' => $this->integer()->comment('Изменил'),
            ],
            $tableOptions
        );

        $this->createIndex('idx-ticket-client_id', '{{%ticket}}', ['client_id']);
        $this->createIndex('idx-ticket-tariff_id', '{{%ticket}}', ['tariff_id']);

        $this->addForeignKey(
            'fk-ticket-client_id',
            '{{%ticket}}',
            ['client_id'],
            '{{%client}}',
            ['id'],
            'CASCADE',
            'NO ACTION'
        );
        $this->addForeignKey(
            'fk-ticket-tariff_id',
            '{{%ticket}}',
            ['tariff_id'],
            '{{%tariff}}',
            ['id'],
            'CASCADE',
            'NO ACTION'
        );
    }

    public function down()
    {
        $this->dropTable('{{%ticket}}');
    }
}
