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
PeopleList class
Kласс для работы со списками людей
Реализует в себе работу с помощью списком Объектов класса People.
Хранит id всех записей People. Позволяет достать все существующие записи в виде объекта People или удалить по id.
 */


include 'peopleClass.php';
if (class_exists('People')) {


    class PeopleList extends Db
    {
        private $ppl_id=array();

        function __construct()
        {
            $sql = "SELECT id FROM people";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll();
            foreach ($result as $row) {
                array_push($this->ppl_id, $row["id"]);
            }
        }

        public function GetArrayPeople()
        {
            $ppl=array();
            foreach ($this->ppl_id as &$value) {
                $p=new People($value);
                array_push($ppl,$p);
            }
        }

        public function DeleteArrayPeople()
        {
            $ppl=array();
            foreach ($this->ppl_id as &$value) {
                $p=new People($value);
                $p->deleteById($value);
            }
        }
    }

} else {
    echo 'Error';
}
?>