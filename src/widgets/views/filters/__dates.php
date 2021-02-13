<?php
    use \yii\helpers\Html;

    $dates = ( isset( $settings[ 'dates' ] ) AND is_array( $settings[ 'dates' ])) ? $settings[ 'dates' ] : false;

    if ( $dates ) {
        $items = [];

        foreach( $dates as $date ) {
            if ( isset( $date[ 'label' ] )) {

                 $items[] = Html::tag( 'li',
                     Html::a( $date[ 'label' ], 'javascript:void(-1)', [
                         'data-field'    => isset( $date[ 'field' ] ) ? $date[ 'field' ] : '',
                         'data-type'     => isset( $date[ 'type' ] ) ? $date[ 'type' ] : '',
                         'data-chart-el' => $chart_id,
                         'data-chart-id' => '',
                     ])
                 );
            }
        }

        if ( count ( $items ) != 0 ) {
            echo Html::tag( 'ul', implode( "\n", $items ));
        }
    }
