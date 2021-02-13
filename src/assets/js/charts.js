class chart {
    constructor( id, chart_id, config ) {
        console.log( '[chart-' + chart_id + '] init -> ' + id );

        this.id       = id;
        this.chart_id = chart_id;
        this.config   = config;

        this.chart_el = '.chart-' + id;
        this.modal_el = '.modal-add-filter-' + id;

        this.filter = {};
        this.allowed_filters = {};
        this.add_filter = {};

        this.initFilters();
        this.initModal();
        this.getAllowedFilters(null);
        this.getAppliedFilters();

        this.render();
    }

    initFilters() {
        console.log( '[chart-' + this.chart_id + '] initFilters -> ' + this.id );

        var obj     = this;
        this.filter = {};
        var modal   = $(this.modal_el);

        /** Filters - click **/
        $( this.chart_el + ' .dates-filters li a').click(function() {

            $(obj.chart_el + ' .dates-filters li a').removeClass('selected');
            $(this).addClass('selected');

            var interval = $(this).attr( 'data-type' );
            var field    = $(this).attr( 'data-field' );

            console.log( '[charts] Filter - click -- ' + obj.chart_id + ' -> ' + obj.id + ' [interval: ' + interval + ', field: ' + field + ']');

            if ( interval == 'interval' )
                 $(obj.chart_el + ' .dates-interval').fadeIn();
            else $(obj.chart_el + ' .dates-interval').fadeOut();

            var start_date = $(obj.chart_el + ' .dates-interval input[name=start_date]', $(obj.chart_el)).val();
            var end_date   = $(obj.chart_el + ' .dates-interval input[name=end_date]',   $(obj.chart_el)).val();

            obj.filter = {
                date: {
                    interval    : interval,
                    field       : field,
                    start_date  : start_date,
                    end_date    : end_date
                }
            };

            if ( interval != 'interval' ) obj.render();
        });

        /** Set daterangepicker for dates filter **/
        $( this.chart_el + ' .dates-interval input[name="range"]').daterangepicker({
                opens: 'left',
                ranges: {
                    'Сегодня': [moment(), moment()],
                    'Вчера': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Последние 7 дней': [moment().subtract(6, 'days'), moment()],
                    'Последние 30 дней': [moment().subtract(29, 'days'), moment()],
                    'Текущий месяц': [moment().startOf('month'), moment().endOf('month')],
                    'Прошлый месяц': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                "buttonClasses": "btn btn-sm",
                "applyButtonClasses": "btn btn-sm",
                "alwaysShowCalendars": true,
                "showDropdowns": true,
                "locale": {
                    "format": "MM/DD/YYYY",
                    "separator": " - ",
                    "applyLabel": "Применить",
                    "cancelLabel": "Отмена",
                    "fromLabel": "С",
                    "toLabel": "по",
                    "customRangeLabel": "Произвольная дата",
                    "weekLabel": "W",
                    "daysOfWeek": [ "Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб" ],
                    "monthNames": [ "Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь" ],
                    "firstDay": 1
                }
            }, function ( start, end, label ) {
                var start_date = start.format('YYYY-MM-DD');
                var end_date   = end.format('YYYY-MM-DD');
                var item = $(obj.chart_el + ' .dates-filters li a[data-type="interval"]');

                obj.filter.date = {
                    interval    : 'interval',
                    field       : $(item).attr( 'data-field' ),
                    start_date  : start_date,
                    end_date    : end_date
                };

                obj.render();
            });

        /** Delete filter from chart - onclick **/
        $( this.chart_el ).on('click', '.applied-filters a.del-filter', function() {
            if( confirm( 'Вы действительно хотите удалить выбранный фильтр?' )) {
                var params = {
                    filter_id: $(this).attr( 'data-filter-id' ),
                    chart_id: obj.chart_id
                };

                $.post( obj.config['api']['delFilterUrl'], params, function( _data ) {
                    obj.getAppliedFilters(null);
                    obj.render();
                });
            }
        })

        /** Update chart button - onclick **/
        $( this.chart_el ).on('click', '.update-filter-button', function(){
             obj.render();
        })
    }

    initModal() {
        console.log( '[chart-' + this.chart_id + '] initModal -> ' + this.id );

        var obj   = this;
        var modal = $(this.modal_el);

        /** Click and show modal with filters **/

        $( this.chart_el + ' .add-filter-button').click(function(){
            console.log( '[chart-' + obj.chart_id + '] addFilterButton - show modal -> ' + obj.id + ' modal_id: ' + obj.modal_el );

            obj.add_filter = {};
            $('select.filter-values', modal).html('');
            $('select, input').val('');

            obj.getAllowedFilters(function( data ) {
                $('.filters-list', modal).html('');

                $.each( data.filters, function( index, filter ){
                    $('.filters-list', modal)
                        .append( '<li><a href="javascript:void(-1)" ' +
                            'data-filter-id="' + filter.id + '" ' +
                            'data-chart-id="' + obj.chart_id + '" ' +
                            'data-field="' + filter.field_type + '">' + filter.title + '</a></li>' );
                });

                $('.filters-list', modal).on( 'click', 'li a', function(){
                    $('.filters-list li a', modal).removeClass( 'sel' );
                    $(this).addClass( 'sel' );

                    var filter_id   = $(this).attr('data-filter-id');
                    var chart_id    = $(this).attr('data-chart-id');

                    $.each( obj.allowed_filters, function( index, filter ) {
                        if ( filter_id == filter.id ) { obj.add_filter = filter; return; }
                    });

                    $('select.filter-values', modal).html('');
                    $('.sel-values',  modal).hide();
                    $('.text-values', modal).hide();

                    if ( obj.add_filter.field_type == 'select' ) {
                        $('.sel-values', modal).fadeIn();
                        $.each( obj.add_filter.data_list, function( index, item ) {
                            $('select.filter-values', modal).append('<option value="' + item. id + '">' + item.name + '</option>' );
                        });
                    } else if ( obj.add_filter.field_type == 'text' ) {
                        $('.text-values input', modal).val('');
                        $('.text-values', modal).fadeIn();
                    }

                });

                modal.modal('show');
            });
        });

        /** Init select2 for values **/
        $('select.filter-values', modal).select2({
            allowClear: false,
            multiple: true,
            maximumSelectionSize: 1,
            placeholder: "Выберите значение",
            closeOnSelect: false
        });

        /** Apply filter - button in modal **/
        $('.add-filter-modal-button', modal).click(function(){

            var params = {
                filter_id: obj.add_filter.id,
                chart_id : obj.chart_id
            };

            if ( obj.add_filter.field_type == 'select' ) {
                params.values = $('select.filter-values', $(modal)).val();
            } else if ( obj.add_filter.field_type == 'text' ) {
                params.values = $('.text-values input', $(modal)).val()
            }

            if ( typeof obj.add_filter.field_type == 'undefined' ) {
                alert( 'Необходимо выбрать фильтр для добавления в график' );
                return;
            }

            if ( typeof params.values == 'undefined' || params.values.length == 0 ) {
                alert( 'Необходимо указать значения для выбранного фильтра' );
                return;
            }

            $.post( obj.config['api']['setFilterUrl'], params, function (data){
                if ( data.error ) {
                    alert( data.message );
                    return;
                }
                obj.getAppliedFilters();
                modal.modal('hide');
            });
        });
    }

    getAllowedFilters( callback ) {
        console.log( '[chart-' + this.chart_id + '] getAllowedFilters -> ' + this.id );

        var obj = this;
        var params = {
            chart_id: obj.chart_id
        };

        $.post( obj.config['api'][ 'getAllowedFiltersUrl' ], params, function( data ) {
            if ( data.filters ) {
                obj.allowed_filters = data.filters;
                if ( typeof callback === 'function') callback( data );
            }
        });

    }

    getAppliedFilters () {
        console.log( '[chart-' + this.chart_id + '] getAppliedFilters -> ' + this.id );

        var obj = this;
        var params = { chart_id: obj.chart_id };

        $(this.chart_el + ' .applied-filters' ).html('');

        $.post( obj.config['api']['getAppliedUrl'], params, function( _data ) {
            $.each( _data.filters, function( index, item ) {
                $( obj.chart_el + ' .applied-filters' )
                    .append( '<span class="badge badge-default">' + item.title + ' = "' + item.value + '"' +
                    ' &nbsp; <a href="javascript:void(-1)" class="del-filter" data-filter-id="' + item.id + '" style="font-weight: bold;">[X]</a>' +
                    '</span> ' );
            });

            obj.render();
        });
    }

    render() {
        console.log( '[chart-' + this.chart_id + '] render -> ' + this.id + ' [!!!]' );

        var obj = this;
        var params = { chart_id : obj.chart_id, filters : this.filter };

        $('#' + this.id).hide();

        $.post( this.config[ 'api' ][ 'renderUrl' ], params, function( _data ) {
            $('#' + obj.id ).fadeIn();

            if ( _data.error ) {
                 alert( _data.message );
            } else {
                FusionCharts.ready(function () {
                    new FusionCharts({
                        type: "column2d",
                        renderAt: obj.id,
                        width : "100%",
                        height: "350",
                        dataFormat: "json",
                        dataSource: {
                            chart: {
                                "baseFont": "Open Sans, sans-serif",
                                "labelFontColor": "0075c2",
                                "showYAxisValues":"0",
                                "showValues": "1",
                                "valuePadding":"-2",
                                "canvasTopPadding": "30",
                                "placeValuesInside": "0",
                                "valueFontSize": "14",
                                "baseFontSize": "11",
                                "showToolTip": "1",
                                "labelDisplay": "stagger",
                                "theme": "fusion"
                            },
                            data: _data.data
                        }
                    }).render();
                });
            }
        });
    }
}