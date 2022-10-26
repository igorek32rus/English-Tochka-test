<?php
    // HTTP-заголовки
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    // подключение базы данных и файл, содержащий объекты
    include_once "../config/db.php";
    include_once "../models/User.php";

    // коннект с БД
    $database = new Database();
    $db = $database->getConnection();

    $user = new User($db);       // инициализация юзера
    $data = file_get_contents('php://input');
    $json = json_decode($data);

    $user->login = $json->login;

    $user->auth();      // авторизация
    $balance = $user->balance();      // получение баланса
    $stmt = $user->boughtProducts();   // купленные продукты

    // массив продуктов
    $products_arr = array();

    // получение содержимого
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // извлекаем строку
        extract($row);
        array_push($products_arr, $product_id);
    }

    if ($user->id != -1) {
        // создание массива ответа
        $responseArr = array(
            "id" =>  $user->id,
            "name" => $user->name,
            "login" => $user->login,
            "balance" => $balance,
            "products" => $products_arr
        );

        http_response_code(200);    // код - ОК
        echo json_encode($responseArr);     // ответ json
    } else {
        http_response_code(404);    // код 404 Не найдено

        echo json_encode(array(
            "error" => "Пользователь не найден"     // отправка ошибки
        ), JSON_UNESCAPED_UNICODE);
    }

?>