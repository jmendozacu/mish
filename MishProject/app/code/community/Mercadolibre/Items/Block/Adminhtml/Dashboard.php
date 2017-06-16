<?php
class Mercadolibre_Items_Block_Adminhtml_Dashboard extends Mage_Core_Block_Template
{
    protected $_locale;

    /**
     * Location of the "Enable Chart" config param
     */
    const XML_PATH_ENABLE_CHARTS = 'admin/dashboard/enable_charts';

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('items/dashboard/index.phtml');

    }

    protected function _prepareLayout()
    {
       /* $this->setChild('lastOrders',
                $this->getLayout()->createBlock('items/adminhtml_dashboard_orders_grid')
        );
		
		$this->setChild('latestQuestions',
                $this->getLayout()->createBlock('items/adminhtml_dashboard_questions_grid')
        );*/

        $this->setChild('totals',
                $this->getLayout()->createBlock('items/adminhtml_dashboard_totals')
        );
		$this->setChild('questionstatus',
                $this->getLayout()->createBlock('items/adminhtml_dashboard_questionstatus')
        );
		
		$this->setChild('orderstatus',
                $this->getLayout()->createBlock('items/adminhtml_dashboard_orderstatus')
        );

        /*$this->setChild('sales',
                $this->getLayout()->createBlock('items/adminhtml_dashboard_sales')
        );

        $this->setChild('lastSearches',
                $this->getLayout()->createBlock('items/adminhtml_dashboard_searches_last')
        );

        $this->setChild('topSearches',
                $this->getLayout()->createBlock('adminhtml/dashboard_searches_top')
        );*/

        if (Mage::getStoreConfig(self::XML_PATH_ENABLE_CHARTS)) {
            $block = $this->getLayout()->createBlock('adminhtml/dashboard_diagrams');
        } else {
            $block = $this->getLayout()->createBlock('adminhtml/template')
                ->setTemplate('dashboard/graph/disabled.phtml')
                ->setConfigUrl($this->getUrl('adminhtml/system_config/edit', array('section'=>'admin')));
        }
        $this->setChild('diagrams', $block);

        $this->setChild('grids',
                $this->getLayout()->createBlock('adminhtml/dashboard_grids')
        );

        parent::_prepareLayout();
    }

    public function getSwitchUrl()
    {
        if ($url = $this->getData('switch_url')) {
            return $url;
        }
        return $this->getUrl('*/*/*', array('_current'=>true, 'period'=>null));
    }
}
