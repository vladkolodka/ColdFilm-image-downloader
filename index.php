<?php
header("Content-Type: text/html; charset=utf-8");
ini_set('max_execution_time', 60*60);

require __DIR__ . '/vendor/autoload.php';
use PHPHtmlParser\Dom;

$hashes = [];
$files = scandir('img');
$files = array_splice($files, 2);

foreach ($files as $file)
    $hashes[] = md5(file_get_contents("img/$file"));

for($i = 1; $i <= 60; $i++) {
    $parser = new Dom();
    $parser->loadFromUrl("http://coldfilm.ru/news/?page$i");

    $elements = $parser->find('#allEntries div[id^=entry] img');

    foreach ($elements as $element) {
        $image = file_get_contents($element->src);
        $hash = md5($image);
        $file_name = mb_convert_encoding(preg_replace([
            '/\//', '/\:/', '/\\\/', '/\*/', '/\?/', '/\"/', '/\|/', '/\</', '/\>/'
        ], '', $element->title), "CP-1251");
        $extension = strrchr($element->src, '.');
        if(!in_array($hash, $hashes)){
            $hashes[] = $hash;
            echo translit($element->title) . ".$extension\n";
            file_put_contents('img/' . $file_name . ".$extension", $image);
        } else echo translit($element->title) . ".$extension EXISTS\n";
    }
}

function translit($str) {
    $rus = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
    $lat = array('A', 'B', 'V', 'G', 'D', 'E', 'E', 'Gh', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Sch', 'Y', 'Y', 'Y', 'E', 'Yu', 'Ya', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'gh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', 'y', 'y', 'y', 'e', 'yu', 'ya');
    return str_replace($rus, $lat, $str);
}