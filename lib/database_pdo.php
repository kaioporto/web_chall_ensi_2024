<?php
	
	//Defining http user if there is
	define('USERNAME', (isset($_SERVER['REMOTE_USER']))?$_SERVER['REMOTE_USER']:'not authenticated');
	
	class Database{
		protected $pdo;
		private $stmt;
		private $values;
		public $sql;
		private $debug = FALSE;

		public function __construct($file = 'config.ini'){
			if(!$settings = parse_ini_file($file, TRUE)) throw new Exception("Unable to open " . $file . ".");
			
			$dataSrcName = $settings['data_src_name']['driver'] . 
			':host=' . $settings['data_src_name']['host'] .
			((!empty($settings['data_src_name']['port'])) ? 
			(';port=' . $settings['data_src_name']['port']) : '') . ';dbname=' . $settings['data_src_name']['dbname'];

			try{
				$this->connect($dataSrcName, $settings['db_data']['user'], $settings['db_data']['pass']);
			}catch(Exception $e){
				if($this->debug){
					echo $e->getMessage();
				}
				throw new Exception("Unable to connect to database");
			}
		}

		protected function connect($dataSrcName, $user, $pass){
			$this->pdo = new PDO($dataSrcName, $user, $pass);
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}

		public function insert($table, $args){
			$this->sql = 'INSERT INTO ' . $table . ' (' . implode(', ', array_keys($args)) .')'; 
			$valuesfields = $this->formatValues($args, True);
			$this->sql .= ' VALUES ('. implode(', ', $valuesfields) .')';
			try{
				$this->stmt = $this->pdo->prepare($this->sql);
				$this->bindValues();
				$this->stmt->execute();
			}catch(Exception $e){
				return $e->getMessage();
			}
			if(isset($this->stmt) && $this->stmt->rowCount() != 0){
				return true;
			}
			return false;
		}

		protected function formatValues($values, $insert = False){
			if(!is_null($values)){
				$valuesfields = [];
				foreach($values as $key => $value){
					if($insert){ $valuesfields[] = "?"; }
					else{ $valuesfields[] = "{$key} = ?"; }
					$this->values[] = $value;
				}
			}
			return $valuesfields;
		}

		public function select_all($table, $columns = '*', $where = NULL, $limit = 0){
			if(is_array($columns)){
				$columns = implode(', ', $columns);
			}
			$this->sql = 'SELECT ' . $columns . ' FROM '. $table;
			if(!is_null($where)){
				$wherefields = $this->formatValues($where);
				$this->sql .= ' WHERE '. implode(' AND ', $wherefields);
			}
			$this->stmt = $this->pdo->prepare($this->sql);
			if(!is_null($where)){
				$this->bindValues();
			}
			try{
				$this->stmt->execute();
			}catch(Exception $e){
				$e->getMessage();
			}
			if($limit === 1){
				$result = $this->stmt->fetch(PDO::FETCH_ASSOC);
			}else{
				$result = $this->stmt->fetchAll(PDO::FETCH_ASSOC);
			}
			return $result;
		}

		public function select_columns($table, $columns, $where = NULL){
			return $this->select_all($table, $columns, $where, 1);
		}

		public function update($table, $columns, $where = NULL){
			$sets = $this->formatValues($columns);
			$this->sql = 'UPDATE ' . $table . ' SET ' . implode(', ', $sets);
			if(!is_null($where)){
				$wherefields = $this->formatValues($where);
				$this->sql .= ' WHERE '. implode(' AND ', $wherefields);
			}
			try{
				$this->stmt = $this->pdo->prepare($this->sql);
				$this->bindValues();
				$this->stmt->execute();
			}catch(Exception $e){
				$e->getMessage();
			}
			if(isset($this->stmt) && $this->stmt->rowCount() != 0){
				return true;
			}
			return false;
		}

		public function remove($table, $id){
			$this->sql = 'DELETE FROM ' . $table . ' WHERE ' . substr_replace($table, "", -1) . '_id = ?';
			$this->stmt = $this->pdo->prepare($this->sql);
			$this->stmt->bindValue(1, $id, PDO::PARAM_INT);
			try{
				$this->stmt->execute();
			}catch(Exception $e){
				$e->getMessage();
			}
			if(isset($this->stmt) && $this->stmt->rowCount() != 0){
				return true;
			}
			return false;
		}

		public function query($sql, $variables = [], $limit = 0) {
			$this->stmt = $this->pdo->prepare($sql);
			try{
				$result = $this->stmt->execute($variables);
			}catch(Exception $e){
				$e->getMessage();
			}
			if(strpos($sql, 'SELECT') !== false){
				if($limit != 0){
					$result = $this->stmt->fetch(PDO::FETCH_ASSOC);
				}
				$result = $this->stmt->fetchAll(PDO::FETCH_ASSOC);
			}
			return $result;
		}

		protected function bindValues(){
			if(is_array($this->values)){
				foreach($this->values as $i => $value){
					if(is_numeric($value) && intval($value) == $value){
						$type = PDO::PARAM_INT;
						$value = intval($value);
					}elseif(is_null($value) || $value === 'NULL'){
						$type = PDO::PARAM_NULL;
						$value = NULL;
					}elseif(is_bool($value)){
						$type = PDO::PARAM_BOOL;
					}else{$type = PDO::PARAM_STR;}
					$this->stmt->bindValue(intval($i + 1), $value, $type);
				}
				unset($this->values);
				$this->values = [];
			}
		}

		//generates random information
		//type = 1 -> boolean
		//$type = 2 -> number
		//$type = 3 -> network class A, B or C
		private function random_number($type){
			$_MAX = 255;
			if($type == 1){
				return (random_int(0, $_MAX) < ($_MAX/2) ? 1 : 0);
			}elseif($type == 2){
				return (random_int(0, $_MAX));
			}elseif($type == 3){
				$range_class = random_int(0, $_MAX);
				$value = 0;
				if($range_class < 128){
					$value = 8;
				}elseif($range_class >= 128 && $range_class < 192){
					$value = 16;
				}elseif($range_class >= 192 && $range_class <= 255){
					$value = 24;
				}
				return $value;
			}
		}
		/*seeding data to the database. debug function, dev environment, testing purposes*/
		//$table = table to seed
		//function generates fictional data based on $lines
		public function seedDatabase($table, $lines){
			//generating fictional data
			$params = array();
			$ip = $this->random_number(2).".".$this->random_number(2).".".$this->random_number(2).".".$this->random_number(2);
			if($table == "routers"){
				for($i=0; $i < $lines; $i++){
					if($this->random_number(1)){
						$query_type = "snmp";
					}else{ $query_type = "script"; }
					$params = ["router_id" => $i,
								"router_name" => "huawei_".$i,
								"router_ip" => $ip,
								"query_type" => $query_type];
					
					$result = $this->insert($table, $params);
					if(!$result){
						return false;
					}
				}
				return $result;
			}
			if($table == "vlans"){
				for($i=0; $i < $lines; $i++){
					$network_class = $this->random_number(3);
					$params += ["vlan_id" => $i,
								"vlan_name" => "rede_ufba_".$i,
								"netaddr_ip" => $ip . "/" . $network_class,
								"router_id" => ($i+1),
								"router_int" => "core_ufba_".$i];
				}
				//return $result;
			}
			if($table == "mac_history"){
				$mac = $this->random_number(2);
				for($i=0; $i < $lines; $i++){
					$params += ["ip" => $ip,
								"mac" => $mac,
								"vlan_id" => $i,
								"router_id" => $i+1];
				}
				//return $result;
			}
		}
	}

	try{
		global $db;
		$db = new Database();
	}
	catch(Exception $e){
		throw new Exception($e);
	}
?>