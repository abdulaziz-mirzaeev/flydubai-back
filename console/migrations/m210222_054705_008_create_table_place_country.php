<?php

use yii\db\Migration;

class m210222_054705_008_create_table_place_country extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%place_country}}',
            [
                'id' => $this->primaryKey(),
                'name' => $this->string(),
                'numeric' => $this->integer(),
                'phone_code' => $this->string(11),
                'currency_id' => $this->integer(),
                'created_at' => $this->dateTime(),
                'modified_at' => $this->dateTime(),
                'created_by' => $this->integer(),
                'modified_by' => $this->integer(),
            ],
            $tableOptions
        );

        $this->addForeignKey(
            'fk-place_country-currency_id',
            '{{%place_country}}',
            ['currency_id'],
            '{{%currency}}',
            ['id'],
            'NO ACTION',
            'NO ACTION'
        );
    }

    public function down()
    {
        $this->dropTable('{{%place_country}}');
    }
}
