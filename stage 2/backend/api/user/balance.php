<?php
    // HTTP-заголовки
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    // подключение базы данных и файл, содержащий объекты
    include_once "../config/db.php";
    include_once "../models/User.php";

    // коннект с БД
    $database = new Database();
    $db = $database->getConnection();

    $user = new User($db);       // инициализация юзера
    $user->login = isset($_GET["login"]) ? $_GET["login"] : die();

    $user->auth();      // проверка на существование пользователя

    if ($user->id == -1) {
        http_response_code(401);    // код 401 Не авторизован

        echo json_encode(array(
            "error" => "Пользователь не найден"     // отправка ошибки
        ), JSON_UNESCAPED_UNICODE);
        return;
    }

    $balance = $user->balance();      // получение баланса

    // создание массива ответа
    $responseArr = array(
        "balance" =>  $balance
    );

    http_response_code(200);    // код - ОК
    echo json_encode($responseArr);     // ответ json

?>