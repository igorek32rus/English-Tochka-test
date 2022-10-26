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
    
    $user = new User($db);    // инициализируем объект
    $user->login = isset($_GET["login"]) ? $_GET["login"] : die();
    $user->auth();      // проверка на существование пользователя

    if ($user->id == -1) {
        http_response_code(401);    // код 401 Не авторизован

        echo json_encode(array(
            "error" => "Пользователь не найден"     // отправка ошибки
        ), JSON_UNESCAPED_UNICODE);
        return;
    }

    // запрашиваем товары
    $stmt = $user->boughtProducts();
    $num = $stmt->rowCount();

    // массив товаров
    $products_arr = array();
    $products_arr["products"] = array();

    if ($num == 0) {
        http_response_code(200);    // 200 OK
        echo json_encode($products_arr);    // откправка json
        return;
    }

    // получение содержимого
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // извлекаем строку
        extract($row);
        array_push($products_arr["products"], $product_id);
    }

    http_response_code(200);    // 200 OK
    echo json_encode($products_arr);    // откправка json

?>