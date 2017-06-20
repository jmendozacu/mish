<?PHP
class Eisbehr_QrMage_Model_Config_Encoding
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'UTF-8', 	  	 'label' => 'UTF-8 (default)'),
			array('value' => 'Shift_JIS', 	 'label' => 'Shift_JIS'),
			array('value' => 'ISO-8859-1', 	 'label' => 'ISO-8859-1'),
        );
    }
}
