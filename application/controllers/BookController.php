<?php
/**
 * Created by PhpStorm.
 * User: Stepan
 * Date: 23.11.17
 * Time: 23:33
 */


class BookController extends CI_Controller
{
    /**
     * Переменная содержит названия входящих POST параметров, которые должны быть обработаны
     * @var array
     */
    public $foreignKeys = ['genre', 'author'];

    public function __construct()
    {
        parent::__construct();
        $this->load->model('book', '', True);
    }

    /**
     * Функция возвращает все содержащиеся в таблице `books` записи
     * GET books/index || GET books
     *
     * @return string (view)
     */
    public function index()
    {
        $data = $this->book->all();

        $body = $this->load->view('book/index', ['books' => $data], true);

        return $this->viewWithHeadFoot($body);
    }

    /**
     * Функция возвращает форму для добавления записи в таблицу `books`
     * GET books/create
     *
     * @return string (view)
     */
    public function create()
    {
        $data = $this->getInfoToForm();

        $body = $this->load->view('book/form', $data, true);

        return $this->viewWithHeadFoot($body,'Добавить книгу');
    }

    /**
     * Функция добавляет новую запись в таблицу `books`
     * POST books
     * @param POST[string $title, string $author, string $genre, int $year]
     *
     * @status
     * Не все параметры (4) - 417
     * Не уникальный $title в таблице `books` - 409
     * Rows affected != 1 - 501
     * Success - 201
     *
     * @return string (view)
     */
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

        if ((int) $row !== 1)
        {
            return $this->returnWithStatus('501 Not Implemented');
        }

        $this->db->trans_complete();

        return $this->returnWithStatus('201 Created');
    }

    /**
     * Функция возвращает форму для редактирования существующей записи из таблицы `books`
     * GET books/id/edit
     *
     * @param int $id
     *
     * @status
     * Отсутствует запись с данным $id - 404
     *
     * @return string (view)
     */
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

    /**
     * Функция изменяет запись в таблице `books` по $id
     * POST books/id
     *
     * @param int $id
     * @param POST[string $title, string $author, string $genre, int $year]
     *
     * @status
     * Отсутствует запись с данным $id - 404
     * Не уникальный $title в таблице `books` - 409
     * Rows affected > 1 - 501
     * Rows affected == 0 - 210
     * Success - 202
     *
     * @return string (view)
     */
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
            return $this->returnWithStatus('409 Expectation Failed');
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

        return $this->returnWithStatus('202 Accepted');
    }

    /**
     * Функция удаляет запись из таблицы `books` по $id
     * GET books/id/delete
     *
     * @param int $id
     *
     * @status
     * Rows affected != 1 501
     * Success - 200
     *
     * @return string (view)
     */
    public function destroy($id)
    {
        $this->db->trans_start();
        $id = (int) $id;

        $row = $this->book->delete($id);

        if ((int) $row !== 1)
        {
            return $this->returnWithStatus('501 Not Implemented');
        }

        $this->db->trans_complete();

        return $this->returnWithStatus('200 OK');
    }

    /**
     * Функция выбирает данные из таблиц `genres` и `authors` для формирования формы добавления / изменения записи
     *
     * @return array
     */
    private function getInfoToForm()
    {

        $this->load->model('genre');
        $this->load->model('author');

        $genres = $this->genre->all();
        $authors = $this->author->all();
        $years = range(1950, 2017);

        return ['genres' => $genres, 'authors' => $authors, 'years' => $years];
    }

    /**
     * Функция обрабатывает входящие данные массива POST для добавления / обновления записи
     * Пустые или числовые вместо строковых переменные удаляются из массива
     * Для строковых переменных $author и $genre возвращает их $id
     * Ищет записи в талицах или создает новые записи. В случае неудачи транзакция откатывается и записи удаляются
     *
     * @param array $data[string $title, string $author, string $genre, int $year]
     *
     * @status
     * $year < 1950 || > 2017 - 417
     *
     * @return array $data
     */
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

    /**
     * Функция добавляет $body страницы к header и footer, выводит их
     *
     * @param string $body (view)
     * @param string $title
     */
    private function viewWithHeadFoot($body, $title = null)
    {
        $title = ($title === null) ? 'Библиотека' : $title;

        $output = $this->load->view('general/header', ['title' => $title], true) . $body;

        echo $output . $this->load->view('general/footer', '', true);
    }

    /**
     * Функция перенаправляет с корневого URL '/' на '/books'
     * GET /
     */
    public function redirectToResource()
    {
        header('Location: /books');
        exit();
    }

    /**
     * Функция добавляет статус к выводу страницы
     *
     * @param string $status
     * @return string (view)
     */
    private function returnWithStatus($status)
    {
        header('HTTP/1.1 ' . $status);
        $body = $this->load->view('book/status', ['status' => $status], true);
        return $this->viewWithHeadFoot($body, (int) $status);
    }


}