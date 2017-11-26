<?php
/**
 * Created by PhpStorm.
 * User: Stepan
 * Date: 23.11.17
 * Time: 19:36
 */

class Book extends CI_Model
{
    public $fillable = ['title', 'genre', 'genre_id', 'author', 'author_id', 'year'];
    public $unique = 'title';
    public $id = null;

    public function __construct()
    {
        parent::__construct();

        $this->table = strtolower(__CLASS__) . 's';

    }

    /**
     * @param $id
     * @param null $table
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

    public function save($array)
    {
        return (isset($array['id'])) ? $this->update($array) : $this->insert($array);
    }

    public function update(array $data)
    {
        $table = $this->table;

        $id = $data['id'];
        unset($data['id']);

        $this->db->limit(1)->update($table, $data, ['id' => $id]);
        return $this->db->affected_rows();

    }

    public function insert($data)
    {
        $table = $this->table;

        $this->db->limit(1)->insert($table, $data);
        return $this->db->affected_rows();
    }

    public function delete($id)
    {
        $table = $this->table;

        $this->db
            ->where('id', $id)
            ->limit(1)
            ->delete($table);

        return $this->db->affected_rows();

    }

    public function unique($field)
    {
        $obj = $this->find([$this->unique => $field]);

        return ($obj === null) || ($this->id === (int) $obj->id);
    }

}