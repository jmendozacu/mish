<?php
class Mirasvit_Kb_Block_Adminhtml_Article_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct ()
    {
        parent::__construct();
        $this->_objectId = 'article_id';
        $this->_controller = 'adminhtml_article';
        $this->_blockGroup = 'kb';


        $this->_updateButton('save', 'label', Mage::helper('kb')->__('Save'));
        $this->_updateButton('delete', 'label', Mage::helper('kb')->__('Delete'));


        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('kb')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action + 'back/edit/');
            }
        ";

        return $this;
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }

    public function getArticle()
    {
        if (Mage::registry('current_article') && Mage::registry('current_article')->getId()) {
            return Mage::registry('current_article');
        }
    }

    public function getHeaderText ()
    {
        if ($article = $this->getArticle()) {
            return Mage::helper('kb')->__("Edit Article '%s'", $this->htmlEscape($article->getName()));
        } else {
            return Mage::helper('kb')->__('Create New Article');
        }
    }

    /************************/

}