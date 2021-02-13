<?php


namespace nickon\yii2_charts\controllers;

use nickon\yii2_charts\models\ChartsFilters;
use nickon\yii2_charts\widgets\Charts;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use Yii;
use yii\web\Response;

class AjaxController extends Controller
{
        public function behaviors() {
            $this->enableCsrfValidation = false;
            $behaviors = parent::behaviors();
            $behaviors['contentNegotiator'] = [
                'class' => 'yii\filters\ContentNegotiator',
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ]
            ];

            return $behaviors;
        }

        /**
        * Apply filter
        * @return array
        */

        public function actionSetFilter() {
            $chart_id = Yii::$app->request->post( 'chart_id', 0 );
            if ( $chart_id == 0 ) return [ 'error' => true, 'message' => 'Chart id error' ];

            $filter_id = Yii::$app->request->post( 'filter_id', 0 );
            if ( $filter_id == 0 ) return [ 'error' => true, 'message' => 'Filter id error' ];

            $fileter_model = ChartsFilters::getOne( $filter_id );
            if ( !$fileter_model ) return [ 'error' => true, 'message' => 'Filter model error' ];

            $chart_model = \nickon\yii2_charts\models\Charts::getOne( $chart_id );
            if ( !$chart_model ) return [ 'error' => true, 'message' => 'Chart model error' ];

            $filters = ( isset( $chart_model->filters ) AND $chart_model->filters != '' ) ? json_decode( $chart_model->filters, true ) : [];

            $values  = Yii::$app->request->post( 'values', []);

            if ( ( is_array( $values ) AND $values !== [] ) OR ( is_string( $values ) AND $values != '' )) {
                $filters[ $filter_id ] = $values;
            }

            $chart_model->filters = json_encode( $filters, JSON_UNESCAPED_UNICODE );
            $chart_model->save(false);

            return [
                'error' => false,
                'message' => 'Success',
                'values' => $values
            ];
        }

        /**
        *  Delete applied filter
        */

        public function actionDelFilter() {
            $chart_id = Yii::$app->request->post( 'chart_id', 0 );
            if ( $chart_id == 0 ) return [ 'error' => true, 'message' => 'Chart id error' ];

            $filter_id = Yii::$app->request->post( 'filter_id', 0 );
            if ( $filter_id == 0 ) return [ 'error' => true, 'message' => 'Filter id error' ];

            $chart_model = \nickon\yii2_charts\models\Charts::getOne( $chart_id );
            if ( !$chart_model ) return [ 'error' => true, 'message' => 'Chart model error' ];

            $filters = ( isset( $chart_model->filters ) AND $chart_model->filters != '' ) ? json_decode( $chart_model->filters, true ) : [];

            if ( is_array( $filters ) AND isset( $filters[ $filter_id ] )) {
                 unset( $filters[ $filter_id ]);
            }

            $chart_model->filters = json_encode( $filters, JSON_UNESCAPED_UNICODE );
            $chart_model->save(false);

            return [ 'error' => false, 'message' => 'Success' ];
        }

        /**
         *  Get applied filters for chart
        *  @return array
        */

        public function actionGetAppliedFilters () {
            $chart_id = Yii::$app->request->post( 'chart_id', 0 );
            if ( $chart_id == 0 ) return [ 'error' => true, 'message' => 'Chart id error' ];

            $model = \nickon\yii2_charts\models\Charts::findOne( $chart_id );
            if ( !$model ) return [ 'error' => true, 'message' => 'Chart model error' ];

            $allowed_filters = ChartsFilters::find()->all();
            $allowed_filters = ArrayHelper::index( $allowed_filters, 'id' );

            $applied_filters = ( isset( $model->filters ) AND $model->filters != '' ) ? json_decode( $model->filters, JSON_UNESCAPED_UNICODE ) : [];

            $_filters = [];

            if ( is_array( $applied_filters )) {
                 foreach( $applied_filters as $filter_id => $values ) {

                        $filter = isset( $allowed_filters[ $filter_id ]) ? $allowed_filters[ $filter_id ] : false;
                        if ( !$filter ) continue;

                        $value = '';
                        if ( $filter->field_type == 'select' ) {

                            $data_list = [];

                            $data_source    = ( isset( $filter->data_source ) AND $filter->data_source != '' )     ? json_decode( $filter->data_source, true ) : false;
                            $default_values = ( isset( $filter->default_value ) AND $filter->default_value != '' ) ? json_decode( $filter->default_value, true ) : false;

                            if ( $data_source AND $data_source !== false AND $data_source !== '' ) {
                                if ( isset( $data_source['table'] ) AND $data_source[ 'table' ] != '' AND \Yii::$app->db->getTableSchema( $data_source[ 'table' ], true ) !== null ) {
                                    if ( isset( $data_source[ 'index' ], $data_source[ 'value' ] )) {
                                        $data_list = ChartsFilters::getDataSourceList( $data_source[ 'table' ], $data_source[ 'index' ], $data_source[ 'value' ]);
                                    }
                                }

                                if ( $default_values AND $default_values !== false AND count( $data_list ) == 0 ) $data_list = $default_values;
                            } elseif ( $default_values AND $default_values !== false ) {
                                $data_list = $default_values;
                            }

                            $value = [];

                            if ( is_array( $data_list )) {
                                 foreach( $data_list as $item )
                                     if ( isset( $item[ 'name' ] ) AND in_array( $item['id'], $values )) $value[] = $item[ 'name' ];
                            }

                            $value = implode( ', ', $value );

                        } elseif ( $filter->field_type == 'text' ) {
                            $value = isset( $applied_filters[ $filter_id ] ) ? $applied_filters[ $filter_id ] : '';
                        }

                        $_filters[] = [
                            'id'   => $filter_id,
                            'title' => $filter->title,
                            'value' => $value,
                        ];
                 }
            }

            return [ 'filters' => $_filters ];
        }

        /**
         * Get allowed filters for chart ...
        * @return array
        */

        public function actionGetAllowedFilters() {
            $chart_id = Yii::$app->request->post( 'chart_id', 0 );
            if ( $chart_id == 0 ) return [ 'error' => true, 'message' => 'Chart id error' ];

            $filters = ChartsFilters::find()->where( 'chart_id = :chart_id', [ ':chart_id' => $chart_id ])->orderBy( 'posi ASC' )->all();

            $data = [];
            foreach( $filters as $filter ) {

                $data_list = [];

                switch( $filter->field_type ) {
                    case 'select':

                        $data_source    = ( isset( $filter->data_source ) AND $filter->data_source != '' ) ? json_decode( $filter->data_source, true ) : false;
                        $default_values = ( isset( $filter->default_value ) AND $filter->default_value != '' ) ? json_decode( $filter->default_value, true ) : false;

                        if ( $data_source AND $data_source !== false AND $data_source !== '' ) {
                             if ( isset( $data_source['table'] ) AND $data_source[ 'table' ] != '' AND \Yii::$app->db->getTableSchema( $data_source[ 'table' ], true ) !== null ) {
                                if ( isset( $data_source[ 'index' ], $data_source[ 'value' ] )) {
                                    $data_list = ChartsFilters::getDataSourceList( $data_source[ 'table' ], $data_source[ 'index' ], $data_source[ 'value' ]);
                                }
                            }

                            if ( $default_values AND $default_values !== false AND count( $data_list ) == 0 ) $data_list = $default_values;
                        } elseif ( $default_values AND $default_values !== false ) {
                            $data_list = $default_values;
                        }
                        break;

                    case 'bool' :
                        break;
                }


                $data[] = [
                    'id'         => $filter->id,
                    'title'      => $filter->title,
                    'field_type' => $filter->field_type,
                    'data_list'  => $data_list,
                ];
            }

            return [ 'filters' => $data ];
        }

       /**
        * Render chart
        * @return array
        */

        public function actionRender() {

                $chart_id    = Yii::$app->request->post( 'chart_id',    '' );
                if ( $chart_id == 0 ) return [ 'error' => true, 'message' => 'Chart id error' ];

                $chart_model = \nickon\yii2_charts\models\Charts::getOne( $chart_id );
                if ( !$chart_model ) return [ 'error' => true, 'message' => 'Chart model error' ];

                if( \Yii::$app->db->getTableSchema( $chart_model->data_source, true ) === null ) {
                     return [ 'error' => true, 'message' => 'data_source invalid. table "' . $table_name . '" is not exists' ];
                }

                $query = ( new \yii\db\Query())->from( $chart_model->data_source );

                /*** Filter by date ***/
                $filters = Yii::$app->request->post( 'filters', []);
                $where   = [];

                if ( isset( $filters[ 'date' ][ 'interval' ], $filters[ 'date' ][ 'field' ] )) {

                     $field = $filters[ 'date' ][ 'field' ];

                     switch ( $filters[ 'date' ][ 'interval' ] ) {
                         case Charts::DATES_TODAY:
                             $query->andWhere( 'DATE(' . $field . ') = CURDATE()' );
                             break;

                         case Charts::DATES_YESTERDAY :
                             $query->andWhere( 'DATE(' . $field . ') = DATE(NOW() - INTERVAL 1 DAY )' );
                             break;

                         case Charts::DATES_WEEK :
                             $query->andWhere($field . ' BETWEEN DATE(' . $field . ' - INTERVAL 7 DAY) AND DATE(' . $field . ')' );
                             break;

                         case Charts::DATES_FROM_WEEK:

                             $query->andWhere('YEARWEEK(' . $field . ', 1) = YEARWEEK(CURDATE(), 1 )' );
                             break;

                         case Charts::DATES_MONTH:
                             $query->andWhere( $field . ' BETWEEN DATE(' . $field . ' - INTERVAL 30 DAY) AND DATE(' . $field . ')' );
                             break;

                         case Charts::DATES_FROM_MONTH :
                             $query->andWhere('MONTH(' . $field . ') = MONTH(CURDATE())' );
                             $query->andWhere('YEAR(' . $field . ') = YEAR(CURDATE())' );
                             break;

                         case Charts::DATES_YEAR:
                             $query->andWhere( $field . ' BETWEEN DATE(NOW() - INTERVAL 1 YEAR) AND DATE(NOW())' );
                             break;

                         case Charts::DATES_FROM_YEAR:
                             $query->andWhere('YEAR(' . $field . ') = YEAR(CURDATE())' );
                             break;

                         case Charts::DATES_ALL_TIME:
                             break;

                         case Charts::DATES_INTERVAL:

                             $start_date = isset( $filters[ 'date' ][ 'start_date' ] ) ? $filters[ 'date' ][ 'start_date' ] : false;
                             $end_date   = isset( $filters[ 'date' ][ 'end_date' ]) ? $filters[ 'date' ][ 'end_date' ] : false;

                             if ( $start_date AND $end_date ) {
                                  $start_date = date( 'Y-m-d', strtotime( $filters[ 'date' ][ 'start_date' ]));
                                  $end_date   = date( 'Y-m-d', strtotime( $filters[ 'date' ][ 'end_date' ]));
                                  $query->andWhere([ 'between', 'DATE(' . $field . ')', $start_date, $end_date ]);
                             }

                             break;
                     }
                }

                /*** Add filters ***/
                $applied_filters = ( isset( $chart_model->filters ) AND $chart_model->filters != '' ) ? json_decode( $chart_model->filters, true ) : [];
                $applied_ids     = is_array( $applied_filters ) ? array_keys( $applied_filters ) : [];

                $filters = ChartsFilters::find()->where( 'chart_id = :chart_id', [ ':chart_id' => $chart_id ])->all();
                $filters = ArrayHelper::index( $filters, 'id' );

                foreach( $filters as $filter_id => $filter ) {
                    if ( in_array( $filter_id, $applied_ids )) {

                         if ( $filter->field_type == 'select' ) {
                             $_values = isset( $applied_filters[ $filter_id ] ) ? $applied_filters[ $filter_id ] : [];

                             if ( is_array( $_values ) AND count( $_values ) != 0 ) {
                                 $query->andWhere([ 'IN', $filter->field, $_values ]);
                             }

                         } elseif ( $filter->field_type == 'text' ) {
                              $_value = isset( $applied_filters[ $filter_id ]) ? $applied_filters[ $filter_id ] : '';

                              if ( $_value != '' ) {
                                  $query->andWhere([ 'LIKE', $filter->field, '%' . $_value . '%', false ]);
                              }
                         }
                    }
                }

                /*** Group by field ***/
                if ( $chart_model->group_field != '' ) {
                     $query->andWhere([ '<>', $chart_model->group_field, '' ]);

                     $query->groupBy( $chart_model->group_field );
                     $query->select([ $chart_model->group_field, 'COUNT(' . $chart_model->group_field . ') as count' ]);
                }

                $res = $query->all();

                /*
                print_R( $res );
                echo $query->createCommand()->getRawSql();
                die();
                */

                /*** Parse results and compile chart json ***/
                $data = [];

                if ( is_array( $res )) {

                     $total = 0;
                     foreach( $res as $item ) {
                         if ( isset( $item[ 'count' ])) $total += $item[ 'count' ];
                     }

                     if ( $total != 0 ) {

                         if ( $chart_model->show_total_column == 1 ) {
                             $data[] = [
                                 'label'        => 'Всего',
                                 'value'        => $total,
                                 'displayvalue' => $this->num( $total, $total, $chart_model->show_column_percents == 1 ),
                                 'showToolTip'  => 1,
                             ];
                         }

                         $chart_labels = ( isset( $chart_model->labels ) AND $chart_model->labels != '' ) ?
                             json_decode( $chart_model->labels, true ) : [];

                         foreach( $res as $item ) {

                             $_field = $item[ $chart_model->group_field ];
                             $label = isset( $chart_labels[ $_field ]) ? $chart_labels[ $_field ] : $_field;

                             $value = $item[ 'count' ];

                             $data[] = [
                                 'label' => $label,
                                 'value' => $value,
                                 'displayvalue' => $this->num( $value, $total, $chart_model->show_column_percents == 1 ),
                                 'showToolTip'  => 1,
                             ];
                         }
                     }
                }

                return [
                    'chart_id' => $chart_id,
                    'data'     => $data,
                ];
        }

        private function num( $value, $total = 0, $show_percent = true ) {
            $percent = ( $value != 0  AND $total != 0 ) ? round( ( $value / $total ) * 100, 2 ) : 0;

            $label = number_format( $value, 0, ',', ' ');
            if ( $show_percent AND $percent != 0 ) $label .= ' {br} ' . $percent . '%';

            return $label;
        }

}