<?php
    include_once "config.php";

    class Database {
        private $host = DB_HOST;
        private $db_name = DB_NAME;
        private $username = DB_USER;
        private $password = DB_PASS;
        public $conn;
    
        public function getConnection() {     // подключение к БД
            $this->conn = null;
    
            try {
                $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
                $this->conn->exec("set names utf8");
            } catch (PDOException $exception) {
                echo "Error: " . $exception->getMessage();
            }
    
            return $this->conn;
        }
    }
?>