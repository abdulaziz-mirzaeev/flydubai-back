<?php

use yii\db\Migration;

class m210222_054708_034_create_table_notification extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%notification}}',
            [
                'id' => $this->primaryKey(),
                'user_id' => $this->integer()->comment('Кому'),
                'process_id' => $this->integer()->comment('Процесс'),
                'message' => $this->string(512)->comment('Сообщение'),
                'type' => $this->tinyInteger()->comment('Тип сообщения'),
                'status' => $this->boolean()->comment('Статус'),
                'created_at' => $this->dateTime()->comment('Дата создания'),
                'modified_at' => $this->dateTime()->comment('Дата изменения'),
                'created_by' => $this->integer()->comment('Создал'),
                'modified_by' => $this->integer()->comment('Изменил'),
            ],
            $tableOptions
        );

        $this->addForeignKey(
            'FK_notification_process',
            '{{%notification}}',
            ['process_id'],
            '{{%process}}',
            ['id'],
            'CASCADE',
            'NO ACTION'
        );
        $this->addForeignKey(
            'FK_notification_user',
            '{{%notification}}',
            ['user_id'],
            '{{%user}}',
            ['id'],
            'NO ACTION',
            'NO ACTION'
        );
    }

    public function down()
    {
        $this->dropTable('{{%notification}}');
    }
}
