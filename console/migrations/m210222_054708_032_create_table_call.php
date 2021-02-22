<?php

use yii\db\Migration;

class m210222_054708_032_create_table_call extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%call}}',
            [
                'id' => $this->primaryKey(),
                'operator_id' => $this->integer()->comment('Оператор'),
                'client_id' => $this->integer()->comment('Клиент'),
                'status' => $this->string(32)->comment('Статус'),
                'comment' => $this->text()->comment('Комментарий'),
                'created_at' => $this->dateTime()->comment('Дата создания'),
                'modified_at' => $this->dateTime()->comment('Дата изменения'),
                'created_by' => $this->integer()->comment('Создал'),
                'modified_by' => $this->integer()->comment('Изменил'),
            ],
            $tableOptions
        );

        $this->createIndex('idx-call-client_id', '{{%call}}', ['client_id']);

        $this->addForeignKey(
            'fk-call-client_id',
            '{{%call}}',
            ['client_id'],
            '{{%client}}',
            ['id'],
            'CASCADE',
            'NO ACTION'
        );
        $this->addForeignKey(
            'fk-call-operator_id',
            '{{%call}}',
            ['operator_id'],
            '{{%operator}}',
            ['id'],
            'CASCADE',
            'NO ACTION'
        );
    }

    public function down()
    {
        $this->dropTable('{{%call}}');
    }
}
