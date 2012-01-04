#!/usr/bin/env php
<?php
array_shift($argv); $books = $argv;
$file = date('Ymd', strtotime('now')) . '.txt';
$fh = fopen($file, 'a+');

foreach ($books as $k => $v) {
    if(!$rs = fwrite($fh, "$v\r\n", 2048))
        return $rs;
}

fclose($fh);
//var_dump($fh);
//var_dump($file);
