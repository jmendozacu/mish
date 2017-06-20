<?php
class Mirasvit_Kb_Helper_Tag extends Mage_Core_Helper_Abstract
{
    public function setTags($article, $tags)
    {
        if (is_string($tags)) {
            $tags = explode(',', $tags);
        }
        $tagIds = array();
        foreach ($tags as $tagName) {
            $tag = $this->getTag($tagName);
        	if (!$tag) {
                continue;
            }
            $tagIds[] = $tag->getId();
        }
        $article->setTagIds($tagIds);
    }

    public function getTag($tagName)
    {
        $tagName = trim($tagName);
        if (!$tagName) {
            return false;
        }
    	$collection = Mage::getModel('kb/tag')->getCollection()
    					->addFieldToFilter('name', $tagName);
    	if ($collection->count()) {
    		$tag = $collection->getFirstItem();
    	} else {
    		$tag = Mage::getModel('kb/tag')->setName($tagName)->save();
    	}
    	return $tag;
    }

    public function getListUrl()
    {
        return Mage::helper('mstcore/urlrewrite')->getUrl('KB', 'TAG_LIST');
    }
}