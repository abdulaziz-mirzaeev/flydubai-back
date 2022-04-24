<?php

use common\models\traits\MigrationTrait;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%visa_partner}}`.
 */
class m201225_050458_create_visa_partner_table extends Migration
{
    use MigrationTrait;
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%visa_partner}}', [
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
        $this->dropTable('{{%visa_partner}}');
    }
}
