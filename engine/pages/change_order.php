<?php
if (!defined('security_hash')) {
    die("Недостаточно прав");
}

$statusArray = ['new' => 'Новый', 'in_process' => 'В процессе', 'closed' => 'Закрыт'];
if (isset($_POST['table'], $_POST['dish'], $_POST['quantity']) && $_SESSION['id'] > 0) {


    $sale = intval($_POST['sale']);
    $sale = ($sale >= 0 && $sale <= 100) ? $sale : 0;

    $tips = intval($_POST['tips']) ?? 0;
    $tips = abs($tips);

    $table_id = intval($_POST['table']) ?? 0;

    $dish = $_POST['dish'] ?? [];
    $quantity = $_POST['quantity'] ?? [];
    $order_id = $_GET['id'];
    $status = $_POST['status'] ?? 'closed';
    $dishAndQuantity = merge($dish, $quantity);

    sumQuantity($dish, $quantity);
    for ($i = 0; $i < count($dish); $i++) {
        DB::query('UPDATE dishes_orders
        SET quantity=?
        WHERE orders_id=? and dishes_id=?', [$quantity[$i], $order_id, $dish[$i]]);
    }
    $mass = DB::getColumn('SELECT dishes_id FROM dishes_orders WHERE orders_id = ?', [$order_id]);

    $test = array_diff($dish, $mass);

    foreach ($test as $item) {
        DB::add("INSERT INTO dishes_orders
                    (orders_id, dishes_id, quantity)
                    VALUES (:order_id, :dish_id, :quantity)",
            [
                'order_id' => $order_id,
                'dish_id' => $item,
                'quantity' => $dishAndQuantity[$item],
            ]);
         // кол-во

    }

    if ($sale != 0 and $tips != 0) {
        $db_order_id = DB::query("UPDATE orders
        SET  sale=?, tips=?, status=?
        WHERE id=?", [$sale, $tips,]);

    } else {
        $error = 1;
    }

}


// Удаление продукта по name_id
if (isset($_GET['name_id']) && isset($_GET['quantity'])) {

    $name = $_GET['name_id'];
    $quantity = $_GET['quantity'];
    $order_id = $_GET['id'];
    DB::query('DELETE FROM dishes_orders WHERE orders_id=? and dishes_id=? and quantity=?', [$order_id, $name, $quantity]);
    $status = "База успешно обновлена";
} else {
    $status = "Ошибка обновления";
}
$order_id = intval($_GET['id']);




$tables = DB::getAll("SELECT id FROM tables WHERE status = 1");
$dishes = DB::getAll("SELECT * FROM dishes");


if (isset($db_order_id)) $order_id = $db_order_id;
$order = DB::getRow("SELECT * From orders join order_costs oc on orders.id = oc.order_id where id = ?", [$order_id]);
$sum_without_sale = ($order['sale'] != 100) ? $order['total_price'] / (100 - $order['sale']) * 100 : 0;
//$user = DB::getRow("SELECT * FROM waiters WHERE username = ?", [$username]);
$countItemInOrder = DB::getAll("
select SUM(quantity) as 'quantity'
from dishes_orders
WHERE orders_id = ?
GROUP BY orders_id", [$order_id]);

$orders = DB::getAll("
SELECT * FROM orders
JOIN order_costs oc ON (orders.id = oc.order_id)
JOIN waiters w ON (orders.waiters_id = w.id)
");

$dishesInOrder = DB::getAll("
select name,cost,quantity,dishes_id
from dishes_orders
JOIN dishes d on dishes_orders.dishes_id = d.id
WHERE orders_id =?
ORDER BY orders_id", [$order_id]
);


?>

<main role="main" class="col-md-10 ml-sm-auto col-lg-10 pt-3 px-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Изменить заказ<? if (isset($order['id'])) echo "№" . $order['id'] ?></h1>
    </div>
    <form method="post">
        <div class="row">
            <div class="col-md-4 order-md-2 mb-4">
                <h4 class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Корзина</span>
                    <span class="badge badge-secondary badge-pill"><?= $countItemInOrder[0]['quantity'] ?? '' ?></span>
                </h4>
                <!--            --><? // print_r($_GET) ?>
                <!--            --><? // print_r($countItemInOrder)?>
                <!--                        --><? // print_r($countItemInOrder)?>
                <ul class="list-group mb-3">
                    <?
                    if (count($tables) > 0)
                        foreach ($dishesInOrder as $item) {

                            print <<<HERE
                    <li class="list-group-item d-flex justify-content-between lh-condensed">
                        <div>
                            <h6 class="my-0">{$item['name']} <span class="text-muted">x {$item['quantity']}</span></h6>
                        </div>
                        
                        
                        <span class="text-muted">{$item['cost']} &#8381;</span>
                        <a href="/orders/change?id={$order['id']}&name_id={$item['dishes_id']}&quantity={$item['quantity']}" class="btn btn-sm btn-outline-danger">Delete</a>
                    </li>
HERE;
                        }
                    ?>

                    <li class="list-group-item d-flex justify-content-between">
                        <span>Итог (Без скидки):</span>
                        <strong><?= $sum_without_sale ?? '0' ?> &#8381;</strong>
                    </li>

                    <? if ($order['sale'] > 0) : ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Скидка :</span>
                            <strong><?= $order['sale'] ?> %</strong>
                        </li>
                    <? endif; ?>

                    <li class="list-group-item d-flex justify-content-between">
                        <span>Итог:</span>
                        <strong><?= $order['total_price'] ?? '0' ?> &#8381;</strong>
                    </li>


                </ul>

                <div class="card p-2">
                    <div class="input-group">
                        <input name="sale" type="number" class="form-control" placeholder="Скидка" min="0" max="100">
                        <div class="input-group-append">
                            <span class="input-group-text">%</span>
                        </div>

                    </div>

                    <div class="input-group mt-3">
                        <input name="tips" type="number" class="form-control" placeholder="Чаевые" min="0"
                               autocomplete="off">
                        <div class="input-group-append">
                            <span class="input-group-text">&#8381;</span>
                        </div>
                    </div>
                    <div class="input-group mt-3">

                        <select class="custom-select d-block w-100" name="status" required="" autocomplete="off">
                            <option value="" hidden>Статус</option>
                            <?

                            foreach ($statusArray as $key => $value) {
                                $r = $order['status'] == $key ? "selected" : "";
                                echo "<option value='{$key}' $r>{$value}</option>";
                            }
                            ?>

                        </select>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary btn-lg btn-block">Изменить заказ</button>
                </div>

            </div>

            <div class="col-md-8 order-md-1">
                <div class="row">
                    <div class="row col-md-12">
                        <div class="col-md-8 mb-3">
                            <label for="table">Стол </label>
                            <select class="custom-select d-block w-100" id="table" name="table" required>
                                <option value="" hidden>Выберите стол...</option>
                                <?
                                foreach ($tables as $table) {
                                    $r = $order['tables_id'] == $table['id'] ? "selected" : "";
                                    echo "<option value='{$table['id']}' {$r}>{$table['id']}</option>";
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
                                <select class="custom-select d-block w-100" name="dish[]">
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