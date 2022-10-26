<?php
    // HTTP-заголовки
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    // подключение базы данных и файл, содержащий объекты
    include_once "../config/db.php";
    include_once "../models/Product.php";

    // коннект с БД
    $database = new Database();
    $db = $database->getConnection();
    
    $product = new Product($db);    // инициализируем объект

    // запрашиваем товары
    $stmt = $product->getAll();
    $num = $stmt->rowCount();

    // проверка, найдено ли больше 0 записей
    if ($num > 0) {
        // массив товаров
        $products_arr = array();
        $products_arr["products"] = array();

        // получение содержимого
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // извлекаем строку
            extract($row);
            $product_item = array(
                "id" => $id,
                "description" => $description,
                "price" => $price
            );
            array_push($products_arr["products"], $product_item);
        }

        http_response_code(200);    // 200 OK
        echo json_encode($products_arr);    // откправка json
    } else {
        http_response_code(404);        // 404 Не найдено
        echo json_encode(array("error" => "Товары не найдены"), JSON_UNESCAPED_UNICODE);
    }

?>