<?php
return [
//    'Меню',
    ['label' => 'Заказы', 'icon' => 'fas fa-book', 'url' => '/orders'],
    ['label' => 'Продукция', 'icon' => 'fas fa-boxes', 'url' => '/products'],
    ['label' => 'Регионы', 'icon' => 'fas fa-map-marker-alt', 'url' => '/regions'],
    ['label' => 'Пользователи', 'icon' => 'fas fa-users', 'url' => '/users'],
    ['label' => 'Отчеты', 'icon' => 'fas fa-users', 'items' => [
        ['label' => 'Отчёт по продажам', 'icon' => 'fas fa-chart-line', 'url' => '/report-dev'],
    ]],
];