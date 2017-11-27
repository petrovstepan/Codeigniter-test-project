<?php
/**
 * Created by PhpStorm.
 * User: Stepan
 * Date: 23.11.17
 * Time: 19:36
 */

class Book extends CI_Model
{
    /**
     * Переменная содержит название поля таблицы, которое должно быть уникальным
     * @var string
     */
    public $unique = 'title';

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
     * @return StClass / null
     */
    public function find($param)
    {
        $table = $this->table;
        $array = (is_array($param) ? $param : ["{$this->table}.id" => $param]);

        $query = $this->db
            ->select(['books.id as id', 'title', 'authors.author as author', 'genre', 'year'])
            ->where($array)
            ->join('genres', 'books.genre_id = genres.id')
            ->join('authors', 'books.author_id = authors.id')
            ->get($table)
            ->result();

        return (count($query) !== 0) ? $query[0] : null;
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
            ->select(['books.id as id', 'title', 'authors.author as author', 'genre', 'year'])
            ->join('genres', 'books.genre_id = genres.id')
            ->join('authors', 'books.author_id = authors.id')
            ->get($table)
            ->result();
    }

    /**
     * Функция сохраняет запись в таблице, выбираея метод вставки или обновления
     *
     * @param $array
     * @return mixed
     */
    public function save(array $array)
    {
        return (isset($array['id'])) ? $this->update($array) : $this->insert($array);
    }

    /**
     * Функция обновляет запись в таблице
     *
     * @param array $data
     * @return int (affected_rows)
     */
    public function update(array $data)
    {
        $table = $this->table;

        $id = $data['id'];
        unset($data['id']);

        $this->db->limit(1)->update($table, $data, ['id' => $id]);
        return $this->db->affected_rows();

    }

    /**
     * Функция вставляет запись в таблицу
     *
     * @param array $data[string $title, string $author, string $genre, int $year]
     * @return int (affected_rows)
     */
    public function insert($data)
    {
        $table = $this->table;

        $this->db->limit(1)->insert($table, $data);
        return $this->db->affected_rows();
    }

    /**
     * Функция удаляет запись из таблицы
     *
     * @param int $id
     * @return int (affected rows)
     */
    public function delete($id)
    {
        $table = $this->table;

        $this->db
            ->where('id', $id)
            ->limit(1)
            ->delete($table);

        return $this->db->affected_rows();
    }

    /**
     * Функция проверяет, является ли значение уникальным в таблице
     *
     * @param string $field, int $id
     * @return bool
     */
    public function unique($field, $id = 0)
    {
        $obj = $this->find([$this->unique => $field]);

        return ($obj === null) || ($id === (int) $obj->id);
    }

}