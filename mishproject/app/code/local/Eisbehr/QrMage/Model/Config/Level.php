<?PHP
class Eisbehr_QrMage_Model_Config_Level
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'L', 	'label' => 'L (default)'),
			array('value' => 'M', 	'label' => 'M'),
			array('value' => 'Q', 	'label' => 'Q'),
			array('value' => 'H',	'label' => 'H'),
        );
    }
}
