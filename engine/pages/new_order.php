<?php
if (!defined('security_hash')) {
    die("Недостаточно прав");
}

if (isset($_POST['table'], $_POST['dish'], $_POST['quantity']) && $_SESSION['id'] > 0) {
    $sale = intval($_POST['sale']);
    $sale = ($sale >= 0 && $sale <= 100) ? $sale : 0;

    $tips = intval($_POST['tips']) ?? 0;
    $tips = abs($tips);

    $table_id = intval($_POST['table']) ?? 0;

    $dish = $_POST['dish'] ?? [];
    $quantity = $_POST['quantity'] ?? [];

    if (count($dish) == count($quantity) && count($quantity) > 0) {
        $db_order_id = DB::add("INSERT INTO orders 
        (tables_id, waiters_id, sale, tips, status) 
        VALUES (:table_id, :waiter_id, :sale, :tips, :status)",
            [
                'table_id' => $table_id,
                'waiter_id' => $_SESSION['id'],
                'sale' => $sale,
                'tips' => $tips,
                'status' => 'new'
            ]);

        if (intval($db_order_id) > 0) {
            for ($i = 0; $i < count($dish); $i++) {
                $d = intval($dish[$i]);
                $q = intval($quantity[$i]);
                DB::add("INSERT INTO dishes_orders 
                    (orders_id, dishes_id, quantity)
                    VALUES (:order_id, :dish_id, :quantity)",
                    [
                        'order_id' => $db_order_id,
                        'dish_id' => $d,
                        'quantity' => $q
                    ]);
            }
        } else {
            $error = 1;
        }
    }
    ob_end_clean();
    header("Location: /orders/change?id=$db_order_id");
    exit;
}


$tables = DB::getAll("SELECT id FROM tables WHERE status = 1");
$dishes = DB::getAll("SELECT * FROM dishes");

$order_id = intval($_GET['id']);
if (isset($db_order_id)) $order_id = $db_order_id;
$order = DB::getRow("SELECT * From orders join order_costs oc on orders.id = oc.order_id where id = ?", [$order_id]);
$sum_without_sale = ($order['sale'] != 100) ? $order['total_price'] / (100 - $order['sale']) * 100 : 0;
//$user = DB::getRow("SELECT * FROM waiters WHERE username = ?", [$username]);
$countItemInOrder = DB::getAll("
select SUM(quantity) as 'quantity'
from dishes_orders
WHERE orders_id = ?
GROUP BY orders_id


ORDER BY orders_id", [$order_id]);

$orders = DB::getAll("
SELECT * FROM orders
JOIN order_costs oc ON (orders.id = oc.order_id)
JOIN waiters w ON (orders.waiters_id = w.id)
");

$dishesInOrder = DB::getAll("
select name,cost,quantity
from dishes_orders
JOIN dishes d on dishes_orders.dishes_id = d.id
WHERE orders_id =?
ORDER BY orders_id", [$order_id]
);


?>

<main role="main" class="col-md-10 ml-sm-auto col-lg-10 pt-3 px-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Добавить заказ <? if (isset($order['id'])) echo "№" . $order['id'] ?></h1>
    </div>
    <form method="post" action="">
        <div class="row">
            <div class="col-md-4 order-md-2 mb-4">
                <div class="card p-2">
                    <div class="input-group">
                        <input name="sale" type="number" class="form-control" placeholder="Скидка" min="0" max="100">
                        <div class="input-group-append">
                            <span class="input-group-text">%</span>
                        </div>

                    </div>

                    <div class="input-group mt-3">
                        <input name="tips" type="text" class="form-control" placeholder="Чаевые">
                        <div class="input-group-append">
                            <span class="input-group-text">&#8381;</span>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary btn-lg btn-block">Добавить заказ</button>
                </div>

            </div>

            <div class="col-md-8 order-md-1">
                <div class="row">
                    <div class="row col-md-12">
                        <div class="col-md-8 mb-3">
                            <label for="country">Стол</label>
                            <select class="custom-select d-block w-100" id="table" name="table" required="">
                                <option value="" hidden>Выберите стол...</option>
                                <?
                                if (count($tables) > 0)
                                    foreach ($tables as $table) {
                                        echo "<option value='{$table['id']}'>{$table['id']}</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class=" mt-auto">
                                <a id="add_dish" href="javascript:" class="btn btn-sm btn-success mb-1">+</a>
                            </div>
                        </div>
                    </div>
                    <div id="dishes" class="row col-md-12">
                        <div id="dish_default" class="dish row col-md-12 mb-3">
                            <div class="col-md-7">
                                <label for="table">Блюдо</label>
                                <select class="custom-select d-block w-100" name="dish[]" required="">
                                    <option value="" hidden>Выберите блюдо...</option>
                                    <?
                                    foreach ($dishes as $dish) {
                                        echo "<option value='{$dish['id']}'>{$dish['name']} - {$dish['cost']}руб.</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="quantity">Количество</label>
                                <input type="number" name="quantity[]" class="quantity form-control" min="1"
                                       max="99"
                                       placeholder=""
                                       autocomplete="off">

                                <div class="invalid-feedback">
                                    Неверные значения
                                </div>
                            </div>
                            <div class="col-md-1 mt-auto">
<!--                                <a href="javascript:" class="remove_dish btn btn-sm btn-outline-warning mb-1">x</a>-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</main>

<script>
    $(function () {
        let dishes = $('#dishes');


        $('#add_dish').click(function () {

            let obj = $('#dish_default').clone();

            obj.removeAttr('id');
            obj.find('.quantity').val('');
            obj.appendTo(dishes);
            return false;
        });

        $('.remove_dish').click(function () {
            console.log("1");
            $(this).remove();
            return false;
        });
    });


</script>