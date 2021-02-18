<?php

use common\models\traits\MigrationTrait;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%client}}`.
 */
class m201225_051000_create_client_table extends Migration
{

    use MigrationTrait;

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%client}}', [
            'id' => $this->primaryKey(),
            'first_name' => $this->string(255)->null()->comment('Имя'),
            'second_name' => $this->string(255)->null()->comment('Фамилия'),
            'last_name' => $this->string(255)->null()->comment('Отчество'),
            'user_id' => $this->integer()->comment('Клиент'), // аккаунт клиента
            'client_type_id' => $this->integer()->comment('Тип клиента'),
            'client_number' => $this->string(32)->comment('Номер клиента'),
            'passport_serial' => $this->string(16)->comment('Серия паспорта'),
            'passport_number' => $this->string(32)->comment('Номер паспорта'),
            'created_at'=>$this->dateTime()->comment('Дата создания'),
            'modified_at'=>$this->dateTime()->comment('Дата изменения'),
            'created_by'=>$this->integer()->comment('Создал'),
            'modified_by'=>$this->integer()->comment('Изменил'),
        ]);

        $this->addIndex('client','client_type_id','client_type');
        $this->addIndex('client','user_id','user');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->removeIndex('client','client_type_id');
        $this->removeIndex('client','user_id');

        $this->dropTable('{{%client}}');
    }
}
