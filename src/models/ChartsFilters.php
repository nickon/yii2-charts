<?php


namespace nickon\yii2_charts\models;


use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class ChartsFilters extends ActiveRecord
{
    public static function tableName() {
        return 'charts_filters';
    }

    public static function getDataSourceList( $table_name, $index, $value ) {

        $list = ( new \yii\db\Query())
            ->from( $table_name )
            ->select([ $index, $value ])
            ->all();

        return $list;
    }

    public static function getOne( $filter_id ) {
        return static::find()->where( 'id = :id', [ ':id' => $filter_id ])->one();
    }

}