<?php

class VES_VendorsCms_Model_Template_Filter extends Varien_Filter_Template
{
	public function includeDirective($construction)
    {
        return 'does not support';
    }
    
    /**
     * Filter the string as template.
     *
     * @param string $value
     * @return string
     */
    public function filter($value)
    {
        // "depend" and "if" operands should be first
        foreach (array(
            self::CONSTRUCTION_DEPEND_PATTERN => 'dependDirective',
            self::CONSTRUCTION_IF_PATTERN     => 'ifDirective',
            ) as $pattern => $directive) {
            if (preg_match_all($pattern, $value, $constructions, PREG_SET_ORDER)) {
                foreach($constructions as $index => $construction) {
                    $replacedValue = '';
                    $callback = array($this, $directive);
                    if(!is_callable($callback)) {
                        continue;
                    }
                    try {
                        $replacedValue = call_user_func($callback, $construction);
                    } catch (Exception $e) {
                        throw $e;
                    }
                    $value = str_replace($construction[0], $replacedValue, $value);
                }
            }
        }

        if(preg_match_all(self::CONSTRUCTION_PATTERN, $value, $constructions, PREG_SET_ORDER)) {
        	$filters = Mage::getConfig()->getNode('global/ves_filter')->asArray();
            foreach($constructions as $index=>$construction) {
                $replacedValue = '';
                if(isset($filters[$construction[1]])){
                	$handle = Mage::getModel($filters[$construction[1]]['class']);
                	$callback = array($handle, $construction[1].'Directive');
	                if(!is_callable($callback)) {
	                    continue;
	                }
	                try {
	                    $replacedValue = call_user_func($callback, $construction);
	                } catch (Exception $e) {
	                    throw $e;
	                }
	                $value = str_replace($construction[0], $replacedValue, $value);
                }
            }
        }
        return $value;
    }
}