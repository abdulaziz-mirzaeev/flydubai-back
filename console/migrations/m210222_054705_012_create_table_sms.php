<?php

use yii\db\Migration;

class m210222_054705_012_create_table_sms extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%sms}}',
            [
                'id' => $this->primaryKey(),
                'message' => $this->string(1024)->comment('Сообщение'),
                'error' => $this->string()->comment('Ошибка'),
                'phone' => $this->string(18)->comment('Телефон'),
                'status' => $this->boolean()->unsigned()->defaultValue('0')->comment('Статус'),
                'created_at' => $this->dateTime()->comment('Дата создания'),
                'created_by' => $this->integer()->comment('Создал'),
            ],
            $tableOptions
        );
    }

    public function down()
    {
        $this->dropTable('{{%sms}}');
    }
}
