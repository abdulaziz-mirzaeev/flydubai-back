<?php

use common\models\traits\MigrationTrait;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%return}}`.
 */
class m201225_102332_create_return_table extends Migration
{
    use MigrationTrait;
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        /*
         * ·Возвраты от клиентов будут создаваться на отдельной странице оператором,
         *  где будет выбираться номер (ID) заявки и сумма к возврату.
            Возврат денежных средств вычисляется с той же кассы куда была выполнена оплата.
        */

        $this->createTable('{{%return}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->comment('Заявка'),
            'price' => $this->float()->comment('Сумма'),
            'cashier_id' =>$this->integer()->comment('Касса'),
            'comment' => $this->text()->comment('Комментарий'),
            'created_at'=>$this->dateTime()->comment('Дата создания'),
           //'modified_at'=>$this->dateTime()->comment('Дата изменения'),
            'created_by'=>$this->integer()->comment('Создал'),
            //'modified_by'=>$this->integer()->comment('Изменил'),
        ]);

        $this->addIndex('return','order_id','order');
        $this->addIndex('return','cashier_id','cashier');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->removeIndex('return','order_id');
        $this->removeIndex('return','cashier_id');

        $this->dropTable('{{%return}}');
    }
}
