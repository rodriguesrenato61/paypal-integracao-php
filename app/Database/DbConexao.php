<?php

	namespace App\Database;

	abstract class DbConexao {
		
		private static $pdo;
		
		const ROW_OBJECT = "object";
		const ROW_ARRAY = "array";
		
		public static function getPdo(){
			if(!self::$pdo){
				$host = DB_HOST;
				$dbname = DB_NAME;
				$user = DB_USER;
				$password = DB_PASSWORD;
				
				self::$pdo = new \PDO("mysql:host={$host};dbname={$dbname};charset=utf8", $user, $password);
				self::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			}
			return self::$pdo;
		}

		public static function geraCodigoInsercao(){
			$codigo = rand(0, 100000);
			return strval(time()).strval($codigo);
		}
		
	}

?>

