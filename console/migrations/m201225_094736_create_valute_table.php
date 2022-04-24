<?php

use common\models\traits\MigrationTrait;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%valute}}`.
 */
class m201225_094736_create_valute_table extends Migration
{
    use MigrationTrait;
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%valute}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(4)->notNull()->comment('Название'),
            'description' => $this->string(32)->null()->comment('Описание'),
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
        $this->dropTable('{{%valute}}');
    }
}
