<!doctype html>

<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Загрузка файлов</title>
    <style>
        form div {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

<form method="post" action="form_handler.php" enctype="multipart/form-data">
    <!-- enctype="multipart/form-data" - атрибут необходим при загрузке файлов -->
    <div>
        <input name="title" type="text" placeholder="Название">
    </div>
    <div>
        <!-- для загрузки одного файла -->
        <!-- <input type="file" accept="image/*" name="picture"> -->

        <!-- для загрузки нескольких файлов -->
        <input type="file" accept="image/*" multiple name="picture[]">
    </div>

    <div>
        <input type="submit" value="Загрузить">
    </div>

</form>

</body>

</html>