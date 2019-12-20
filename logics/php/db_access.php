<?php

ini_set('display_errors', 1);
#error_reporting(-1);

require_once __DIR__."/../../vendor/autoload.php";

$dotenv = Dotenv\Dotenv::create(__DIR__."/../..");
$dotenv->load(__DIR__."/../..");

class MySql { #元ネタ→ https://www.geek.sc/archives/458
    public function __construct(){ #DBの接続の際に使う情報を定義
        $this->dsn = 'mysql:host=my-rds.c7rekwisb6xy.ap-northeast-1.rds.amazonaws.com;dbname=real_time_chat;charset=utf8mb4';
        $this->id = $_ENV['DB_ID'];
        $this->pw = $_ENV['DB_PW'];
        $this->option = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
    }
    public function query($sql){
        try{
            $this->db = new PDO ($this->dsn, $this->id, $this->pw, $this->option);
            $this->sql = $this->db->query($sql);
            $this->data = $this->sql->fetchAll(PDO::FETCH_ASSOC); #クエリ結果を連想配列で取得して、
            $this->lastInsertId = $this->db->lastInsertId();
            return $this->data; #それを返り値にする。

        } catch (PDOException $err) {
            throw $err;
            exit();
        }
    }
    /**
    *function prepare($sql,...$prepares)
    *第一引数: SQL文
    *偶数番目の引数: プレースホルダの名前 
    *奇数番目の引数: プレースホルダにバインドする値
    */
    public function prepare($sql,...$prepares){
        if ( $this->preparesCount % 2 !== 0 ){ #可変長引数 $prepares の数が偶数じゃなかった場合、エラーを出す。 
            trigger_error( "Fatal Error: prepare() の引数は、必ず奇数個になります。第二引数以降を、プレースホルダとバインド値をセットで記入してください。", E_USER_ERROR );
        }#ここってもしかして、例外を投げたほうがいいかも。

        try{
            $this->db = new PDO ($this->dsn, $this->id, $this->pw, $this->option);

            $this->sql = $this->db->prepare($sql);
            for ($i = 1; $i <= $this->preparesCount; $i+=2) { #可変長引数 $prepares の値の1/2回分ループする。
                $this->sql->bindValue($prepares[$i-1],$prepares[$i]); #奇数番目の引数をプレースホルダとし、偶数番目の引数をバインド値とする。
            }
            $this->sql->execute();

            $this->lastInsertId = $this->db->lastInsertId();

            return $this->sql->fetchAll(PDO::FETCH_ASSOC); #クエリ結果を連想配列で取得して、それを返り値にする。

        } catch (PDOException $err) {
            throw $err;
            exit();
        }
    }

    public function sql($sql,...$prepares) {
        $this->preparesCount = count($prepares);
        if( $this->preparesCount < 1 ) {
            return $this->query($sql); # preparesの中身が0未満 (=プレースホルダが無い) なら、query() を実行。
        } else {
            return $this->prepare($sql,...$prepares); # preparesの中身が1つ以上 (=プレースホルダが有る) なら、prepare() を実行。
        }
    }
    public function lastInsertId(){
        return $this->lastInsertId;
    }

};