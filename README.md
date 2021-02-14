# Yii2 FushionCharts manager

## Instalation
```bash
composer require nickon/yii2-charts
```

## Usage
```php
echo \nickon\yii2_charts\widgets\Charts::widget([
    'settings' => [
        'chart_id' => 1,
        'dates' => [
            [ 'label' => 'Сегодня',               'type' => Charts::DATES_TODAY,      'field' => 'date' ],
            [ 'label' => 'Вчера',                 'type' => Charts::DATES_YESTERDAY,  'field' => 'date' ],
            [ 'label' => 'Неделя',                'type' => Charts::DATES_WEEK,       'field' => 'date' ],
            [ 'label' => 'С начала недели',       'type' => Charts::DATES_FROM_WEEK,  'field' => 'date' ],
            [ 'label' => 'Последний месяц',       'type' => Charts::DATES_MONTH,      'field' => 'date' ],
            [ 'label' => 'С начала месяца',       'type' => Charts::DATES_FROM_MONTH, 'field' => 'date' ],
            [ 'label' => 'За последний год',      'type' => Charts::DATES_YEAR,       'field' => 'date' ],
            [ 'label' => 'С начала года',         'type' => Charts::DATES_FROM_YEAR,  'field' => 'date' ],
            [ 'label' => 'За всё время',          'type' => Charts::DATES_ALL_TIME,   'field' => 'date' ],
            [ 'label' => 'Произвольный интервал', 'type' => Charts::DATES_INTERVAL,   'field' => 'date' ],
        ]
    ]
]);
```

