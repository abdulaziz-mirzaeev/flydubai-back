<?php

use yii\db\Migration;

class m210222_054706_022_create_table_client extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%client}}',
            [
                'id' => $this->primaryKey(),
                'first_name' => $this->string()->comment('Имя'),
                'last_name' => $this->string()->comment('Отчество'),
                'patronym' => $this->string()->comment('Фамилия'),
                'company' => $this->string()->comment('Компания'),
                'email' => $this->string(64)->comment('Email'),
                'phone' => $this->string(24)->comment('Телефон'),
                'client_type_id' => $this->integer()->comment('Тип клиента'),
                'send_newsletter' => $this->boolean()->defaultValue('0')->comment('Смс рассылка'),
                'client_number' => $this->string(32)->comment('Номер клиента'),
                'passport_serial' => $this->string(16)->comment('Серия паспорта'),
                'passport_number' => $this->string(32)->comment('Номер паспорта'),
                'created_at' => $this->dateTime()->comment('Дата создания'),
                'modified_at' => $this->dateTime()->comment('Дата изменения'),
                'created_by' => $this->integer()->comment('Создал'),
                'modified_by' => $this->integer()->comment('Изменил'),
            ],
            $tableOptions
        );

        $this->createIndex('idx-client-client_type_id', '{{%client}}', ['client_type_id']);

        $this->addForeignKey(
            'fk-client-client_type_id',
            '{{%client}}',
            ['client_type_id'],
            '{{%client_type}}',
            ['id'],
            'CASCADE',
            'NO ACTION'
        );
    }

    public function down()
    {
        $this->dropTable('{{%client}}');
    }
}
