<?php

use common\models\traits\MigrationTrait;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%tour_partner}}`.
 */
class m201225_051545_create_tour_partner_table extends Migration
{
    use MigrationTrait;
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%tour_partner}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->null()->comment('Название'),
            'created_at'=>$this->dateTime()->comment('Дата создания'),
            'modified_at'=>$this->dateTime()->comment('Дата изменения'),
            'created_by'=>$this->integer()->comment('Создал'),
            'modified_by'=>$this->integer()->comment('Изменил'),

        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%tour_partner}}');
    }
}
