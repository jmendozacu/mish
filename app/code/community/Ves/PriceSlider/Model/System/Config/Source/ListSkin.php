<?php

class Ves_PriceSlider_Model_System_Config_Source_ListSkin
{
    public function toOptionArray()
    {

		return array( array("value"=>"", "label"=>"-- Select A Skin --"),
                                    array("value"=>"skinNice", "label"=>"Skin Nice"),
                                    array("value"=>"skinFlat", "label"=>"Skin Flat"),
                                    array("value"=>"skinHTML5", "label"=>"Skin HTML5"),
                                    array("value"=>"skinModern", "label"=>"Skin Modern"),
                                    array("value"=>"skinSimple", "label"=>"Skin Simple")
                                     );
    }
}