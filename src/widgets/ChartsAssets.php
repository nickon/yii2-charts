<?php

namespace nickon\yii2_charts\widgets;

use yii;
use yii\web\AssetBundle;
use yii\web\View;

class ChartsAssets extends AssetBundle
{
    public $sourcePath = '@charts/assets';

    public $js = [
        'js/daterangepicker/moment.min.js',
        'js/daterangepicker/daterangepicker.js',
        'js/charts.js',
    ];

    public $css = [
        'css/charts.css',
    ];

    public $depends = [];

    public function __construct($config = [])
    {
        $this->css = [
            'js/daterangepicker/daterangepicker.css?v=' .time(),
            'css/charts.css?v='.time()
        ];

        parent::__construct($config);
    }
}