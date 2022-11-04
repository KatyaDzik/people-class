<?php 
 /**
* Автор: Дзик Екатерина
*
* Дата реализации: 04.11.2022 10:00
*
* Дата изменения: 04.11.2022 22:00
*
* Утилита для работы с базой данных - PHPMyAdmin 
*/

class Db {
  private $host = "localhost";
  private $user = "root";
  private $pwd = "root";
  private $dbName = "test";

  public function connect() {
    $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbName;
    $pdo = new PDO($dsn, $this->user, $this->pwd);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
  }
}
