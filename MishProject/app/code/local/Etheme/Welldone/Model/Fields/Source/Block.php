<?php

class Etheme_Welldone_Model_Fields_Source_Block
{
    protected $_options;

    public function toOptionArray()
    {
        if(!$this -> _options)
        {
            $this    -> _options = array(
                array(
                    'value'    => 0,
                    'label'    => Mage::helper('catalog') -> __('Please select static block ...'),
                )
            );

            $options = Mage::getResourceModel('cms/block_collection') -> load() -> toOptionArray();
            $this -> _options = array_merge($this -> _options, $options);
        }

        return $this->_options;
    }
}