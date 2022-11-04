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


/*
People class
Класс для работы с базой данных людей
Класс содержит значения имя, фамиля, дата, рождения, город рождения, пол. 
Позволяет сохранять записи в БД, Удалять из БД по id, получать запись.
Имеет статические методы для определения пола по бинарному значению и метод определения возраста на сегодняшний день по дате.

[*] Форматирование человека с преобразованием возраста и (или) пола 
(п.3 и п.4) в зависимотси от параметров (возвращает новый экземпляр 
StdClass со всеми полями изначального класса).
*/

include 'db_class.php';


class People extends Db
{
    private $id;
    private $first_name;
    private $last_name;
    private $date_of_birth;
    private $sex;
    private $city_of_birth;
    
    //если в конструкторе класса указан один аргумент, то он находит запись в БД по переданному аргументу
    //если в конструкторе 5 аргументов, то создается запись в БД
    function __construct()
    {
      $count=func_num_args();
      switch ($count) {
        case 1:
          $record = $this->getById(func_get_arg(0));
          $this->id=$record[0]["id"];
          $this->first_name=$record[0]["first_name"];
          $this->last_name=$record[0]["last_name"];
          $this->date_of_birth=$record[0]["date_of_birth"];
          $this->sex=$record[0]["sex"];
          $this->city_of_birth=$record[0]["city_of_birth"];
          break;
        case 5:
          if($this->AddPeople(func_get_arg(0), func_get_arg(1), func_get_arg(2), func_get_arg(3), func_get_arg(4))) {
            $this->first_name=func_get_arg(0);
            $this->last_name=func_get_arg(1);
            $this->date_of_birth=func_get_arg(2);
            $this->sex=func_get_arg(3);
            $this->city_of_birth=func_get_arg(4);
          }
          break;
        default:
          return 'error';
          break;

      }
    }

    public function AddPeople($first_name, $last_name, $date_of_birth, $sex, $city_of_birth) 
    {
      if($this->ValidData($first_name, $last_name, $date_of_birth, $sex, $city_of_birth)) {
        $conn=$this->connect();
        $sql = "INSERT INTO people(first_name, last_name, date_of_birth, sex, city_of_birth) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $res = $stmt->execute([$first_name, $last_name, $date_of_birth, $sex, $city_of_birth]);
        $this->id=$conn->lastInsertId();
        return $res;
      }
    }
    
    //функция валидации данных
    // 1. Проверяем что строки не пустые
    // 2. Проверяем, что в фио и городе не содержаться цифры или символы отличные от букв алфавита
    // 3. Проверяем дату на корректность и чтобы не была больше сегодняшнего дня.
    // 4. Проверяем, что пол равен 1 или 0
    public function ValidData($fname, $lname, $brthd, $sex, $city)
    {
      if ($fname !== '' && $lname !== '' && $city !== '' && $sex!=='' && $brthd!=='') {
        if (ctype_alpha($fname) && ctype_alpha($lname) && ctype_alpha($city)) {
            $test_data = preg_replace('/[^0-9\-]/u', '', trim($brthd));
            $data_check = explode('-', $test_data);
            $current_date=date('Y-m-d');
            $m=date("$data_check[0]-$data_check[1]-$data_check[2]");
            if (@checkdate($data_check[1], $data_check[2], $data_check[0]) && $current_date-$m>=0) {
                if ($sex=='0' || $sex=='1') {
                  return true;
                } else {
                  return false;
                }
            } else {
              return false;
            } 
        } else { 
          return false;
        }
      }  else {
          return false;
        }
    }
    
    public function GetById($id)
    {
      $sql = "SELECT id, first_name, last_name, date_of_birth, sex, city_of_birth FROM people WHERE id = ?";
      $stmt = $this->connect()->prepare($sql);
      $stmt->execute([$id]);
      while($result = $stmt->fetchAll()) {
        return $result;
      };
    }

    public function DeleteById()
    {
      $sql = "DELETE FROM people WHERE id = ?";
      $stmt = $this->connect()->prepare($sql);
      $stmt->execute([$this->id]);
    }

    public static function CalculateAge($birthday) 
    {
      echo $birthday.'<br>';
      $birthday_timestamp = strtotime($birthday);
      $age = date('Y') - date('Y', $birthday_timestamp);
      if (date('md', $birthday_timestamp) > date('md')) {
        $age--;
      }
      return $age;
    }

    public static function DefineSex($val)
    {
      switch ($val) {
        case 0:
          return 'malex<br>';
          break;
        case 1:
          return 'female<br>';
          break;
         default:
          return 'other<br>';
      }
    }

    function ToStdObject() 
    {
      $empInfo = array(
        'id' => $this->id,
        'first_name' => $this->first_name,
        'last_name' => $this->last_name,
        'date_of_birth' => $this->date_of_birth,
        'sex' => $this->sex,
        'city_of_birth' => $this->city_of_birth,);
      $empInfoObj = (object) $empInfo;
    }

    function GetDateBirth()
    {
        return $this->date_of_birth;
    }

    function GetSex()
    {
        return $this->sex;
    }

    function SetDateBirth($x)
    {
      $this->toStdObject();
      $this->date_of_birth= $x;     
    }

    function SetSex($x)
    {
      $this->toStdObject();
      $this->sex = $x;
    }
}