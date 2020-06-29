<? 
$post=$_POST;
$user_link = $post['link'];
$filename='short.txt';
match_check($filename, $user_link);

function match_check($filename, $user_link){
    $arr_data=file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $host_url=parse_url($user_link, PHP_URL_HOST);
    $patch_url=generate_string(parse_url($user_link, PHP_URL_PATH));
    // Формируем короткую ссылку
    $short_url=$host_url.'/'.$patch_url;
    // Формируем длинную ссылку
    $long_url="$user_link#$short_url";
    if (!file_get_contents($filename)):
        write_file($filename, $long_url);
    else:
        foreach ($arr_data as $user_link) {
            $short_array=explode('#',$long_url);
            // Если короткая ссылка уже есть в файле
            if(!empty($short_array)):
                foreach ($short_array as $value) {
                    echo $value."<br/>";
                    if($user_link===$value || $short_url===$value):
                    match_check($filename,$short_url);
                    else:
                        write_file($filename, $long_url);
                    endif;
                }
            endif;
        }
    endif;
    echo "Короткая ссылка $short_url"; 
}

function write_file($filename, $data){
    if(file_put_contents($filename, $data.PHP_EOL, FILE_APPEND | LOCK_EX)!==false){
        echo "Данные успешно записаны в файл $filename";
    }
}

// Генаратор символов
function generate_string($string, $strength = 7) {
    $input_length = strlen($string);
    $random_string = '';
    // Массив, в котором перечисляются всем символы, требующие замены или в данном случае (ниже) уничтожения из строки
    $excess = array(",", "/", "\\", ".", "-");
    for($i = 0; $i < $strength; $i++) {
       $random_character = $string[mt_rand(0, $input_length - 1)];
       $random_character = str_replace($excess, "", $random_character);
       $random_string .= $random_character;
    }
    return trim($random_string);
}
