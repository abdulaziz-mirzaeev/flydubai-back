<?php

use yii\db\Migration;

class m210222_054705_005_create_table_company extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%company}}',
            [
                'id' => $this->primaryKey(),
                'name' => $this->string()->comment('Название'),
                'created_at' => $this->dateTime()->comment('Дата создания'),
                'modified_at' => $this->dateTime()->comment('Дата изменения'),
                'created_by' => $this->integer()->comment('Создал'),
                'modified_by' => $this->integer()->comment('Изменил'),
            ],
            $tableOptions
        );
    }

    public function down()
    {
        $this->dropTable('{{%company}}');
    }
}
