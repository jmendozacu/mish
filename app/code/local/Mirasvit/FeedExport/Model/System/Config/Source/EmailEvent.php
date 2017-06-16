<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Advanced Product Feeds
 * @version   1.1.2
 * @build     629
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_FeedExport_Model_System_Config_Source_EmailEvent
{
    public function toOptionArray()
    {
       return array(
           array(
                'label' => '',
                'value' => ''
            ),
            array(
                'label' => __('Successfull Generation'),
                'value' => 'generation_success'
            ),
            array(
                'label' => __('Unsuccessful Generation'),
                'value' => 'generation_fail'
            ),
            array(
                'label' => __('Successfull Delivery'),
                'value' => 'delivery_success'
            ),
            array(
                'label' => __('Unsuccessful Delivery'),
                'value' => 'delivery_fail'
            ),
        );
    }
}