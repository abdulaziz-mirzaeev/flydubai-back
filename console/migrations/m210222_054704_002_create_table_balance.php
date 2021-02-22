<?php

use yii\db\Migration;

class m210222_054704_002_create_table_balance extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%balance}}',
            [
                'id' => $this->primaryKey(),
                'summ' => $this->decimal(16, 2)->comment('Сумма баланса из 1С'),
                'invoice' => $this->string(64),
                'date' => $this->dateTime()->comment('Дата баланса'),
                'created_at' => $this->dateTime(),
                'modified_at' => $this->dateTime(),
                'created_by' => $this->integer(),
                'modified_by' => $this->integer(),
            ],
            $tableOptions
        );
    }

    public function down()
    {
        $this->dropTable('{{%balance}}');
    }
}
