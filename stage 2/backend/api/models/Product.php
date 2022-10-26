<?php
    class Product {
        private PDO $conn;
        private $table_name = "products";      // имя таблицы в БД
    
        // свойства товара
        public int $id = -1;
        public string $description;
        public string $price;
    
        public function __construct($db) {
            $this->conn = $db;
        }
    
        // метод для получения товаров
        function getAll() {
            // выбираем все записи
            $query = "SELECT * FROM ".$this->table_name."";
            $stmt = $this->conn->prepare($query);   // подготовка запроса
            $stmt->execute();       // запрос
            return $stmt;
        }

        function getOne() {
            $query = "SELECT * FROM `".$this->table_name."` WHERE `id` = :productID";
            
            $stmt = $this->conn->prepare($query);       // подготовка запроса
            $stmt->bindParam(':productID', $this->id, PDO::PARAM_INT);   // привязка id продукта
            $stmt->execute();       // выполняем запрос

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!empty($row["id"])) {
                // установка значений свойств продукта
                $this->id = $row["id"];
                $this->description = $row["description"];
                $this->price = $row["price"];
            }
        }

        function buy($userID) {
            // запрос на покупку продукта
            $query = "INSERT INTO `orders_users` SET `product_id` = :productID, `user_id` = :userID";

            // подготовка запроса
            $stmt = $this->conn->prepare($query);

            // привязка значений
            $stmt->bindParam(":productID", $this->id);
            $stmt->bindParam(":userID", $userID);

            // выполняем запрос
            if ($stmt->execute()) return true;

            return false;
        }
    }
?>