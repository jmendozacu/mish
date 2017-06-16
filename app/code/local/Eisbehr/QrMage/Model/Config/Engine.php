<?PHP
class Eisbehr_QrMage_Model_Config_Engine
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'google', 	  		'label' => 'Google (engine cloud based on google)'),
			array('value' => 'swetake', 	 	'label' => 'Swetake (engine local based on server)'),
        );
    }
}
