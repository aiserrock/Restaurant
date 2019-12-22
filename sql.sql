SELECT orders.id                                        AS 'order_id',
       SUM((((
                 COALESCE(dishes.cost, 0) * COALESCE(d.quantity, 0))
           * (100 - COALESCE(orders.sale, 0)) * 0.01))) AS 'total_price'
FROM orders
         LEFT JOIN dishes_orders d ON (orders.id = d.orders_id)
         LEFT JOIN dishes ON (d.dishes_id = dishes.id)
GROUP BY orders.id
ORDER BY orders.id