<?php


namespace nickon\yii2_charts\models;

use yii\db\ActiveRecord;

class Charts extends ActiveRecord
{
        public static function tableName() {
            return 'charts';
        }

        public static function getOne( $id ) {
            return static::find()->where( 'id = :id',  [ ':id' => $id ])->one();
        }
}