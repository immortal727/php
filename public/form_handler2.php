<?
upload('picture', 'uploads/');
// Загрузка файлов 
function upload($name_input, $pach){  
    // $name_input - это свойство name в input  
    // $pach - директория куда будет сохранён загруженный файл 
    // директория загрузки должна иметь доступ 777 
    
    // Проверяет существует ли директория и возможно ли её открыть из этого скрипта 
    if(!opendir($pach)){
        return 'Директория сохранения файлов, указана неверно!';
    } 
    
    foreach ($_FILES as $value) {
        // название файла целиком
        $file_name=$value['name'];
       
        // тип файла
        $type = $value['type'];

        foreach($file_name as $key=> $file){
            // устанавливаем директорию загрузки файла 
            $uploadfile = $pach.basename($file); 
            // расширение
            $ext = pathinfo($uploadfile, PATHINFO_EXTENSION);
            // Временный путь на сервере
            $tmp_name = $value['tmp_name'][$key];
            
            if(!empty($file)){ 
                $error = $success = '';
                // Ограничения размера загружаемого файла 
                $value['error']=size(1024, $tmp_name, $value['size'], $error='');
                // Проверка на тип
                $value['error']=type($type[$key], $file, $error='');
                // Проверим на ошибки загрузки.
                if (!empty($value['error']) 
                    || empty($tmp_name)) {
                    switch (@$value['error']) {
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
                elseif ($tmp_name == 'none' || !is_uploaded_file($tmp_name)) {
                    $error = "Не удалось загрузить файл $file.\n";
                }
                else {
                    // pathinfo возвращает информацию о пути к файлу
                    // в виде ассоциативного массива или строки
                    $parts = pathinfo($file);
                    // если пустое имя файла или расширение extension
                    if (empty($file) || empty($parts['extension'])) {
                        $error = 'Пустое имя файла или не задано расширение';
                    }
                }

                if (empty($error)){
                    // Изменяем имя файла
                    $file = md5($file . microtime());
                    // Проверяем существует ли такой файл в директории
                    // если существует, то изменяем название файла
                    if(is_file($uploadfile)):
                        $file = md5($file . microtime());
                    endif;

                    // проверяет, является ли файл загруженным на сервер методом POST,
                    // если так, перемещает его в указанное место
                    if (move_uploaded_file($tmp_name, $uploadfile)) {
                        echo "Файл $file упешно загружен\n";
                    } else {
                        echo "Файл $file загрузить не удалось\n";
                    }
                }
                else{
                    echo $error;
                    return;
                } 
            }
        }
    }
}

function size($size, $tmp_name, $file_size, $error){
    if($file_size >= $size){
        $error=2;   
        return $error;
    } 
}
// Проверка на тип
function type($type, $file, $error=''){
    // Разрешенные расширения файлов.
    $allow = array('img','bmp','jpg', 'jpge', 'png');
    $parts = pathinfo( $file);
    // Если не разрешенное расширение (массив $allow в начале)
    // и не разрешенный тип файла
    // strtolower преобразует строку в нижний регистр
    if (!empty($allow) && !in_array(strtolower($parts['extension']), $allow)) {
        $error=11;
        return $error;
    }
}
