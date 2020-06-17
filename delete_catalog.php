<?
RemoveDir('catalog');

function RemoveDir($path) {
    if(file_exists($path) && is_dir($path)) {
        $dirHandle = opendir($path);
        while (false !== ($file = readdir($dirHandle))){
            // исключаем папки с назварием '.' и '..'
            if ($file!='.' && $file!='..') {
                $tmpPath=$path.'/'.$file;
                chmod($tmpPath, 0777);
                // если папка
                if (is_dir($tmpPath)){  
                    RemoveDir($tmpPath);
                } 
                else{  
                    if(file_exists($tmpPath)){
                        // удаляем файл
                        unlink($tmpPath);
                    }
                }
            }
        }
        closedir($dirHandle); // закрытие директории
        // удаляем текущую папку
        if(file_exists($path)){
            rmdir($path);
        }
        else{
            echo "Удаляемой папки не существует или это файл!";
        }
    }
}

function removeDirectory($dir) {
    if ($objs = glob($dir."/*")) {
        foreach($objs as $obj) {
            is_dir($obj) ? removeDirectory($obj) : unlink($obj);
        }
    }
    rmdir($dir);
}
