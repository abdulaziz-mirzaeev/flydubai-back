<?php

use yii\db\Migration;

class m210222_054707_025_create_table_place_city extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%place_city}}',
            [
                'id' => $this->primaryKey(),
                'name' => $this->string(),
                'place_region_id' => $this->integer(),
                'created_at' => $this->string(50),
                'modified_at' => $this->dateTime(),
                'created_by' => $this->integer(),
                'modified_by' => $this->integer(),
            ],
            $tableOptions
        );

        $this->addForeignKey(
            'place_city_place_region',
            '{{%place_city}}',
            ['place_region_id'],
            '{{%place_region}}',
            ['id'],
            'NO ACTION',
            'NO ACTION'
        );
    }

    public function down()
    {
        $this->dropTable('{{%place_city}}');
    }
}
