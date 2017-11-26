<html>

    <head>
        <link rel="stylesheet" type="text/css" href="/resources/css/materialize.css">
        <link rel="stylesheet" type="text/css" href="/resources/css/style.css">
        <script type="text/javascript" src="/resources/js/jquery-2.1.1.js"></script>
        <script type="text/javascript" src="/resources/js/materialize.js"></script>
        <script type="text/javascript" src="/resources/js/script.js"></script>
    </head>

    <body>
        <header>
            <div class="row">
                <div class="col s10 offset-s2">
                    <nav class="top-nav">
                        <div class="container">

                            <div class="nav-wrapper">
                                <a class="brand-logo"><?=$title ? : null?></a>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>

            <ul class="side-nav fixed">
                <li><a href="/books">Библиотека</a></li>
                <li><a href="/books/create">Добавить книгу</a></li>
            </ul>
        </header>

        <main>
        <div class="container">
            <div class="row">
                <div class="col s2"></div>
                <div class="col s10">

