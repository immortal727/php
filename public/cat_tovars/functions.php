<?
/* В новом массиве массивы, описывающие дочерние категории должны стать вложенными массивами в родительский массив-категорию по ключу children_elems. */
function get_cat($category){
    $new_arr=[];
    foreach ($category as $value) {
        foreach ($value as $key => $znach) {
            if(empty($new_arr[$value['parent_category']])):
                if($value['parent_category']===null):
                    $value['parent_category']=0;
                endif;
                $new_arr[$value['parent_category']]=array();
            endif;
        }
        $new_arr[$value['parent_category']][]=$value;
    }
    return $new_arr;
}

function view_cat($arr, $parent_id=0){
    // Условие выхода из рекурсии
    if(empty($arr[$parent_id])):
        return;
    endif;

    echo "<ul>";
    for($i=0; $i<count($arr[$parent_id]); $i++){
    ?>
        <li><a href="?category_id='<? echo $arr[$parent_id][$i]['id'];?>  &parent_id=<? echo $parent_id; ?>'">
        <? echo $arr[$parent_id][$i]['title'] ;?>
        </a>
        <? view_cat($arr,$arr[$parent_id][$i]['id']); ?>
        </li>
    <? } ?>
    </ul>
    <?
}
