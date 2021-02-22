<?php

use yii\db\Migration;

class m210222_054705_011_create_table_sklad_test extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%sklad_test}}',
            [
                'id' => $this->primaryKey(),
                'ware_id' => $this->integer(),
                'shop_catalog_id' => $this->integer(),
                'amount' => $this->integer(),
            ],
            $tableOptions
        );
    }

    public function down()
    {
        $this->dropTable('{{%sklad_test}}');
    }
}
