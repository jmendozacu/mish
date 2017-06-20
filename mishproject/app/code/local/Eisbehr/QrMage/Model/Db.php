<?PHP
class Eisbehr_QrMage_Model_Db extends Varien_Object
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function insertEntry($url)
	{
		$this->cleanDb();
		
		$sql = Mage::getSingleton('core/resource')->getConnection('core_write');
		
		$time = time();
		$hash = md5($url . $time);
		
		$qry = "INSERT INTO
					eisbehr_qrmage_buffer
					(hash, time, url)
				VALUES
					('" . $hash . "', '" . $time . "', '" . $url . "')";
		
		$sql->query($qry);
		return $hash;		
	}
	
	public function getEntry($hash)
	{
		if( preg_match("/^[a-f0-9]{32}$/", $hash) )
		{
			$sql = Mage::getSingleton('core/resource')->getConnection('core_read');	
			
			$qry = "SELECT
						url as url
					FROM
						eisbehr_qrmage_buffer
					WHERE
						hash = '" . $hash . "'
					LIMIT 1";
			
			$data = $sql->fetchAll($qry);
			
			return $data[0]['url'];
		}
		
		return NULL;
	}
	
	public function cleanDb()
	{
		$sql = Mage::getSingleton('core/resource')->getConnection('core_write');
		
		// older then one hour
		$time = time() - 3600;
		
		$qry = "DELETE FROM
					eisbehr_qrmage_buffer
				WHERE
					time < '" . $time . "'";
		
		$sql->query($qry);
		
		return true;
	}
}
