<?php
class VES_VendorsSellerList_Model_Observer {
    /**
     * get scope of system config data
     * @param $data
     * @return string
     */
    protected function _findScope($data)
    {
        if (is_null($data['store']) && $data['website']) {
            return $data['website'];
        } elseif ($data['store']) {
            return $data['store'];
        }
        return 'default';
    }

    public function admin_system_config_changed_section_vendors($observer)
    {
        $postData = Mage::app()->getRequest()->getPost();
        $layoutFields = $postData['groups']['sellers_list']['fields'];
        $config = Mage::getModel('core/config');
        $scope = $this->_findScope($observer->getEvent()->getData());
        foreach ($layoutFields as $key => $layoutField) {
            // seller list page
            if ($key == 'layout' && $layoutField['value'] == 'empty') {
                $config->saveConfig('vendors/sellers_list/layout/one_column', '0', $scope);
                $config->saveConfig('vendors/sellers_list/layout/empty', '1', $scope);
                $config->saveConfig('vendors/sellers_list/layout/two_columns_left', '0', $scope);
                $config->saveConfig('vendors/sellers_list/layout/two_columns_right', '0', $scope);
                $config->saveConfig('vendors/sellers_list/layout/three_columns', '0', $scope);
            }
            elseif ($key == 'layout' && $layoutField['value'] == 'one_column') {
                $config->saveConfig('vendors/sellers_list/layout/one_column', '1', $scope);
                $config->saveConfig('vendors/sellers_list/layout/empty', '0', $scope);
                $config->saveConfig('vendors/sellers_list/layout/two_columns_left', '0', $scope);
                $config->saveConfig('vendors/sellers_list/layout/two_columns_right', '0', $scope);
                $config->saveConfig('vendors/sellers_list/layout/three_columns', '0', $scope);
            }
            elseif ($key == 'layout' && $layoutField['value'] == 'two_columns_left') {
                $config->saveConfig('vendors/sellers_list/layout/one_column', '0', $scope);
                $config->saveConfig('vendors/sellers_list/layout/empty', '0', $scope);
                $config->saveConfig('vendors/sellers_list/layout/two_columns_left', '1', $scope);
                $config->saveConfig('vendors/sellers_list/layout/two_columns_right', '0', $scope);
                $config->saveConfig('vendors/sellers_list/layout/three_columns', '0', $scope);
            }
            elseif ($key == 'layout' && $layoutField['value'] == 'two_columns_right') {
                $config->saveConfig('vendors/sellers_list/layout/one_column', '0', $scope);
                $config->saveConfig('vendors/sellers_list/layout/empty', '0', $scope);
                $config->saveConfig('vendors/sellers_list/layout/two_columns_left', '0', $scope);
                $config->saveConfig('vendors/sellers_list/layout/two_columns_right', '1', $scope);
                $config->saveConfig('vendors/sellers_list/layout/three_columns', '0', $scope);
            }
            elseif ($key == 'layout' && $layoutField['value'] == 'three_columns') {
                $config->saveConfig('vendors/sellers_list/layout/one_column', '0', $scope);
                $config->saveConfig('vendors/sellers_list/layout/empty', '0', $scope);
                $config->saveConfig('vendors/sellers_list/layout/two_columns_left', '0', $scope);
                $config->saveConfig('vendors/sellers_list/layout/two_columns_right', '0', $scope);
                $config->saveConfig('vendors/sellers_list/layout/three_columns', '1', $scope);
            }
        }
    }
}