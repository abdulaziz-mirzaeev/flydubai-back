<?php

use yii\db\Migration;

class m210222_054705_009_create_table_place_region extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%place_region}}',
            [
                'id' => $this->primaryKey(),
                'name' => $this->string(),
                'place_country_id' => $this->integer(),
                'delivery_price' => $this->integer(),
                'created_at' => $this->dateTime(),
                'modified_at' => $this->dateTime(),
                'created_by' => $this->integer(),
                'modified_by' => $this->integer(),
            ],
            $tableOptions
        );

        $this->addForeignKey(
            'place_region_place_country',
            '{{%place_region}}',
            ['place_country_id'],
            '{{%place_country}}',
            ['id'],
            'NO ACTION',
            'NO ACTION'
        );
    }

    public function down()
    {
        $this->dropTable('{{%place_region}}');
    }
}
