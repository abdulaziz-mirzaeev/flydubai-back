<?php

use yii\db\Migration;

/**
 * Class m210315_104220_add_column_to_ticket_notification_is_sended
 */
class m210315_104220_add_column_to_ticket_notification_is_sended extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('ticket', 'notified', $this->tinyInteger(2)->after('flight_date')->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210315_104220_add_column_to_ticket_notification_is_sended cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210315_104220_add_column_to_ticket_notification_is_sended cannot be reverted.\n";

        return false;
    }
    */
}
