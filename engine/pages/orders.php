<?php

if (!defined('security_hash')) {
    die("Недостаточно прав");
}

$orders = DB::getAll("
SELECT * FROM orders
JOIN order_costs oc ON (orders.id = oc.order_id)
JOIN waiters w ON (orders.waiters_id = w.id)
");

$status = [
    'new' => 'Новый',
    'in_process' => 'В процессе',
    'closed' => 'Закрыт'
];
?>

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Заказы</h1>
        <a href="/orders/new/" class="btn btn-success btn-lg">Создать заказ</a>
    </div>

    <table class="table">
        <thead>
        <tr>
            <th scope="col">№</th>
            <th scope="col">Дата заказа</th>
            <th scope="col">№ Стола</th>
            <th scope="col">Официант</th>
            <th scope="col">Скидка</th>
            <th scope="col">Чаевые</th>
            <th scope="col">Сумма (без скидки)</th>
            <th scope="col">Сумма (со скидкой)</th>
            <th scope="col">Статус</th>
            <th></th>
        </tr>
        </thead>
        <tbody>

        <?
        foreach ($orders AS $order) {
            $sum_without_sale = $order['total_price'] / (100 - $order['sale']) * 100;
            $date = date("j.n.Y H:i", $order['order_date']);

            $date = new DateTime($order['order_date']);
            print <<<HERE
        <tr>
            <th scope="row">{$order['id']}</th>
            <td>{$date->add(new DateInterval('PT3H'))->format('d-m-Y H:i')}</td>
            <td>{$order['tables_id']}</td>
            <td>{$order['name']}</td>
            <td>{$order['sale']}%</td>
            <td>{$order['tips']} руб.</td>
            <td>{$sum_without_sale} руб.</td>
            <td>{$order['total_price']} руб.</td>
            <td>{$status[$order['status']]}</td>
            <td><a href="/orders/view/?id={$order['id']}" class="btn btn-sm btn-outline-warning"><svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a></td>
        </tr>
HERE;


        }

        ?>
        </tbody>
    </table>

</main>

