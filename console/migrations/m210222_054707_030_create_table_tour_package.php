<?php

use yii\db\Migration;

class m210222_054707_030_create_table_tour_package extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%tour_package}}',
            [
                'id' => $this->primaryKey(),
                'tour_operator_id' => $this->integer()->comment('Тур оператор'),
                'tour_id' => $this->integer()->comment('Тур'),
                'tour_partner_id' => $this->integer()->comment('Партнер по турпакетам'),
                'client_id' => $this->integer()->comment('Клиент'),
                'cost_price' => $this->float()->comment('Себестоимость визы'),
                'sell_price' => $this->float()->comment('Цена'),
                'comment' => $this->string(1024)->comment('Комментарий'),
                'payment_at' => $this->dateTime(),
                'created_at' => $this->dateTime()->comment('Дата создания'),
                'modified_at' => $this->dateTime()->comment('Дата изменения'),
                'created_by' => $this->integer()->comment('Создал'),
                'modified_by' => $this->integer()->comment('Изменил'),
            ],
            $tableOptions
        );

        $this->createIndex('idx-tour_package-client_id', '{{%tour_package}}', ['client_id']);
        $this->createIndex('idx-tour_package-tour_id', '{{%tour_package}}', ['tour_id']);
        $this->createIndex('idx-tour_package-tour_operator_id', '{{%tour_package}}', ['tour_operator_id']);
        $this->createIndex('idx-tour_package-tour_partner_id', '{{%tour_package}}', ['tour_partner_id']);

        $this->addForeignKey(
            'fk-tour_package-client_id',
            '{{%tour_package}}',
            ['client_id'],
            '{{%client}}',
            ['id'],
            'CASCADE',
            'NO ACTION'
        );
        $this->addForeignKey(
            'fk-tour_package-tour_id',
            '{{%tour_package}}',
            ['tour_id'],
            '{{%tour}}',
            ['id'],
            'CASCADE',
            'NO ACTION'
        );
        $this->addForeignKey(
            'fk-tour_package-tour_operator_id',
            '{{%tour_package}}',
            ['tour_operator_id'],
            '{{%tour_operator}}',
            ['id'],
            'CASCADE',
            'NO ACTION'
        );
        $this->addForeignKey(
            'fk-tour_package-tour_partner_id',
            '{{%tour_package}}',
            ['tour_partner_id'],
            '{{%tour_partner}}',
            ['id'],
            'CASCADE',
            'NO ACTION'
        );
    }

    public function down()
    {
        $this->dropTable('{{%tour_package}}');
    }
}
