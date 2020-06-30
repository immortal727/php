<?
require_once 'categories.php';
require_once 'functions.php';
$result=get_cat($categories);
// var_dump($result);
view_cat($result);
