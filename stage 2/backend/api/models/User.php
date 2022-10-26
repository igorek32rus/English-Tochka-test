<?php
    class User {
        private PDO $conn;
        private $table_name = "users";      // имя таблицы в БД
    
        // свойства пользователя
        public int $id = -1;
        public string $name;
        public string $login;
    
        public function __construct($db) {
            $this->conn = $db;
        }
    
        function auth() {
            $query = "SELECT `id`, `name`, `login` FROM `".$this->table_name."` WHERE `login` = :userLogin";
            
            $stmt = $this->conn->prepare($query);       // подготовка запроса
            $stmt->bindParam(':userLogin', $this->login, PDO::PARAM_STR);   // привязка имени пользователя

            $stmt->execute();       // выполняем запрос

            // получаем строку
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!empty($row["id"])) {
                // установка значений свойств пользователя
                $this->id = $row["id"];
                $this->name = $row["name"];
                $this->login = $row["login"];
            }
        }

        // SELECT SUM(DISTINCT `price`) as `sum` FROM `coins` WHERE user_id = :userID
        // SELECT SUM(products.price) FROM products INNER JOIN orders_users ON orders_users.product_id = products.id WHERE orders_users.user_id = :userID;

        // метод для получения товаров
        function balance() {
            // общая сумма заработанного
            $query = "SELECT 
                IFNULL(
                    (SELECT 
                        SUM(DISTINCT `price`) as `sum` 
                    FROM `coins` 
                    WHERE `user_id` = :userID),
                0) - 
                IFNULL(
                    (SELECT 
                        SUM(`products`.`price`) 
                    FROM `products` 
                    INNER JOIN `orders_users` 
                        ON `orders_users`.`product_id` = `products`.`id` 
                    WHERE `orders_users`.`user_id` = :userID), 
                0) 
                as `sum`";
            $stmt = $this->conn->prepare($query);       // подготовка запроса
            $stmt->bindParam(':userID', $this->id, PDO::PARAM_INT);   // привязка id пользователя

            $stmt->execute();       // выполняем запрос

            // получаем строку
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!empty($row["sum"])) {
                return $row["sum"];
            }
        }
        
        // купленные продукты
        function boughtProducts() {
            // выбираем все записи для пользователя
            $query = "SELECT product_id FROM `orders_users` WHERE `user_id` = :userID";
            $stmt = $this->conn->prepare($query);   // подготовка запроса
            $stmt->bindParam(':userID', $this->id, PDO::PARAM_INT);   // привязка id пользователя
            $stmt->execute();       // запрос
            return $stmt;
        }
    }
?>