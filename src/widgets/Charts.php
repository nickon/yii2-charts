<?php


namespace nickon\yii2_charts\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Json;
use yii\web\View;

class Charts extends Widget
{
    const DATES_TODAY      = 'today';
    const DATES_YESTERDAY  = 'yesteday';
    const DATES_WEEK       = 'week';
    const DATES_MONTH      = 'month';
    const DATES_YEAR       = 'year';

    const DATES_FROM_WEEK  = 'from_week';
    const DATES_FROM_MONTH = 'from_month';
    const DATES_FROM_YEAR  = 'from_year';

    const DATES_ALL_TIME   = 'all_time';
    const DATES_INTERVAL   = 'interval';

    const FILTER_TYPE_GROUP  = 'group';
    const FILTER_TYPE_SEARCH = 'search';

    public $id;
    public $settings = [];
    public $defaultSettings = [];

    public $baseUrl = '/charts/ajax/';

    public function init() {
        $this->id = uniqid();

        $this->defaultSettings = [
            'dataProvider' => null,
            'dates'     => [],
            'filters'   => [],

            'api' => [
                'renderUrl'            => Url::to([ $this->baseUrl . 'render' ]),
                'setFilterUrl'         => Url::to([ $this->baseUrl . 'set-filter' ]),
                'delFilterUrl'         => Url::to([ $this->baseUrl . 'del-filter' ]),
                'getAllowedFiltersUrl' => Url::to([ $this->baseUrl . 'get-allowed-filters' ]),
                'getAppliedUrl'        => Url::to([ $this->baseUrl . 'get-applied-filters' ]),
            ]
        ];

        Yii::setAlias('@charts', dirname( __DIR__ ));

        if (!empty($this->defaultSettings)) {
            $this->settings = ArrayHelper::merge( $this->defaultSettings, $this->settings );
        }

        parent::init();
    }

    public function run() {
        $this->view = $this->getView();
        ChartsAssets::register( $this->view );

        $chart_id = isset( $this->settings[ 'chart_id' ] ) ? $this->settings[ 'chart_id' ] : 0;

        $chart_model = \nickon\yii2_charts\models\Charts::getOne( $chart_id );
        if ( !$chart_model ) return false;

        $js = <<<JS
$(document).ready(function(){
    new chart( '{$this->id}', '{$chart_id}', {
        'api': {
            'renderUrl'     : '{$this->settings['api']['renderUrl']}',
            'setFilterUrl'  : '{$this->settings['api']['setFilterUrl']}',
            'delFilterUrl'  : '{$this->settings['api']['delFilterUrl']}',
            'getAllowedFiltersUrl' : '{$this->settings['api']['getAllowedFiltersUrl']}',
            'getAppliedUrl'        : '{$this->settings['api']['getAppliedUrl']}'
        }
    });
}); 
JS;
        $this->view->registerJs( $js );

        return $this->render( 'chart', [
            'id'       => $this->id,
            'chart_id' => $chart_id,
            'settings' => $this->settings,
        ]);
    }
}