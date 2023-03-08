<?php
class Database
{
    private $conn;
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "saabre";
    
    public function __construct()
    {
        $dsn = "mysql:host=$this->host;dbname=$this->dbname;charset=utf8mb4";

        try {
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch(PDOException $e){
            echo "Erreur de connexion : " . $e->getMessage();
            exit;
        }
    }

    public function getDataBase()
    {
        return $this->conn;
    }

    public function lastInsertId()
    {
        return $this->conn->lastInsertId();
    }
    
    public function executeRequest($sql, $params = NULL)
    {
        if ($params == NULL){
            $result = $this->conn->query($sql);
        }else{
            $result = $this->conn->prepare($sql);
            $result->execute($params);
        }
        return $result;
    }
}