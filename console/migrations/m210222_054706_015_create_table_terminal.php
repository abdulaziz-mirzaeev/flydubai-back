<?php

use yii\db\Migration;

class m210222_054706_015_create_table_terminal extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%terminal}}',
            [
                'id' => $this->primaryKey(),
                'terminalID' => $this->string(24)->comment('ID терминала в uzcassa'),
                'terminalSN' => $this->string(24)->comment('Серия'),
                'terminalModel' => $this->string(24)->comment('Модель'),
                'branch_id' => $this->integer()->comment('Филиал в uzcassa'),
                'company_id' => $this->integer()->comment('Компания в uzcassa'),
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
        $this->dropTable('{{%terminal}}');
    }
}
