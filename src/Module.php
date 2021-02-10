<?php


namespace nickon\yii2_charts;


class Module extends \yii\base\Module
{
    public $controllerNamespace = 'nickon\yii2_charts\controllers';

    public function init() {
        parent::init();

        Yii::setAlias('@charts', __DIR__ );
    }
}