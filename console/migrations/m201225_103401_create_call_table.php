<?php

use common\models\traits\MigrationTrait;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%call}}`.
 */
class m201225_103401_create_call_table extends Migration
{
    use MigrationTrait;
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        /*
         * Страница с журналом звонков между оператором и клиентом.
         * У каждого оператора будет свой внутренний номер и оператор устанавливает статус к каждому звонку:
            ·Сделал заказ
            ·Узнал информацию по билетам, визам, турпакетам, карго
            ·Узнал информацию о компании
            ·Поставил бронь и комментарий. Также формируется статистика по звонкам*/

        $this->createTable('{{%call}}', [
            'id' => $this->primaryKey(),
            'operator_id' => $this->integer()->comment('Оператор'),
            'client_id' => $this->integer()->comment('Клиент'),
            'status' => $this->tinyInteger(1)->comment('Статус' ),
            'comment' => $this->text()->comment('Комментарий' ),
            'created_at'=>$this->dateTime()->comment('Дата создания'),
            'modified_at'=>$this->dateTime()->comment('Дата изменения'),
            'created_by'=>$this->integer()->comment('Создал'),
            'modified_by'=>$this->integer()->comment('Изменил'),
        ]);
        $this->addIndex('call','operator_id','user');
        $this->addIndex('call','client_id','client');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->removeIndex('call','operator_id');
        $this->removeIndex('call','client_id');

        $this->dropTable('{{%call}}');
    }
}
