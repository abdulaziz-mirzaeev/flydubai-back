<?php

use common\models\traits\MigrationTrait;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%cashier}}`.
 */
class m201225_095131_create_cashier_table extends Migration
{
    use MigrationTrait;
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%cashier}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(32)->notNull()->comment('Название'),
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
        $this->dropTable('{{%cashier}}');
    }
}
