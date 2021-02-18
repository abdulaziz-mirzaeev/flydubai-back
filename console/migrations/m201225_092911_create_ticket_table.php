<?php

use common\models\traits\MigrationTrait;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%ticket}}`.
 */
class m201225_092911_create_ticket_table extends Migration
{
    use MigrationTrait;
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%ticket}}', [
            'id' => $this->primaryKey(),
            'flight_number' => $this->string(32)->comment('Номер рейса'),
            'flight_route' => $this->string(255)->comment('Направление рейса'),
            'cost_price' => $this->float()->comment('Себестоимость визы'),
            'sell_price' => $this->float()->comment('Цена'),
            'tariff_id' => $this->integer()->comment('Тариф'),
            'tariff_type_id' => $this->integer()->comment('Тип тарифа'),
            'ticket_id' => $this->integer()->comment('ID (prn) билета'),
            'client_id' => $this->integer()->comment('Клиент'),
            'comment' => $this->text()->comment('Комментарий'),
            'flight_date'=>$this->dateTime()->comment('Дата вылета'),
            //'flight_return_date'=>$this->dateTime()->comment('Дата возвращения'),
            'created_at'=>$this->dateTime()->comment('Дата создания'),
            'modified_at'=>$this->dateTime()->comment('Дата изменения'),
            'created_by'=>$this->integer()->comment('Создал'),
            'modified_by'=>$this->integer()->comment('Изменил'),
        ]);

        $this->addIndex('ticket','tariff_id','tariff');
        $this->addIndex('ticket','tariff_type_id','tariff_type');
        $this->addIndex('ticket','client_id','client');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->removeIndex('ticket','tariff_id');
        $this->removeIndex('ticket','tariff_type_id');
        $this->removeIndex('ticket','client_id');

        $this->dropTable('{{%ticket}}');
    }
}
