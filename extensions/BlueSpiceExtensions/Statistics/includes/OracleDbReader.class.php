<?php
/**
 * Reads data from database for Statistics for BlueSpice.
 *
 * Part of BlueSpice for MediaWiki
 *
 * @author     Markus Glaser <glaser@hallowelt.biz>
 * @author     Patric Wirth <wirth@hallowelt.biz>

 * @package    BlueSpice_Extensions
 * @subpackage Statistics
 * @copyright  Copyright (C) 2011 Hallo Welt! - Medienwerkstatt GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
 * @filesource
 */

/**
 * Reads data from database for Statistics for BlueSpice.
 * @package    BlueSpice_Extensions
 * @subpackage Statistics 
 */
class OracleDbReader extends StatsDataProvider {
	
	/**
	 * Database server host
	 * @var string 
	 */
	public $host;
	/**
	 * Database user
	 * @var string
	 */
	public $user;
	/**
	 * Database password
	 * @var string
	 */
	public $pass;
	/**
	 * Database name
	 * @var string
	 */
	public $db;
	/**
	 * Stores current database connection
	 * @var resource
	 */
	private $conn = false;

	/**
	 * Counts occurrences in a certain interval
	 * @param Interval $interval
	 * @return int Number of occurrences
	 */
	public function countInInterval( $interval, $listable=false ) {
		$count = 0;
		$this->connect();

		$sql = $this->match;

		$sql = str_replace("@period", "BETWEEN '".$interval->getStartTS("YmdHis")."' AND '".$interval->getEndTS("YmdHis")."' ", $sql);
		$sql = str_replace("@start", " '".$interval->getStartTS("YmdHis")."' ", $sql);
		$sql = str_replace("@end", " '".$interval->getEndTS("YmdHis")."' ", $sql); 

		$res = oci_parse($this->conn,$sql);
                oci_execute($res);
                
                $row = oci_fetch_array($res);
                
		return $row[0];
	}

    /**
	 * Counts number of unique values that match a specific criterium
	 * @param Interval $interval
	 * @return array List of unique values
	 */
	public function uniqueValues( $interval, $listable=true, $count=2 ) {
		$uniqueValues = array();
		if (!$listable) return $uniqueValues;

		$this->connect();

		$sql = $this->match;

		$sql = str_replace("@period", "BETWEEN '".$interval->getStartTS("YmdHis")."' AND '".$interval->getEndTS("YmdHis")."' ", $sql);
		$sql = str_replace("@start", " '".$interval->getStartTS("YmdHis")."' ", $sql);
		$sql = str_replace("@end", " '".$interval->getEndTS("YmdHis")."' ", $sql);
                
                $res = oci_parse($this->conn,$sql);
                oci_execute($res);
                
		while ($row = oci_fetch_array($res)) {
			$rowArr = array();
			for ($i=0; $i<$count; $i++ )
				$rowArr[] = $row[$i];
			$uniqueValues[] = $rowArr;
		}
                
		return $uniqueValues;
	}

	/**
	 * Connect to database. This method stores the connection and reuses it.
	 * @return resource Database connection
	 */
	private function connect()
	{
		if ($this->conn) return $this->conn;
		$this->conn = oci_connect($this->user, $this->pass, $this->host);
		return $this->conn;
	}

}