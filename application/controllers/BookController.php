<?php
/**
 * Created by PhpStorm.
 * User: Stepan
 * Date: 23.11.17
 * Time: 23:33
 */


class BookController extends CI_Controller
{
    public $foreignKeys = ['genre', 'author'];
    public $errors = [];

    public function __construct()
    {
        parent::__construct();
        $this->load->model('book', '', True);
    }

    public function index()
    {
        $data = $this->book->all();

        $body = $this->load->view('book/index', ['books' => $data], true);

        return $this->viewWithHeadFoot($body);
    }

    public function create()
    {
        $data = $this->getInfoToForm();

        $body = $this->load->view('book/form', $data, true);

        return $this->viewWithHeadFoot($body,'Добавить книгу');
    }

    public function store()
    {
        $this->db->trans_start();

        $data = $this->input->post(NULL, TRUE);

        $processedData = $this->processPostData($data);

        if (count($processedData) !== 4)
        {
            return $this->returnWithStatus('417 Expectation Failed');
        }

        if ($this->book->unique($processedData[$this->book->unique]) === false)
        {
            return $this->returnWithStatus('409 Conflict');
        }

        $row = $this->book->save($processedData);

        if ((int) $row > 1 || (int) $row === 0)
        {
            return $this->returnWithStatus('501 Not Implemented');
        }

        $this->db->trans_complete();

        return $this->returnWithStatus('201 Created');
    }

    public function edit($id)
    {
        $id = (int) $id;
        $book = $this->book->find($id);

        if ($book === null)
        {
            return $this->returnWithStatus('404 Not Found');
        }

        $data = $this->getInfoToForm();
        $data['book'] = $book;

        $body = $this->load->view('book/form', $data, true);

        return $this->viewWithHeadFoot($body,'Редактировать книгу ' . "\"{$book->title}\"");

    }

    public function update($id)
    {
        $this->db->trans_start();

        $id = (int) $id;
        $book = $this->book->find($id);
        $this->book->id = $id;

        if ($book === null)
        {
            return $this->returnWithStatus('404 Not Found');
        }

        $data = $this->input->post(NULL, TRUE);

        $processedData = $this->processPostData($data);
        $processedData['id'] = $id;


        if ($this->book->unique($processedData[$this->book->unique]) === false)
        {
            return $this->returnWithStatus('417 Expectation Failed');
        }

        $row = $this->book->save($processedData);

        if ((int) $row >  1)
        {
            return $this->returnWithStatus('501 Not Implemented');
        }

        if ($row === 0)
        {
            return $this->returnWithStatus('210 No Changes');
        }

        $this->db->trans_complete();

        header('HTTP/1.1 202 Accepted');
        header('Location: /books');
    }

    public function destroy($id)
    {
        $this->db->trans_start();
        $id = (int) $id;

        $row = $this->book->delete($id);

        if ((int) $row > 1 || (int) $row === 0)
        {
            return $this->returnWithStatus('501 Not Implemented');
        }

        $this->db->trans_complete();

        return $this->returnWithStatus('200 Ok');
    }

    private function getInfoToForm()
    {

        $this->load->model('genre');
        $this->load->model('author');

        $genres = $this->genre->all();
        $authors = $this->author->all();
        $years = range(1950, 2017);

        return ['genres' => $genres, 'authors' => $authors, 'years' => $years];
    }

    private function processPostData(array $data)
    {
        foreach ($data as $key => $value)
        {
            if ($key === 'year')
            {
                if (is_numeric($value) === true)
                {
                    if ((int) $value < 1950 || (int) $value > 2017)
                    {
                        return $this->returnWithStatus('417 Expectation Failed');
                    } else
                    {
                        continue;
                    }
                }
            }

            if ($value == null || (is_numeric($value) === true))
            {
                unset($data[$key]);
            }

        }

        foreach ($this->foreignKeys as $key)
        {
            $this->load->model($key);
        }

        foreach ($data as $key => $value)
        {
            if (in_array($key, $this->foreignKeys) === false)
            {
                continue;
            }

            $obj = $this->$key->findOrNew([$key => $data[$key]]);
            $data["{$key}_id"] = $obj->id;
            unset($data[$key]);
        }

        return $data;
    }

    private function getFillbale(array $data, array $fillable = null)
    {
        $fillable = ($fillable === null) ? $this->book->fillable : $fillable;

        foreach($data as $key => $value)
        {
            if (in_array($key, $fillable) === false)
            {
                unset($data[$key]);
            }
        }

        return $data;
    }

    private function viewWithHeadFoot($body, $title = null)
    {
        $title = ($title === null) ? 'Библиотека' : $title;

        $output = $this->load->view('general/header', ['title' => $title], true) . $body;

        echo $output . $this->load->view('general/footer', '', true);
    }

    public function redirectToResource()
    {
        header('Location: /books');
        exit();
    }

    private function returnWithStatus($status)
    {
        header('HTTP/1.1 ' . $status);
        $body = $this->load->view('book/status', ['status' => $status], true);
        return $this->viewWithHeadFoot($body, (int) $status);
    }


}