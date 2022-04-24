<?php

use common\models\traits\MigrationTrait;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%process}}`.
 */
class m201225_105531_create_process_table extends Migration
{
    use MigrationTrait;
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        /*
        * Страница для списка выведенных средств из прибыли из кассы.
        *  Осуществляется новый вывод средств нажав на кнопку «Добавить» и заполнив столбцы:
        *  Касса;
        * Сумма;
        *  Категория (с каких средств производится перенос: Билеты/Турпакеты/Карго/Визы);
        * Комментарий.
        *  После создания нового вывода кассиром, отправляется уведомление директору
        * о разрешении вывода с информацией о сумме и из каких средств.

        * Страница для списка приходов средств в кассу.
        * Создается новый приход нажав на кнопку «Добавить» и заполнив поля:
        * Касса;
        * Сумма;
        * Категория (с каких средств производится перенос: Билеты/Турпакеты/Карго/Визы);
        * Комментарий.
         *
         *
         * */

        $this->createTable('{{%process}}', [
            'id' => $this->primaryKey(),
            'price' => $this->float()->notNull()->comment('Сумма'),
            'cashier_from' => $this->integer()->null()->comment('Касса откуда'),
            'cashier_id' => $this->integer()->notNull()->comment('Касса куда'),
            //'order_id' => $this->integer()->notNull()->comment('Заказ'),
            'category' => $this->tinyInteger(1)->notNull()->comment('Категория'), // Билеты/Турпакеты/Карго/Визы
            'type' => $this->tinyInteger(2)->notNull()->comment('Тип'), // приход/расход/перенос
            'status_director' => $this->boolean()->null()->defaultValue(0)->comment('Статус директора'), // одобрение от директора
            'status' => $this->boolean()->defaultValue(0)->comment('Статус вывода'), // статус от кассира
            'comment' => $this->text()->null()->comment('Комментарий' ),
            'created_at'=>$this->dateTime()->comment('Дата создания'),
            //'modified_at'=>$this->dateTime()->comment('Дата изменения'),
            'created_by'=>$this->integer()->comment('Создал'),
            //'modified_by'=>$this->integer()->comment('Изменил'),
        ]);
        $this->addIndex('process','cashier_id','cashier');
        //$this->addIndex('process','order_id','order');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->removeIndex('process','cashier_id');
       // $this->addIndex('process','order_id');
        $this->dropTable('{{%process}}');
    }
}
