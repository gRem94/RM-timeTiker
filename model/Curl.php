<?php

    Class Curl
    {

        private $url;
        //Массив с пользовательскими данными
        private $data_issues;
        private $data_time_entries;

        public function __construct()
        {
            //Пока введены данные к моей api
            $this->url = 'https://redmine.post.msdnr.ru';
        }

        public function getUrl(){
            return $this->url;
        }

        public function getData_issues(){
            return $this->data_issues;
        }

        public function getData_time_entries()
        {
            return $this->data_time_entries;
        }
        
        public function getIssues($api_key)
        {
            // создание нового ресурса cURL
            $curl = curl_init();
            // установка URL и других необходимых параметров
            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->getUrl() . '/issues.json?key='. $api_key
                    . '&utf8=%E2%9C%93&set_filter=1&f[]=status_id&op[status_id]=o&f[]=assigned_to_id&op[assigned_to_id]=%3D&v[assigned_to_id][]=me&f[]=tracker_id&op[tracker_id]=!&v[tracker_id][]=9&query[sort_criteria][0][]=status&query[sort_criteria][0][]=desc&query[sort_criteria][1][]=id&query[sort_criteria][1][]=desc&query[group_by]=tracker&c[]=tracker&c[]=status&c[]=subject&c[]=author&c[]=assigned_to&c[]=updated_on&saved_query_id=141',
                //поиск по id пользователя
                CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false
            ));
            //загрузка страницы и выдача её браузеру
            $response = curl_exec($curl);
            // Error
            if(!curl_exec($curl)){
                die('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
            } else {
                $this->data_issues = json_decode($response,true);
            }
            // завершение сеанса и освобождение ресурсов
            curl_close($curl);

            return $this->data_issues;
        }

        //выводит список затраченного времени по задачам
        public function setTimer()
        {
            #=====================Старый таймер===================
            $count = 0;
            foreach ($this->data_issues as $key => $value)
                foreach ($value as $key1 => $values) {
                    if ($values['closed_on'] == '') {
                        $sub = abs(strtotime($values['created_on']) - time());
                    } else {
                        $sub = abs(strtotime($values['closed_on']) - strtotime($values['created_on']));
                    }

                    $hours = (int)($sub / (60 * 60));
                    $min = (int)(($sub - $hours * 60 * 60) / 60) / 100;

                    $time_entries = (int)$hours + $min;
                    $this->data_issues['issues'][$count++] += array(
                        'time_entries_new' => $time_entries
                    );

                    $count1 = 0;
                    foreach ($this->data_time_entries as $value1) {
                        $this->data_time_entries[$count1++]['time_entries'] += array(
                            'hours_new' => $time_entries
                        );
                    }
                }
            #=====================Старый таймер===================


        }

        public function getTimeEntries($api_key)
        {
            $this->data_time_entries = array();
            foreach ($this->data_issues as $key => $value)
                foreach ($value as $key1 => $values) {
                    // создание нового ресурса cURL
                    $curl = curl_init();

                    // установка URL и других необходимых параметров
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => $this->getUrl() . '/time_entries.json?key=' . $api_key
                            . '&c[]=project&c[]=spent_on&c[]=user&c[]=activity&c[]=issue&c[]=comments&c[]=hours&f[]=spent_on&f[]=issue_id&f[]=&group_by=&op[issue_id]=~&op[spent_on]=*&set_filter=1&sort=spent_on&t[]=hours&t[]=&v[issue_id][]='.$values['id'],
                        //поиск по id пользователя
                        CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_SSL_VERIFYPEER => false
                    ));
                    //загрузка страницы и выдача её браузеру
                    $response = curl_exec($curl);
                    $result = json_decode($response, true);
                    // Error
                    if (!curl_exec($curl)) {
                        die('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
                    } else {
                        array_push($this->data_time_entries,$result);
                        //$this->data_time_entries = json_decode($response,true);
                    }
                    // завершение сеанса и освобождение ресурсов
                    curl_close($curl);
                }

            return $this->data_time_entries;
        }

        //выводит список задач
        public function showTable()
        {
            if ($this->data_issues != null)
                foreach ($this->data_issues as $value)
                    foreach ($value as $values) {
                        $this->data_to_table[] = [
                            'id' => $values['id'],
                            'tracker' => $values['tracker']['name'],
                            'status'  => $values['status']['name'],
                            'subject' => $values['subject'],
                            'author'  => $values['author']['name'],
                            'assigned_to' => $values['assigned_to']['name'],
                            'start_date'  => $values['start_date'],
                            'due_date'    => $values['due_date'],
                            'updated_on'  => $values['updated_on'],
//                        'time_entries_new' => '00:00:00',
                        ];
                    }
            else // Работаем с фейковыми данными, пока нет доступа
                $this->data_to_table = [
                    '0' =>
                        [
                            'id' => '01234',
                            'tracker' => 'Задача',
                            'status'  => 'Новая',
                            'subject' => 'Локал',
                            'author'  => 'test',
                            'assigned_to' => 'test',
                            'start_date'  => '11-11-1111',
                            'due_date'    => '11-11-1111',
                            'updated_on'  => '11-11-1111',
                        ],
                    '1' =>
                        [
                            'id' => '02345',
                            'tracker' => 'Задача',
                            'status'  => 'В работе',
                            'subject' => 'Локал 2',
                            'author'  => 'test',
                            'assigned_to' => 'test',
                            'start_date'  => '22-22-2222',
                            'due_date'    => '22-22-2222',
                            'updated_on'  => '22-22-2222',
                        ],
                ];


            return $this->data_to_table;
        }
            #=========== Вывод в таблицу
            //  echo '<tr><th scope="row">' . $values['id'] . '</th> '.
