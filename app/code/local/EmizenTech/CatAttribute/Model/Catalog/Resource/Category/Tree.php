<?php
class EmizenTech_CatAttribute_Model_Catalog_Resource_Category_Tree extends Mage_Catalog_Model_Resource_Category_Tree
{
	/**
     * Enter description here...
     *
     * @param Mage_Catalog_Model_Resource_Category_Collection $collection
     * @param boolean $sorted
     * @param array $exclude
     * @param boolean $toLoad
     * @param boolean $onlyActive
     * @return Mage_Catalog_Model_Resource_Category_Tree
     */
    public function addCollectionData($collection = null, $sorted = false, $exclude = array(), $toLoad = true,
        $onlyActive = false)
    {
    	
        if (is_null($collection)) {
            $collection = $this->getCollection($sorted);
        } else {
            $this->setCollection($collection);
        }

        if (!is_array($exclude)) {
            $exclude = array($exclude);
        }

        $nodeIds = array();
        foreach ($this->getNodes() as $node) {
            if (!in_array($node->getId(), $exclude)) {
                $nodeIds[] = $node->getId();
            }
        }
        $collection->addIdFilter($nodeIds);
        if ($onlyActive) {

            $disabledIds = $this->_getDisabledIds($collection);
            if ($disabledIds) {
                $collection->addFieldToFilter('entity_id', array('nin' => $disabledIds));
            }
            $collection->addAttributeToFilter('is_active', 1);
            $collection->addAttributeToFilter('include_in_menu', array('0','1'));
        }

        if ($this->_joinUrlRewriteIntoCollection) {
            $collection->joinUrlRewrite();
            $this->_joinUrlRewriteIntoCollection = false;
        }

        if ($toLoad) {
            $collection->load();

            foreach ($collection as $category) {
                if ($this->getNodeById($category->getId())) {
                    $this->getNodeById($category->getId())
                        ->addData($category->getData());
                }
            }

            foreach ($this->getNodes() as $node) {
                if (!$collection->getItemById($node->getId()) && $node->getParent()) {
                    $this->removeNode($node);
                }
            }
        }

        return $this;
    }
}
?>