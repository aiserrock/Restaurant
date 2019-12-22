<?php if (!defined('security_hash')) {
    die("Недостаточно прав");
}
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Додо Ресторан</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/4.0/examples/dashboard/">

    <!-- Bootstrap core CSS -->
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="/assets/css/dashboard.css" rel="stylesheet">

    <!--header icon-->
    <link rel="shortcut icon" href="/assets/img/logo.png" type="image/x-icon">
</head>

<body>
<nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0">
    <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="#">Додо Ресторан</a>
    <ul class="navbar-nav px-3">
        <li class="nav-item text-nowrap">
            <a class="nav-link" href="/logout/">Выйти</a>
        </li>
    </ul>
</nav>

<div class="container-fluid">
    <div class="row">
        <nav class="col-md-2 d-none d-md-block bg-light sidebar">
            <div class="sidebar-sticky">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <p class="nav-link">
                            Пользователь: <?=$_SESSION['username'] ?>
                        </p>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="/">
                            <span data-feather="command"></span>
                            Главная
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/orders/">
                            <span data-feather="shopping-cart"></span>
                            Заказы
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/dishes/">
                            <span data-feather="file"></span>
                            Блюда
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <span data-feather="book-open"></span>
                            Столы
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

