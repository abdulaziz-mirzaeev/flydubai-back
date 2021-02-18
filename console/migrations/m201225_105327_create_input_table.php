<?php

use common\models\traits\MigrationTrait;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%input}}`.
 */
class m201225_105327_create_input_table extends Migration
{
    use MigrationTrait;
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        /*
         * Страница для списка приходов средств в кассу.
         * Создается новый приход нажав на кнопку «Добавить» и заполнив поля:
         * Касса;
         * Сумма;
         * Категория (с каких средств производится перенос: Билеты/Турпакеты/Карго/Визы);
         * Комментарий. */

        $this->createTable('{{%input}}', [
            'id' => $this->primaryKey(),
            'price' => $this->float()->comment('Сумма'),
            'cashier_id' => $this->integer()->comment('Касса'),
            'type' => $this->tinyInteger(1)->comment('Тип'), // Билеты/Турпакеты/Карго/Визы
            'status_director' => $this->boolean()->defaultValue(0)->comment('Статус директора'), // одобрение от директора
            'status' => $this->boolean()->defaultValue(0)->comment('Статус вывода'), // статус от кассира
            'comment' => $this->text()->comment('Комментарий' ),
            'created_at'=>$this->dateTime()->comment('Дата создания'),
            //'modified_at'=>$this->dateTime()->comment('Дата изменения'),
            'created_by'=>$this->integer()->comment('Создал'),
            //'modified_by'=>$this->integer()->comment('Изменил'),
        ]);
        $this->addIndex('input','cashier_id','cashier');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->removeIndex('input','cashier_id');

        $this->dropTable('{{%input}}');
    }
}
