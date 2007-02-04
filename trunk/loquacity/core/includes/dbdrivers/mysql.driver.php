<?php

class mysqldriver{
	function mysqldriver(){
		//empty
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
}
?>