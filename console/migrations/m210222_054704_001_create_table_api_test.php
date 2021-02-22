<?php

use yii\db\Migration;

class m210222_054704_001_create_table_api_test extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%api_test}}',
            [
                'id' => $this->primaryKey(),
                'number' => $this->string(11),
            ],
            $tableOptions
        );
    }

    public function down()
    {
        $this->dropTable('{{%api_test}}');
    }
}
