<?php
!defined('WEB_ROOT') && exit('Forbidden');

Class DBdriver {
	var $MysqliDB = 0;
	var $default_host;
	var $default_user;
	var $default_pwassword;
	var $default_name;
	var $default_charset;
	var $default_pconnect = 0;
	var $query_num = 0;
	var $default_lp = 1;

	function DBdriver($default_host,$default_port,$default_user,$default_pwassword,$default_name,$default_charset,$default_pconnect= '0',$default_lp='1'){

		$this->MysqliDBhost = $default_host;
		$this->MysqliDBport = $default_port;
		$this->MysqliDBuser = $default_user;
		$this->MysqliDBpw   = $default_pwassword;
		$this->MysqliDBname = $default_name;
		$this->MysqliDBcharset = $default_charset;
		$this->MysqliDBpconnect = $default_pconnect;
		$this->MysqliDBlp = & $default_lp;
              $this->MysqliDB = null;

		$this->connect();
	}


	function connect(){

		$this->MysqliDB = @mysqli_init();

		@mysqli_real_connect($this->MysqliDB,$this->MysqliDBhost, $this->MysqliDBuser, $this->MysqliDBpw, false, $this->MysqliDBport);

		if(@mysqli_errno($this->MysqliDB) != 0){
			$this->halt('Connect('.$this->MysqliDBpconnect.') to MySQL failed');
		}

		$serverinfo = mysqli_get_server_info($this->MysqliDB);

		if ($serverinfo > '4.1' && $this->MysqliDBcharset) {
			mysqli_query($this->MysqliDB, "SET character_set_connection=" . $this->MysqliDBcharset . ",character_set_results=" . $this->MysqliDBcharset . ",character_set_client=binary");
		}


		if ($serverinfo > '5.0') {
			mysqli_query($this->MysqliDB, "SET sql_mode=''");
		}

		if ($this->MysqliDBname && !@mysqli_select_db($this->MysqliDB, $this->MysqliDBname)) {
			$this->halt('Cannot use database');
		}
	}

	function close($linkid){
		return @mysqli_close($linkid);

	}

	function lock($table_name){
		return $this->query("LOCK TABLES ".$table_name." WRITE");
	}
	
	function unlock($table_name){
		return $this->query("UNLOCK $table_name");
	}

	function select_db($default_name){
		if (!@mysqli_select_db($this->MysqliDB,$default_name)) {
			$this->halt('Cannot use database');
		}
	}

	function server_info(){
		return mysqli_get_server_info($this->MysqliDB);
	}


	//getupdate
	function getupdate($SQL_1,$SQL_2,$SQL_3){
		$rt = $this->getone($SQL_1,'MYSQL_NUM');

		if (isset($rt[0])) {
			$this->update($SQL_2);
		} else {
			$this->update($SQL_3);
		}
	}


	function insert_id(){
		return $this->getvalue('SELECT LAST_INSERT_ID()');
	}

	//getvalue
	function getvalue($SQL,$result_type = MYSQL_NUM,$field=0){
		$query = $this->query($SQL);
		$rt =& $this->fetch_array($query,$result_type);
		return isset($rt[$field]) ? $rt[$field] : false;
	}

	//getone
	function getone($SQL,$result_type = MYSQL_ASSOC){
		$query = $this->query($SQL,'Q');
		$rt= & $this->fetch_array($query,$result_type);
		return $rt;
	}



	//update
	function update($SQL,$lp=1){
		if ($this->MysqliDBlp == 1 && $default_lp) {
			$tmpsql6 = substr($SQL,0,6);
			if (strtoupper($tmpsql6.'E')=='REPLACE') {
				$SQL = 'REPLACE LOW_PRIORITY'.substr($SQL,7);
			} else {
				$SQL = $tmpsql6.' LOW_PRIORITY'.substr($SQL,6);
			}
		}
		return $this->query($SQL,'U');
	}


	function query($SQL,$method = null,$error = true){

		$query = @mysqli_query($this->MysqliDB, $SQL, ($method ? MYSQLI_USE_RESULT : MYSQLI_STORE_RESULT));




		if (in_array(mysqli_errno($this->MysqliDB),array(2006, 2013)) && empty($query) && !defined('QUERY')) {
			define('QUERY',true); 
			@mysqli_close($this->MysqliDB);
			sleep(2);
			$this->connect();
			$query = $this->query($SQL);
		}


		if ($method != 'U') {
			$this->query_num++;
		}
		if(!$query && $error){
			$this->halt('Query Error: '.$SQL);
		}
		return $query;
	}



	function fetch_array($query, $result_type = ''){


			if($result_type == 'MYSQL_ASSOC'){
	                     return mysqli_fetch_assoc($query);

			} else if($result_type == 'MYSQLI_NUM'){

				return mysqli_fetch_row($query);

			} else {

				return mysqli_fetch_array($query);
			}
	}


	function checktable($tablename){

		if($tablename){

			if($this->num_rows($this->query("SHOW TABLES LIKE '{$tablename}'"))) {

				return true;

			} else {
				return false;
			}

		} else {
			return false;
		}
	}

	function affected_rows(){
		return mysqli_affected_rows($this->MysqliDB);
	}
	function num_rows($query){
		if (!is_bool($query)) {
			return mysqli_num_rows($query);
		}
		return 0;
	}
	function num_fields($query){
		return @mysqli_num_fields($query);
	}
	function escape_string($str){
		return mysqli_real_escape_string($this->MysqliDB, $str);
	}
	function free_result(){
		$void = func_get_args();
		foreach ($void as $query) {
			if ($query instanceof mysqli_result) {
				mysqli_free_result($query);
			}
		}
		unset($void);
	}


	function getall($query,$result_type = 'MYSQL_ASSOC'){

		if($query){

                     $query = $this->query($query);
			while($r = $this->fetch_array($query,$result_type)){

				$datadb[]=$r;
			}

			$this->free_result();

                     return $datadb;
		} else {

                     return null;
		}


	}


	function halt($msg=null){
		require_once(WEB_ROOT.'Model/mysqli_msg.php');
		new DBERROR($msg);
	}
	

}


?>