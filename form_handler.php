<?
$post=$_POST; 
var_dump($_POST);

// Название <input type="file">
$input_name = 'picture';
var_dump($_FILES[$input_name]);
 
// Разрешенные расширения файлов.
$allow = array('img','bmp','jpg', 'jpge', 'png');
 
// Запрещенные расширения файлов.
$deny = array(
	'phtml', 'php', 'php3', 'php4', 'php5', 'php6', 'php7', 'phps', 'cgi', 'pl', 'asp', 
	'aspx', 'shtml', 'shtm', 'htaccess', 'htpasswd', 'ini', 'log', 'sh', 'js', 'html', 
	'htm', 'css', 'sql', 'spl', 'scgi', 'fcgi'
);
 
// Директория куда будут загружаться файлы.
$path = __DIR__ . '/uploads/';

if (isset($_FILES[$input_name])) {
	// Проверим директорию для загрузки.
	if (!is_dir($path)) { 
		mkdir($path, 0777, true); // создание директории
    }

	// Преобразуем массив $_FILES в удобный вид для перебора в foreach.
    $files = array();
    $diff = count($_FILES[$input_name], COUNT_RECURSIVE) - count($_FILES[$input_name]); // 15 - 5
	if ($diff == 0) {
		$files = array($_FILES[$input_name]);
	} else {
        // Так как $_FILES[$input_name] содержит массив, 
        // а каждый элемент представляет собой еще массив,
        // то прогоняем дважды, где заносим уже в будущий массив $files
        // пару [ключ] => [значение]
		foreach($_FILES[$input_name] as $k => $l) {
			foreach($l as $i => $v) {;
				$files[$i][$k] = $v;
			}
		}		
    }

    // Теперь прогоняем сам массив $files, 
    // в котором содержится вся информация о загруженных файлах
    // (название, тип, размер и т.д.)
    foreach ($files as $file) {
        $error = $success = '';
        // Проверим на ошибки загрузки.
		if (!empty($file['error']) || empty($file['tmp_name'])) {
			switch (@$file['error']) {
				case 1:
				case 2: $error = 'Превышен размер загружаемого файла.'; break;
				case 3: $error = 'Файл был получен только частично.'; break;
				case 4: $error = 'Файл не был загружен.'; break;
				case 6: $error = 'Файл не загружен - отсутствует временная директория.'; break;
				case 7: $error = 'Не удалось записать файл на диск.'; break;
				case 8: $error = 'PHP-расширение остановило загрузку файла.'; break;
				case 9: $error = 'Файл не был загружен - директория не существует.'; break;
				case 10: $error = 'Превышен максимально допустимый размер файла.'; break;
				case 11: $error = 'Данный тип файла запрещен.'; break;
				case 12: $error = 'Ошибка при копировании файла.'; break;
				default: $error = 'Файл не был загружен - неизвестная ошибка.'; break;
            }
        }
        elseif ($file['tmp_name'] == 'none' || !is_uploaded_file($file['tmp_name'])) {
			$error = 'Не удалось загрузить файл.';
        }
        else {
			// Оставляем в имени файла только буквы, цифры и некоторые символы.
			$pattern = "[^a-zа-яё0-9,~!@#%^-_\$\?\(\)\{\}\[\]\.]";
			$name = mb_eregi_replace($pattern, '-', $file['name']);
			$name = mb_ereg_replace('[-]+', '-', $name);
			
			// Т.к. есть проблема с кириллицей в названиях файлов (файлы становятся недоступны).
			// Сделаем их транслит:
			$converter = array(
				'а' => 'a',   'б' => 'b',   'в' => 'v',    'г' => 'g',   'д' => 'd',   'е' => 'e',
				'ё' => 'e',   'ж' => 'zh',  'з' => 'z',    'и' => 'i',   'й' => 'y',   'к' => 'k',
				'л' => 'l',   'м' => 'm',   'н' => 'n',    'о' => 'o',   'п' => 'p',   'р' => 'r',
				'с' => 's',   'т' => 't',   'у' => 'u',    'ф' => 'f',   'х' => 'h',   'ц' => 'c',
				'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',  'ь' => '',    'ы' => 'y',   'ъ' => '',
				'э' => 'e',   'ю' => 'yu',  'я' => 'ya', 
			
				'А' => 'A',   'Б' => 'B',   'В' => 'V',    'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
				'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',    'И' => 'I',   'Й' => 'Y',   'К' => 'K',
				'Л' => 'L',   'М' => 'M',   'Н' => 'N',    'О' => 'O',   'П' => 'P',   'Р' => 'R',
				'С' => 'S',   'Т' => 'T',   'У' => 'U',    'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
				'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',  'Ь' => '',    'Ы' => 'Y',   'Ъ' => '',
				'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
			);
            // заменяем в имени файла русские буквы (если были) латинскими
            $name = strtr($name, $converter);
            // pathinfo возвращает информацию о пути к файлу
            // в виде ассоциативного массива или строки 
            $parts = pathinfo($name);
            // если пустое имя файла или расширение extension
            if (empty($name) || empty($parts['extension'])) {
				$error = 'Пустое имя файла или не задано расширение';
            } 
            // Если не разрешенное расширение (массив $allow в начале)
            // и не разрешенный тип файла
            // strtolower преобразует строку в нижний регистр
            elseif (!empty($allow) && !in_array(strtolower($parts['extension']), $allow)) {
				$error = 'Недопустимый тип файла';
            } 
            // Если содержится не разрешенное расширение и оно содержится в extension
            elseif (!empty($deny) && in_array(strtolower($parts['extension']), $deny)) {
				$error = 'Файл с расширением'. $parts['extension']."загружать нельзя\n";
			} else {
				// Чтобы не затереть файл с таким же названием, добавим префикс.
				$i = 0;
				$prefix = '';
				while (is_file($path . $parts['filename'] . $prefix . '.' . $parts['extension'])) {
                    // к существующим файлам с одинаковым названием будет добавление единички  
                    $prefix = '(' . ++$i . ')'; 
				}
				$name = $parts['filename'] . $prefix . '.' . $parts['extension'];
 
				// Перемещаем файл в директорию.
				if (move_uploaded_file($file['tmp_name'], $path . $name)) {
					// Далее можно сохранить название файла в БД и т.п.
					$success = 'Файл «' . $name . '» успешно загружен.';
				} else {
					$error = 'Не удалось загрузить файл.';
				}
			}
        }
        // Выводим сообщение о результате загрузки.
		if (!empty($success)) {
			echo '<p>' . $success . '</p>';		
		} else {
			echo '<p>' . $error . '</p>';
		}
    }
}

// Дома пробуем загрузить несколько типов файлов
// [.png, r, png, e.txt, f.jpg]
// проверять на тип, размер, 
// выводить информацию об успешно загруженных файлов,
// выводить информацию какой файл и по какой причине не был загружен
// размер идет в байтах всегда

// 1. проверка на тип (types)
// 2. проверка на размер (size)
// 3. получить разрешение файла 
   // pathinfo($file_name, PATHINFO_EXTENSION);
// 4. изменить имя (name) файла
// 5. переместить из временной папки в папку, где хранятся
// файлы move_uploaded_file

// error
// https://www.php.net/manual/ru/features.file-upload.errors.php
