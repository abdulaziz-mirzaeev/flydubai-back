<?php

use yii\db\Migration;

class m210222_054708_033_create_table_cargo extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%cargo}}',
            [
                'id' => $this->primaryKey(),
                'air_waybill' => $this->string(32)->comment('Авианакладная'),
                'client_type_id' => $this->integer()->comment('Тип клиента'),
                'client_id' => $this->integer()->comment('Клиент'),
                'company_id' => $this->integer()->comment('Компания'),
                'package_amount' => $this->integer()->comment('Количество'),
                'package_weight' => $this->integer()->comment('Вес'),
                'package_type_id' => $this->integer()->comment('Тип'),
                'cost_price' => $this->float()->comment('Себестоимость'),
                'sell_price' => $this->float()->comment('Цена продажи'),
                'comment' => $this->text()->comment('Комментарий'),
                'payment_at' => $this->dateTime(),
                'created_at' => $this->dateTime()->comment('Дата создания'),
                'modified_at' => $this->dateTime()->comment('Дата изменения'),
                'created_by' => $this->integer()->comment('Создал'),
                'modified_by' => $this->integer()->comment('Изменил'),
            ],
            $tableOptions
        );

        $this->createIndex('idx-cargo-client_type_id', '{{%cargo}}', ['client_type_id']);
        $this->createIndex('idx-cargo-company_id', '{{%cargo}}', ['client_id']);
        $this->createIndex('idx-cargo-package_type_id', '{{%cargo}}', ['package_type_id']);

        $this->addForeignKey(
            'fk-cargo-client_id',
            '{{%cargo}}',
            ['client_id'],
            '{{%client}}',
            ['id'],
            'CASCADE',
            'NO ACTION'
        );
        $this->addForeignKey(
            'fk-cargo-client_type_id',
            '{{%cargo}}',
            ['client_type_id'],
            '{{%client_type}}',
            ['id'],
            'CASCADE',
            'NO ACTION'
        );
        $this->addForeignKey(
            'fk-cargo-company_id',
            '{{%cargo}}',
            ['company_id'],
            '{{%company}}',
            ['id'],
            'CASCADE',
            'NO ACTION'
        );
        $this->addForeignKey(
            'fk-cargo-package_type_id',
            '{{%cargo}}',
            ['package_type_id'],
            '{{%package_type}}',
            ['id'],
            'CASCADE',
            'NO ACTION'
        );
    }

    public function down()
    {
        $this->dropTable('{{%cargo}}');
    }
}
