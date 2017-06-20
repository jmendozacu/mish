<?php
class Mirasvit_Kb_ArticleController extends Mage_Core_Controller_Front_Action
{
    protected function _getSession()
    {
        return Mage::getSingleton('catalog/session');
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    protected function _initArticle()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            // $article = Mage::getModel('kb/article')->getCollection()
            //     ->addFieldToFilter('main_table.article_id', $id)
            //     ->addFieldToFilter('main_table.is_active', true)
            //     ->getFirstItem();
            $article = Mage::getModel('kb/article')->load($id);
            if ($article->getId() > 0) {
                Mage::register('current_article', $article);
                return $article;
            }
        }
    }

    public function viewAction()
    {
        if ($this->_initArticle()) {
            $this->loadLayout();
            $this->_initLayoutMessages('catalog/session');
            $this->renderLayout();
        } else {
            $this->_forward('no_rote');
        }
    }

    public function voteAction()
    {
        if ($article = $this->_initArticle()) {
            if (!Mage::helper('kb')->getVoteResult($article)) {
                $vote = $this->getRequest()->getParam('vote');
                $article->addVote($vote)
                        ->save();
                Mage::helper('kb')->markAsVoted($article, $vote);
                $session  = $this->_getSession();
                $session->addSuccess($this->__('Thank you for your vote!'));
            }
            $refererUrl = $this->getRequest()->getServer('HTTP_REFERER');
            $this->_redirectUrl($refererUrl);
        } else {
            $this->_forward('no_rote');
        }
    }
    /**
     * search action
     * we don't use 'search' word, because of conflicts with 3rd party extensions
     */
    public function sAction()
    {
        if ($q = $this->getRequest()->getParam('s')) {
            Mage::register('search_query', $q);
            $this->loadLayout();
            $this->_initLayoutMessages('catalog/session');
            $this->renderLayout();
        } else {
            $this->_forward('no_rote');
        }
    }

    /************************/

}