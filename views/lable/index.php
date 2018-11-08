<?php
session_start();
ini_set("display_errors", "1");

//print_r($_SESSION);
//print_r($_COOKIE['TestCookie']);
//if(!empty($_SESSION['api_key'])) {
//    //header('Location: views/curl.php');
//    header('Location: http'.(empty($_SERVER['HTTPS'])?'://':'s://').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); exit();
//    exit();
//}
//    session_destroy();

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Авторизация </title>

    <!--        <link href="app/css/bootstrap.min.css" rel="stylesheet">-->

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
</head>
<body>
<nav class="navbar navbar-expand-sm bg-dark navbar-dark justify-content-right sticky-top ">
    <!--        <a class="navbar-brand" href="#">Logo</a>-->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link">Авторизация</a>
        </li>
    </ul>
</nav>

<div class="container-fluid my-5">
    <div class="row justify-content-center align-self-center">
        <!--            <div class="col col-sm-3 col-md-3 col-lg-2 col-xl-1 h-100"> </div>-->
        <div class="col col-sm-6 col-md-6 col-lg-4 col-xl-4 h-100">
            <form action="views/curl.php" method="post">
                <div class="form-group">
                    <!--                        <label >Авторизация</label>-->
                    <input type="login" class="form-control form-control-lg" placeholder="Введите свой api-key" name="api_key" value="3991fbcf7d4a3ffc180e36e462f349b52854995c">
                </div>
                <div class="form-group">
                    <button class="btn btn-outline-primary btn-lg btn-block">Войти</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="application/javascript">
    function getTimer(){
        var hour = document.getElementById('hour').innerHTML;<br />
        var minute = document.getElementById('minute').innerHTML;<br />
        var second = document.getElementById('second').innerHTML;<br />
        var end = false;
    }

    window.intervalID = setInterval(timer, 1000);
</script>

</body>
</html>