//                            //<td>' . $values['project']['name'] . '</td>
//                              '<td>' . $values['tracker']['name'] . '</td>
//                               <td>' . $values['status']['name'] . '</td>
//                               <td> <a href="https://redmine.post.msdnr.ru/issues/'. $values['id'] .'">' . $values['subject'] . '</a></td>
//                               <td>' . $values['author']['name'] . '</td>
//                               <td>' . $values['assigned_to']['name'] . '</td>
//                               <td>' . $values['start_date'] . '</td>
//                               <td>' . $values['due_date'] . '</td>
//                               <td>' . $values['updated_on'] . '</td>
//                               <td>' . $values['time_entries_new'] . '</td>
//                               </tr>';
            #============
        public function putTimeEntries($api_key)
        {
            $curl = curl_init();

            $count = 0;
            foreach ($this->data_time_entries as $key => $value)
                foreach ($value as $key1 => $values) {
                if($values[0]['id'] != null)
                    $data_to_entries[$count++] = array(
                        'time_entry' => array(
                            'id_entry' => $values[0]['id'],
                            'id_issue' => $values[0]['issue']['id'],
                            'hours' => $values['hours_new']
                        ),
                    );
                }

             echo '<pre>'.print_r($data_to_entries).'</pre>';

            //Рабочий пример
            /*$data_json = json_encode($data_put);

            curl_setopt_array($curl,
                array(
                    CURLOPT_URL => $this->getUrl() . '/time_entries/247656.json?key='.$api_key,
                    //CURLOPT_HTTPHEADER => array('Expect:'),
                    CURLOPT_CUSTOMREQUEST => 'PUT',
                    CURLOPT_POSTFIELDS => $data_json,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_SSL_VERIFYPEER => false,
                    //CURLOPT_FOLLOWLOCATION => 1,
                    //CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_VERBOSE => true,
                    CURLOPT_HTTPHEADER =>
                        array(
                            'Content-Type: application/json; charset=utf-8',
                            'Content-Length: ' . strlen($data_json)
                        ),
                ));

                $response  = curl_exec($curl);
                if(!$response){
                    $response = (json_encode(array(array("error" => curl_error($curl), "code" => curl_errno($curl)))));
                }
                curl_close($curl);

               // echo print_r(get_headers(json_encode($curl), 1));
                return $response;*/
        }
?>
