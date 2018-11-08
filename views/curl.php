<?php
//ini_set("display_errors", "1");
//
//error_reporting(E_ALL);
//3991fbcf7d4a3ffc180e36e462f349b52854995c my api key
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
                    <td><input type=button id="<?= $key ?>" class="btn btn-outline-secondary" value="Старт/Пауза"
                               onclick="StartStop(<?=  json_encode($key, JSON_HEX_TAG) ?>)"></td>
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
        //объявляем переменные
        var base = 60;
        var clocktimer,dateObj,dh,dm,ds,ms;
        var readout='';

        var hours   = 1,
            min     = 1,
            tmin    = 1,
            second  = 0,
            tsecond = 0,
            mcsecond= 0,
            status  = 0,
            idTimer,
            readout_arr;

        //Сброс таймера
        function ClearTime()
        {
            clearTimeout(clocktimer);

            hours    = 1;
            min      = 1;
            tmin     = 1;
            second   = 0;
            tsecond  = 0;
            mcsecond = 0;
            status   = 0; //readout='00:00:00';

            for (var i = 0 ; i < document.timerTask.time_entries.length; i++) {
                if (i == idTimer) {
//                    readout = '00:00:00';

                    readout_arr[i] = '00:00:00';

                    document.timerTask.time_entries[i].value = readout_arr[i];
//                    console.log(readout[i]);
                }
            }
        }

        //Старт таймера
        function StartTimer() {

            var createDateObj = new Date();
            var t = (createDateObj.getTime() - dateObj.getTime())-(second*1000);
            if (t > 999) {
                second++;
            }
            if (second >= (min * base)) {
                tsecond = 0;
                min++;
            } else {
                tsecond = parseInt((mcsecond / 100) + second);
                if(tsecond >= base) {
                    tsecond = tsecond - ((min - 1) * base);
                }
            }
            if (min > (hours * base)) {
                tmin = 1;
                hours++;
            } else {
                tmin = parseInt((mcsecond / 100) + min);
                if(tmin >= base) {
                    tmin = tmin - ((hours - 1) * base);
                }
            }
            mcsecond = Math.round(t/10);
            if (mcsecond > 99) { mcsecond = 0; }
            if (mcsecond == 0) { mcsecond = '00'; }
            if (mcsecond > 0 && mcsecond <= 9) { mcsecond = '0'+ mcsecond; }
            if (tsecond  > 0) {
                ds = tsecond;

                if (tsecond < 10) {
                    ds = '0' + tsecond;
                }
            } else {
                ds = '00';
            }
            dm = tmin - 1;
            if (dm > 0) {
                if (dm < 10) {
                    dm = '0'+ dm;
                }
            } else {
                dm = '00';
            }
            dh = hours - 1;
            if (dh > 0) {
                if (dh < 10) {
                    dh = '0'+ dh;
                }
            } else {
                dh = '00';
            }

            for (var i = 0 ; i < document.timerTask.time_entries.length; i++) {
                if (i == idTimer) {
//                        readout = dh + ':' + dm + ':' + ds;// + '.' + mcsecond;
                    readout_arr[i] = dh + ':' + dm + ':' + ds;// + '.' + mcsecond;

                    document.timerTask.time_entries[i].value =  readout_arr[i];
                    //console.log(readout[i]);
                    }
                }

            clocktimer = setTimeout("StartTimer()",1);
        }
        //Старт | стоп
        function StartStop(id_timer) {
            idTimer = id_timer;

            if (status == 0){
                ClearTime();

                dateObj = new Date();
                StartTimer();
                status = 1;
            } else {
                clearTimeout(clocktimer);
                status = 0;
            }
        }

        /* Вариант для одичноного таймера

        var hours   = 1,
            min     = 1,
            tmin    = 1,
            second  = 0,
            tsecond = 0,
            mcsecond= 0,
            status  = 0,
            idTimer;

        //Сброс таймера
        function ClearTime()
        {
            clearTimeout(clocktimer);

            hours    = 1;
            min      = 1;
            tmin     = 1;
            second   = 0;
            tsecond  = 0;
            mcsecond = 0;
            status   = 0; readout='00:00:00';

            document.timerTask.time_entries[parseInt(idTimer)].value = readout;
        }

        //Старт таймера
        function StartTimer() {
            //alert(idTimer);
            var createDateObj = new Date();
            var t = (createDateObj.getTime() - dateObj.getTime())-(second*1000);
            if (t > 999) {
                second++;
            }
            if (second >= (min * base)) {
                tsecond = 0;
                min++;
            } else {
                tsecond = parseInt((mcsecond / 100) + second);
                if(tsecond >= base) {
                    tsecond = tsecond - ((min - 1) * base);
                }
            }
            if (min > (hours * base)) {
                tmin = 1;
                hours++;
            } else {
                tmin = parseInt((mcsecond / 100) + min);
                if(tmin >= base) {
                    tmin = tmin - ((hours - 1) * base);
                }
            }
            mcsecond = Math.round(t/10);
            if (mcsecond > 99) { mcsecond = 0; }
            if (mcsecond == 0) { mcsecond = '00'; }
            if (mcsecond > 0 && mcsecond <= 9) { mcsecond = '0'+ mcsecond; }
            if (tsecond  > 0) {
                ds = tsecond;

                if (tsecond < 10) {
                    ds = '0' + tsecond;
                }
            } else {
                ds = '00';
            }
            dm = tmin - 1;
            if (dm > 0) {
                if (dm < 10) {
                    dm = '0'+ dm;
                }
            } else {
                dm = '00';
            }
            dh = hours - 1;
            if (dh > 0) {
                if (dh < 10) {
                    dh = '0'+ dh;
                }
            } else {
                dh = '00';
            }
            readout = dh + ':' + dm + ':' + ds;// + '.' + mcsecond;
            document.timerTask.time_entries[parseInt(idTimer)].value = readout;
            clocktimer = setTimeout("StartTimer()",1);
        }
        //Старт | стоп
        function StartStop(id_timer) {
            idTimer = id_timer;
            if (status == 0){
                ClearTime();

                dateObj = new Date();
                StartTimer();
                status = 1;
            } else {
                clearTimeout(clocktimer);
                status = 0;
            }
        }
         */
    </script>
  </body>
</html>