<?php

use yii\db\Migration;

/**
 * Class m210202_074524_add_number_to_user_table
 */
class m210202_074524_add_number_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'number', 'VARCHAR(255) AFTER id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210202_074524_add_number_to_user_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210202_074524_add_number_to_user_table cannot be reverted.\n";

        return false;
    }
    */
}
