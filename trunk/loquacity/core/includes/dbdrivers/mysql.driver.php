<?php

class mysqldriver{
	
	/**
	 * Constructor. Builds the Driver Instance
	 * 
	 * @param object $db Reference to ADODB Instance
	 */
	function mysqldriver(&$db){
		$this->_db = $db;
	}
	/**
	 * Stores a backup of Loquacity's database
	 *
	 * @param filehandle $backup_fh
	 */
	function backup(&$backup_fh){
		// TODO only iterate over tables that start with TBL_PREFIX
		$tables = $this->_db->MetaTables('TABLES');
		foreach($tables as $tbl){
			$schema = $this->getSchema($table);
			if(strlen($schema) > 0){
				fwrite($backup_fh, $schema."\n");
				$this->getData($table, &$backup_fh);
			}
		}
	}
	
	/**
	 * Generates the schema necessary to create a database backup
	 *
	 * @param string $table The table schema needed
	 * @return string
	 */
	function getSchema($table){		
		$sql = 'SHOW CREATE TABLE `'.$table.'`;';
		$rs = $this->_db->Execute($sql);
		$drop = 'DROP TABLE `'.$table.'`;';
		return $drop . $rs->fields[1];
	}
	
	/**
	* Retrieve SQL Statements that will restore data for a table
	*
	* @param string $table Database table to dump
	* @param filehandle $fh Open filehandle to receive the data
	* @return void
	*/
	function getData($table, &$backup_fh){
		$sql = 'SELECT * FROM `'.$table.'`';
		$rs = $this->_db->Execute($sql);
		$max = $rs->FieldCount();
		while(!$rs->EOF){
			$stmt = 'INSERT INTO `'.$table.'` VALUES(';
			$vals = array();
			for($i = 0; $i< $max; $i++){
				$fld = $rs->FetchField($i);
				$type = $rs->MetaType($fld->type);
				if($type == 'L' || $type == 'N' || $type == 'I' || $type == 'R'){
					$stmt .= $rs->fields[$i];
				}
				else{
					$stmt .= $this->_db->qstr($rs->fields[$i], get_magic_quotes_gpc());
				}
				if($i !== $max -1){
					$stmt .=', ';
				}
			}
			$stmt .= ');';
			fwrite($backup_fh, $stmt."\n");
			$rs->MoveNext();
		}
	}
	
	/**
	* Retrieves the proper SQL file and replaces the tokens with the user's configuration
	* Uses result to create the database and populate it with the configuration and sample data
	* 
	* @param array $config Current blog configuration
	* @return bool True on success, False on failure
	*/
	function createDB($config){
		/* For MySQL there are three main versions to worry about:
         * 4.0, 4.1 and 5.0
         * 4.1+ has real charset support, while < 4.1 doesn't
         */
        $info = $this->db->ServerInfo();
        $charset = null;
        $rval = false;
        if((strstr($info['version'], '4.1') !== false) || (strstr($info['version'], '5.0') !== false)){
            //We have a database that supports charsets properly!
            $charset = ' CHARACTER SET utf8 COLLATE utf8_bin';
        }
        if(file_exists(LOQ_INSTALLER.'/sql/mysql.sql') && is_readable(LOQ_INSTALLER.'/sql/mysql.sql')){
            $sql = file_get_contents(LOQ_INSTALLER.'/sql/mysql.sql');
            $sql = str_replace('__pfx__', $config['table_prefix'], $sql);
            if($this->tablesDontExist($sql)){
                if(!is_null($charset)){
                    $sql = str_replace('__charset__', $charset, $sql);
                }
                foreach($config as $setting=>$value){
                    $sql = str_replace('__'.$setting.'__', str_replace("'", "\'", $value), $sql);
                }
                $sql = str_replace('__loq_version__', LOQ_CUR_VERSION, $sql);
                $statements = explode(';', $sql);
                foreach($statements as $line){
                    if(trim($line) !== ''){
                        $this->db->Execute($line);
                    }
                }
                $rval = true;
            }
            else{
                $this->errors[] = 'The database "'.htmlentities($_POST['db_database']).'" already contains tables with the same names as used by Loquacity. Perhaps you meant to select "upgrade" rather than "Fresh Install"';
            }
        }
        else{
            $this->errors[] = 'Unable to find the SQL file to create the database.';
        }
        return $rval;
	}
	
	function tablesDontExist($sql){
		$rval = true;
        $tables = '/CREATE\s+TABLE\s+IF\s+NOT\s+EXISTS\s+`(.*?)`/';
        $matches = array();
        preg_match_all($tables, $sql, $matches);
        if(count($matches) > 0){
            $db_tables = $this->db->MetaTables("TABLES");
            foreach($db_tables as $dt){
                    #echo "<pre>$dt[0]</pre>";
                    if(in_array($dt, $matches[1])){
                        $rval = false;
                    }
            }
        }
        return $rval;
	}
}
?>