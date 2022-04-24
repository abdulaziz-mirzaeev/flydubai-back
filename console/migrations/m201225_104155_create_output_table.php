<?php

use common\models\traits\MigrationTrait;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%output}}`.
 */
class m201225_104155_create_output_table extends Migration
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
         * о разрешении вывода с информацией о сумме и из каких средств. */

        $this->createTable('{{%output}}', [
            'id' => $this->primaryKey(),
            'price' => $this->float()->comment('Сумма'),
            'cashier_id' => $this->integer()->comment('Касса'),
            'type' => $this->tinyInteger(2)->comment('Тип'), // Билеты/Турпакеты/Карго/Визы
            'status_director' => $this->boolean()->comment('Статус директора'), // одобрение от директора
            'status' => $this->boolean()->comment('Статус вывода'), // статус от кассира
            'comment' => $this->text()->comment('Комментарий' ),
            'created_at'=>$this->dateTime()->comment('Дата создания'),
           // 'modified_at'=>$this->dateTime()->comment('Дата изменения'),
            'created_by'=>$this->integer()->comment('Создал'),
            //'modified_by'=>$this->integer()->comment('Изменил'),
        ]);
        $this->addIndex('output','cashier_id','cashier');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->removeIndex('output','cashier_id');

        $this->dropTable('{{%output}}');
    }
}
