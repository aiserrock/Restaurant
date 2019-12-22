<?php
if (!defined('security_hash')) {
    die("Недостаточно прав");
}


if (isset($_SESSION['id'])) {
    header('Location: /');
}

if (!empty($_POST)) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $user = DB::getRow("SELECT * FROM waiters WHERE username = ?", [$username]);

    if (password_verify($password, $user['password'])) {
        $_SESSION['id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['username'] = $user['username'];
        header('Location: /');
    } else {
        $error = true;
    }

}

?>


<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">


    <title>Авторизация</title>

    <!-- Bootstrap core CSS -->
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="/assets/css/signin.css" rel="stylesheet">
</head>


<body class="text-center">

<form class="form-signin" method="post" action="/login/">
    <img class="mb-4" src="/assets/img/logo.png" alt="" width="128" height="128">
    <h1 class="h3 mb-3 font-weight-normal">Додо Ресторан</h1>
    <? if ($error) : ?>
        <div class="alert alert-danger" role="alert">
            Неверный логин или пароль
        </div>
    <? endif; ?>

    <label for="inputEmail" class="sr-only">Email address</label>
    <input name="username" type="text" id="inputEmail" class="form-control" placeholder="Логин" required autofocus>
    <label for="inputPassword" class="sr-only">Password</label>
    <input name="password" type="password" id="inputPassword" class="form-control" placeholder="Пароль" required>
    <button class="btn btn-lg btn-primary btn-block" type="submit">Войти</button>
    <p class="mt-5 mb-3 text-muted">&copy; 2019</p>
</form>
</body>
</html>