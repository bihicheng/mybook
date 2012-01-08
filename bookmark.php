#!/usr/bin/env php
<?php
namespace mybook;

abstract class StoreEngine {

    protected $input;
    protected $books = array();

    public function __construct (){
        $this->setInput();
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
    protected $where = './';
    protected $file;

    function __construct(){
        parent::__construct();
        $this->setPath();
    }
    protected function setPath(){
        $this->file = $this->where . date('Ymd', strtotime('now')) . '.txt';
    }

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

    //const table_mybook = "";

    private static $table_mybook = "
        create table mybook(
                    id integer primary key,
                    name text not null collate nocase,
                    describe text not null default 'TOO LAZY TO NOTHING',
                    email text not null default 'bihicheng@qq.com' collate nocase,
                    unique(name))";

    public function store(){
        try{
                $db = new \PDO('sqlite:mybook');
                $ds = $db->query(self::$table_mybook);
                //create table;
                if(! $re = $db->query("select * from mybook limit 1")){
                    $table_mybook = $db->query(self::$table_mybook);
                }

                foreach ($this->books as $k => $v) {
                    $d = $db->query("insert into mybook (name) values ('$v');");
                }

                foreach ($db->query("select * from mybook") as $row) {
                    print($row['id']) . "\t";
                    print($row['name']) . "\t";
                    print($row['describe']) . "\t";
                    print($row['email']) . "\t\n";
                }
            } catch(PDOException $e){
                trigger_error($e->getMessage());
            }
        }
}
class DB {
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

$de = new DBStore();
//$te = new TextStore();
BookMarker::instance($de);
BookMarker::run();
