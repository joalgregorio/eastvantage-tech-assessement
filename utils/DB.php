<?php

class DB
{
	private $pdo;

	private static $instance = null;

	private function __construct()
	{
		$config = require_once(ROOT . '/config/DevelopmentEnv.php');
		$dsn = $config['db_dsn'];
		$user = $config['db_user'];
		$password = $config['db_pass'];

		try {
			$this->pdo = new \PDO($dsn, $user, $password);
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $err) {
			#did not show error message as some log in information is shown in the message
			die("There was a problem connecting to the database.");
		}
	}

	public static function getInstance()
	{
		if (null === self::$instance) {
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance;
	}

	/**
	 * Prepare and execute a select statement
	 */
	public function select($table, $columns, $filters = null)
	{
		$filterColumns = [];
		$filterValues = [];
		$sql = "SELECT " . implode(', ', $columns) . " FROM " . $table; 
		if($filters) {
			foreach ($filters as $col => $val) {
				$filterColumns[] = "$col = ? ";
				$filterValues[] = $val;
			}
			if($filterColumns) {
				$sql .= " WHERE " . implode(" AND ", $filterColumns);
			}
		}

		$sth = $this->pdo->prepare($sql);

		if($filterValues) {
			foreach($filterValues as $ctr => $val) {
				$sth->bindValue($ctr+1, $val);
			}
		}
		
		try {
			$sth->execute();
			return $sth->fetchAll();
		} catch(PDOException $err) {
			echo "DB Error: " . $err->getMessage();
		}
	}

	/**
	 * Prepare and execute an insert statement
	 */
	public function insert($table, $newRow)
	{
		$columns = array_keys($newRow);
		$vals = array_values($newRow);

		$valColumns = implode(', ', array_fill(0, count($vals), '?'));
		$sql = "INSERT INTO " . $table . "(" . implode(', ', $columns) . ") VALUES ($valColumns)";
		$sth = $this->pdo->prepare($sql);

		foreach($vals as $ctr => $val) {
			$sth->bindValue($ctr+1, $val);
		}
		
		try {
			return $sth->execute();
		} catch(PDOException $err) {
			echo "DB Error: " . $err->getMessage();
		}
	} 

	/**
	 * Prepare and execute a delete statement
	 */
	
	public function delete($table, $filters)
	{
		$filterColumns = [];
		$filterValues = [];
		$sql = "DELETE FROM " . $table . " WHERE "; 
		if($filters) {
			foreach ($filters as $col => $val) {
				$filterColumns[] = "$col = ? ";
				$filterValues[] = $val;
			}
		}

		if($filterColumns) {
			$sql .= implode(" AND ", $filterColumns);
		}

		$sth = $this->pdo->prepare($sql);

		if($filterValues) {
			foreach($filterValues as $ctr => $val) {
				$sth->bindValue($ctr+1, $val);
			}
		}
		
		try {
			return $sth->execute();
		} catch(PDOException $err) {
			echo "DB Error: " . $err->getMessage();
		}
	}

}