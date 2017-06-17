<?PHP
class Eisbehr_QrMage_Model_Config_Image
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'P', 	'label' => 'PNG (default)'),
			array('value' => 'J', 	'label' => 'JPG'),
        );
    }
}
