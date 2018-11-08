<?php

    Class Users
    {
        //для авторизации
//        private $login;
//        private $password;
        //хранима данные тут
        private $data;

//        public function __construct($login, $password)
//        {
//            $this->login = $login;
//            $this->password = $password;
//        }

        public function getData()
        {
            return $this->data;
        }

        public function getId()
        {
            return $this->data['user']['id'];
        }

        public function getInfo()
        {
            return $this->data['user']['lastname'] . ' ' . $this->data['user']['firstname'];
        }

        public function getAPIkey()
        {
            return $this->data['user']['api_key'];
        }

        public function setAPIkey($api_key){
            $this->data = array(
                'user' => array(
                'api_key' => $api_key
                )
            );
        }

        public function getUserInfo($url)
        {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url . '/users/current.json?key=' . $this->getAPIkey(),
                CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false
            ));

            $response = curl_exec($curl);
            if(!curl_exec($curl)){
//                header('location: ../');
//                exit;
                die('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
            }
            $this->data = json_decode($response,true);
            curl_close($curl);

            return $this->data;
        }
    }
?>