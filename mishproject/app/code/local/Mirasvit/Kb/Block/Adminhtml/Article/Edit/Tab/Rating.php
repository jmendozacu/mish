<?php
class Mirasvit_Kb_Block_Adminhtml_Article_Edit_Tab_Rating extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form  = new Varien_Data_Form();
        $this->setForm($form);
        $article = Mage::registry('current_article');

        $fieldset = $form->addFieldset('rating_fieldset', array('legend'=> Mage::helper('kb')->__('Rating')));
        if ($article->getId()) {
            $fieldset->addField('article_id', 'hidden', array(
                'name'      => 'article_id',
                'value'     => $article->getId(),
            ));
        }
        $fieldset->addField('votes_num', 'text', array(
            'label'     => Mage::helper('kb')->__('Number of Votes'),
            'name'      => 'votes_num',
            'value'     => $article->getVotesNum(),

        ));
        $fieldset->addField('rating', 'text', array(
            'label'     => Mage::helper('kb')->__('Rating'),
            'name'      => 'rating',
            'value'     => round($article->getRating(), 1),

        ));
        return parent::_prepareForm();
    }
}