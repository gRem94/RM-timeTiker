<?php
//ini_set("display_errors", "1");
//
//error_reporting(E_ALL);
    session_start();

//    if(!empty($_POST)) {
        $_SESSION['api_key'] = $_POST['api_key'];
//        header('location: curl.php');
//        exit;
//    }


    function my_autoloader($className) {
        # $className = Test
        include_once('../model/' . $className . '.php');
    };
    spl_autoload_register("my_autoloader");

    //Для контроллера
    $curl = new Curl;
    $user = new Users;

    $user->setAPIkey($_SESSION['api_key']);


    //============Редирект
//    function redirect(/*$url, $permanent = false*/)
//    {
//        header('Location: http'.(empty($_SERVER['HTTPS'])?'://':'s://').$_SERVER['HTTP_HOST']);
//
//        exit();
//    }
    //    echo $_POST['api_key'];
    //    $_SESSION['api_key'] = $_POST['api_key'];

    //var_dump($_REQUEST);
    //if(!empty($_SESSION['api_key']))

//        if(empty($_POST['api_key']) or $_POST['api_key'] == null)
//            redirect('/api_table/');
//        else {
//            $_SESSION['api_key'] = $_POST['api_key'];
//            //setcookie("TestCookie", $_SESSION['api_key']);
//            $user->setAPIkey($_SESSION['api_key']);
//        }
    //=============Редирект

    //session_unset();
    //session_destroy();

    $user->getUserInfo($curl->getUrl());
    //echo $user->getInfo();

    if($curl->getData_issues() == null) {
        $curl->getIssues($user->getAPIkey());
    }
    if($curl->getData_time_entries() == null){
        $curl->getTimeEntries($user->getAPIkey());
        $curl->setTimer();
    }

//    $curl->putTimeEntries($user->getAPIkey());
    //вывод список ошибок
    //error_reporting(E_ALL);
    //ini_set('display_errors', 1);
    //ini_set('display_startup_errors', 1);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Timer - API Redmine</title>

    <!--    <link href="../app/css/bootstrap.css" rel="stylesheet">-->

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
                <a class="nav-link" href="#" onclick="history.back();"><?= $user->getInfo(); session_destroy();?></a>
            </li>
        </ul>
    </nav>

    <div class="container">
        <!--Форма для Секундомера-->
        <form name=timerTask>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <!-- <th scope="col">Проект</th>-->
                <th scope="col">    </th>
                <th scope="col">Трекер</th>
                <th scope="col">Статус</th>
                <th scope="col">Тема</th>
                <th scope="col">Автор</th>
                <th scope="col">Назначена</th>
                <th scope="col">Дата начала</th>
<!--                <th scope="col">Срок завершения</th>-->
<!--                <th scope="col">Обновлена</th>-->
                <th scope="col">Трудозатраты (час(-а/-ов)) </th>
            </tr>
            </thead>
            <tbody>
                <?php
//                print_r($curl->showTable());
                foreach ($curl->showTable() as $key => $value)
                {
                ?>
                <tr><th scope="row"> <?= $value['id'] ?></th>
                    <td><input type=button class="btn btn-outline-secondary bt-sm" value="Старт/Пауза"
                               onclick="StartPauseStopTimer(<?=  json_encode($key, JSON_HEX_TAG) ?>,1)">
                        <input type=button class="btn btn-outline-secondary" value="Стоп"
                               onclick="StartPauseStopTimer(<?=  json_encode($key, JSON_HEX_TAG) ?>,0)"></td>
                    <td> <?= $value['tracker'] ?></td>
                    <td> <?= $value['status'] ?></td>
                    <td><a href="https://redmine.post.msdnr.ru/issues/<?= $value['id'] ?>"> <?= $value['subject'] ?></a></td>
                    <td> <?= $value['author'] ?></td>
                    <td> <?= $value['assigned_to'] ?></td>
                    <td> <?= $value['start_date']  ?></td>
<!--                    <td> --><?//= $value['due_date']    ?><!--</td>-->
<!--                    <td> --><?//= $value['updated_on']  ?><!--</td>-->
                    <td><input id="<?= $value['id'] ?>" name="time_entries" size="10" value="00:00:00"></td></tr>
                <?php
                }
                ?>
            </tbody>
        </table>
<!--            <input name=stopwatch size=10 value="00:00:00">-->
<!--            <input type=button class="btn btn-default" value="Запуск/Остановить" onclick="StartStop()">-->
<!--            <input type=button class="btn btn-default" value="Обнулить" onclick="ClearСlock()">-->
        </form>

    </div>
     <script language="JavaScript" type="text/javascript">

        var Timer = function(id, divs, hours, minutes, seconds, state) {
            var object = this;
            this.id = id;
            this.divs = divs;
//            this.dived = dived;
            this.hours = hours;
            this.minutes = minutes;
            this.seconds = seconds;
            this.interval = null;
            this.state = state;

            this.reduce = function tick() {
                //======таймер
                object.seconds++;
                if(object.seconds > 60)
                    object.seconds = 00, object.minutes++;
                if(object.minutes > 60)
                    object.minutes = 00, hours++;
                if(object.hours > 60)
                    object.hours=00 ;
                object.seconds = object.seconds+"";
                object.minutes = object.minutes+"";
                object.hours = object.hours+"";
                if (object.seconds.length<2) object.seconds = "0"+object.seconds;
                if (object.minutes.length<2) object.minutes = "0"+object.minutes;
                if (object.hours.length<2) object.hours = "0"+object.hours;
                object.divs.value=object.hours+":"+object.minutes+":"+object.seconds;
            }

            this.start = function(){
                object.interval = setInterval(object.reduce, 1000);
            }

            this.pause = function() {
                clearInterval(object.interval);
            }

            this.stop = function(){
                clearInterval(object.interval);
                object.divs.value="00:00:00";
                object.seconds = 00;
                object.minutes = 00;
                object.hours = 00;

            }
        }

        var timer = [];
            function StartPauseStopTimer(objId) {

                if(!timer[objId])
                    timer[objId] = new Timer(objId, document.timerTask.time_entries[objId], 00, 00, 00);


                switch (timer[objId].state) {
//                    case 0:
//                        timer[objId].stop();
//                        timer[objId].state = 1;
//                        break;
                    case 0:
                        timer[objId].start();
                        timer[objId].state = 1;
                        break;
                    case 1:
                        timer[objId].pause();
                        timer[objId].state = 0;
                        break;
                }
                        console.log(timer[objId]);

            }

            function StopTimer(objId) {
                if(timer[objId]) {
                    timer[objId].stop();
                    timer[objId].state = 0;
                }
            }
    </script>
  </body>
</html>
