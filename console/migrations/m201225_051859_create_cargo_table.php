<?php

use common\models\traits\MigrationTrait;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%cargo}}`.
 */
class m201225_051859_create_cargo_table extends Migration
{

    use MigrationTrait;

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%cargo}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->null()->comment('Название'),
            'air_waybill_id' => $this->integer()->comment('Авианакладная'),
            'client_type_id' => $this->integer()->comment('Тип клиента'),
            'company_id' => $this->integer()->comment('Компания'),
            'package_amount' => $this->integer()->comment('Количество'),
            'package_weight' => $this->integer()->comment('Вес'),
            'package_type_id' => $this->integer()->comment('Тип'),
            'order_cost_price' => $this->float()->comment('Себестоимость'),
            'sell_price' => $this->float()->comment('Цена продажи'),
            'comment' => $this->text()->comment('Комментарий'),
            'created_at'=>$this->dateTime()->comment('Дата создания'),
            'modified_at'=>$this->dateTime()->comment('Дата изменения'),
            'created_by'=>$this->integer()->comment('Создал'),
            'modified_by'=>$this->integer()->comment('Изменил'),
        ]);

        $this->addIndex('cargo','client_type_id','client_type');
        $this->addIndex('cargo','company_id','company');
        $this->addIndex('cargo','package_type_id','package_type');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->removeIndex('cargo','client_type_id');
        $this->removeIndex('cargo','company_id');
        $this->removeIndex('cargo','package_type_id');

        $this->dropTable('{{%cargo}}');
    }
}
