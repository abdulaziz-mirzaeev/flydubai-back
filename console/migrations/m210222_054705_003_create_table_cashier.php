<?php

use yii\db\Migration;

class m210222_054705_003_create_table_cashier extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%cashier}}',
            [
                'id' => $this->primaryKey(),
                'name' => $this->string(32)->notNull()->comment('Название'),
                'summ_terminal' => $this->float()->notNull()->defaultValue('0')->comment('Сумма терминал'),
                'summ' => $this->float()->notNull()->defaultValue('0')->comment('Сумма'),
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
        $this->dropTable('{{%cashier}}');
    }
}
