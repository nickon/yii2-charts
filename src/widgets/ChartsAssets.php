<?php

namespace nickon\yii2_charts\widgets;

use yii\web\AssetBundle;

class ChartsAssets extends AssetBundle
{
    public $sourcePath = '@media_manager/assets';

    public $js = [
        'js/charts.js',
    ];

    public $css = [
        'css/charts.css',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'yii\jui\JuiAsset'
    ];
}