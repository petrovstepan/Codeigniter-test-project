
<table class="responsive-table striped">

        <thead>
            <tr>
                <th>Название</th>
                <th>Автор</th>
                <th>Жанр</th>
                <th>Год</th>
            </tr>
        </thead>

        <tbody>
        <?php foreach ($books as $book): ?>
            <tr>
                <td><?=$book->title?></td> <td><?=$book->author?></td> <td><?=$book->genre?></td> <td><?=$book->year?></td>
                <td><a href="/books/<?=$book->id?>/edit">Редактировать</a></td> <td><a href="/books/<?=$book->id?>/delete">Удалить</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
</table>