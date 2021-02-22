<?php

use yii\db\Migration;

class m210222_054706_023_create_table_operator extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%operator}}',
            [
                'id' => $this->primaryKey(),
                'number' => $this->string(24)->notNull()->defaultValue('0'),
                'user_id' => $this->integer(),
                'created_at' => $this->dateTime(),
                'created_by' => $this->integer(),
            ],
            $tableOptions
        );

        $this->addForeignKey(
            'fk_operator_user_id',
            '{{%operator}}',
            ['user_id'],
            '{{%user}}',
            ['id'],
            'CASCADE',
            'NO ACTION'
        );
    }

    public function down()
    {
        $this->dropTable('{{%operator}}');
    }
}
