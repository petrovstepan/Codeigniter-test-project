<?php
/**
 * Created by PhpStorm.
 * User: Stepan
 * Date: 25.11.17
 * Time: 0:31
 */

class Genre extends CI_Model
{
    /**
     * Переменная содержит название таблицы БД, которую представляет модель
     * Вида {имя класса с маленькой буквы}s
     * @var string
     */
    public $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = strtolower(__CLASS__) . 's';
    }

    /**
     * Фунция ищет запись в таблице по $id или другому параметру
     *
     * @param int / array $param
     * @return stdClass / null
     */
    public function find($param)
    {
        $table = $this->table;
        $array = is_int($param) ? ['id' => $param] : $param;

        $query = $this->db
            ->where($array)
            ->get($table)
            ->result();

        return (count($query) !== 0) ? $query[0] : null;
    }

    /**
     * Функция ищет запись в таблице или создает новую с данными из массива $param и возвращает объект это записи
     *
     * @param array $param
     * @return stdClass
     */
    public function findOrNew(array $param)
    {
        $obj = $this->find($param);

        if ($obj !== null)
        {
            return $obj;
        }

        $this->insert($param);

        return $this->find($param);
    }

    /**
     * Функция возвращает все записи из таблицы
     *
     * @return array[stdClass / empty]
     */
    public function all()
    {
        $table = $this->table;

        return $this->db
            ->get($table)
            ->result();
    }

    /**
     * Функция вставляет новую запись в таблицу
     *
     * @param array $data
     */
    public function insert(array $data)
    {
        $table = $this->table;

        $this->db->insert($table, $data);
    }
}