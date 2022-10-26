<?php
    // HTTP-заголовки
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    // подключение базы данных и файлы, содержащие объекты
    include_once "../config/db.php";
    include_once "../models/Product.php";
    include_once "../models/User.php";

    // коннект с БД
    $database = new Database();
    $db = $database->getConnection();

    $product = new Product($db);        // инициализация продукта
    $user = new User($db);              // инициализация юзера
    $data = file_get_contents('php://input');
    $json = json_decode($data);

    $product->id = $json->product_id;
    $user->login = $json->login;

    $user->auth();      // авторизация
    if ($user->id == -1) {
        http_response_code(401);    // код 401 Не авторизован

        echo json_encode(array(
            "error" => "Пользователь не найден"     // отправка ошибки
        ), JSON_UNESCAPED_UNICODE);
        return;
    }

    $product->getOne();
    if ($product->id == -1) {
        http_response_code(400);    // код 400 ошибка

        echo json_encode(array(
            "error" => "Продукт не найден"     // отправка ошибки
        ), JSON_UNESCAPED_UNICODE);
        return;
    }

    $balance = $user->balance();
    if ($balance - $product->price < 0) {
        http_response_code(400);    // код 400 ошибка

        echo json_encode(array(
            "error" => "На Вашем счету недостаточно средств"     // отправка ошибки
        ), JSON_UNESCAPED_UNICODE);
        return;
    }

    if (!$product->buy($user->id)) {
        http_response_code(500);    // ошибка добавления

        echo json_encode(array(
            "error" => "Произошла ошибка при покупке продукта"     // отправка ошибки
        ), JSON_UNESCAPED_UNICODE);
        return;
    }

    // запрашиваем купленные товары
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

    $products_arr["balance"] = $balance - $product->price;      // возврат обновлённого баланса

    http_response_code(200);    // 200 OK
    echo json_encode($products_arr);    // откправка json
?>