<?php

namespace common\models\traits;

trait MigrationTrait
{

    public function addIndex($table,$field,$ref_table,$ref_column='id',$delete_type='CASCADE'){
        // создаем индекс для колонки $field
        $this->createIndex(
            "idx-$table-$field",
            $table,
            $field
        );

        // добавляем внешний ключ для таблицы $table
        $this->addForeignKey(
            "fk-$table-$field",
            $table,
            $field,
            $ref_table,
            $ref_column,
            $delete_type
        );

        return true;

    }

    public function removeIndex($table,$field){

        // удаляем внешний ключ
        $this->dropForeignKey(
           "fk-$table-$field",
            $table

        );

        // удаляем индекс
        $this->dropIndex(
            "idx-$table-$field",
            $table
        );

        return true;

    }




    }