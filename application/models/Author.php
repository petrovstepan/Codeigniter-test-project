<?php
/**
 * Created by PhpStorm.
 * User: Stepan
 * Date: 25.11.17
 * Time: 0:43
 */

class Author extends CI_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->table = strtolower(__CLASS__) . 's';
    }

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

    public function findOrNew(array $param)
    {
        $author = $this->find($param);

        if ($author !== null)
        {
            return $author;
        }

        $this->insert($param);

        return $this->find($param);
    }

    public function all()
    {
        $table = $this->table;

        return $this->db
            ->get($table)
            ->result();

    }

    public function insert(array $data)
    {
        $table = $this->table;

        $this->db->insert($table, $data);
    }
}