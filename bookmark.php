#!/usr/bin/env php
<?php
namespace mybook;

abstract class StoreEngine {

    protected $input;
    protected $books = array();
    protected $where = './';
    protected $file;

    public function __construct (){
        $this->setInput();
        $this->setPath();
    }

    protected function setPath(){
        $this->file = $this->where . date('Ymd', strtotime('now')) . '.txt';
    }

    public function setInput(){
        $argv = isset($_SERVER['argv']) ? $_SERVER['argv'] : array();
        $this->input = $this->books = array_slice($argv, 1);

    }

    public function getInput(){
        return $this->input;
    }

    public function getBooks (){
        return $this->books;
    }

    abstract function store();
}

class TextStore extends StoreEngine
{
    public function store(){
        $fh = fopen($this->file, 'a+');
        foreach ($this->books as $k => $v) {
            if(!$rs = fwrite($fh, "$v\r\n", 2048))
                return $rs;
        }
        fclose($fh);
    }
}

class DBStore extends StoreEngine {

    public function store(){
    }
}

//数据
class BookMarker {

    static private $storeEngine;

    private function __construct(){
    }

    static function instance(StoreEngine $se){
        self::$storeEngine = $se;
    }

    static public function run(){
        self::$storeEngine->store();
    }
}

$te = new TextStore();
BookMarker::instance($te);
BookMarker::run();
