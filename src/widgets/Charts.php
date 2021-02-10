<?php


namespace nickon\yii2_charts\widgets;

use Yii;
use yii\base\Widget;

class Charts extends Widget
{
    const DATES_TODAY      = 'today';
    const DATES_YESTERDAY  = 'yesteday';
    const DATES_WEEK       = 'week';
    const DATES_FROM_WEEK  = 'from_week';
    const DATES_MONTH      = 'month';
    const DATES_FROM_MONTH = 'from_month';
    const DATES_ALL_TIME   = 'all_time';
    const DATES_CALENDAR   = 'calendar';

    const FILTER_TYPE_GROUP  = 'group';
    const FILTER_TYPE_SEARCH = 'search';

    public $settings = [];
    public $defaultSettings = [];

    public function init() {

        $this->defaultSettings = [
            'dataProvider' => null,
            'dates' => [],
            'filters' => [],
        ];

        Yii::setAlias('@charts', __DIR__ );

        if (!empty($this->defaultSettings)) {
            $this->settings = ArrayHelper::merge( $this->defaultSettings, $this->settings );
        }

        parent::init();
    }

    public function run() {
        $this->view = $this->getView();
        ChartsAssets::register( $this->view );

        return $this->render( 'chart', [

        ]);
    }
}