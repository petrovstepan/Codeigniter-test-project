
    <form action="<?=isset($book) ? "/books/{$book->id}" : '/books'?>" method="POST">
        <input type="text" name="title" value="<?=isset($book) ? $book->title : null?>" placeholder="Название">

        <input type="text" name="author" list="authors" value="<?=isset($book) ? $book->author : null?>" placeholder="Автор">
            <datalist id="authors">
                <?php foreach($authors as $author):?>
                    <option value="<?=$author->author?>"><?=$author->author?></option>
                <?php endforeach; ?>
            </datalist>

        <input type="text" name="genre" list="genres" value="<?=isset($book) ? $book->genre : null?>" placeholder="Жанр">
            <datalist id="genres">
                <?php foreach($genres as $genre):?>
                    <option value="<?=$genre->genre?>"><?=$genre->genre?></option>
                <?php endforeach; ?>
            </datalist>

        <div class="input-field col s12">
            <select name="year">
                <option value="">Выберите год</option>
            <?php foreach ($years as $year): ?>
                <option value="<?=$year?>" <?php if(isset($book)) {echo ((int) $year === (int) $book->year) ? 'selected' : null;}?>><?=$year?></option>
            <?php endforeach; ?>
            </select>
            <label>Выберите год</label>
        </div>

            <a class="btn submit"><?=isset($book) ? 'Обновить' : 'Добавить'?></a>
    </form>
