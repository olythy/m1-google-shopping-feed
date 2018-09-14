<?php

class Stuntcoders_GoogleShopping_Block_Add_Form extends Mage_Adminhtml_Block_Widget_Form
{

    use Stuntcoders_GoogleShopping_Model_HelperAccess;

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'name' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ));

        $data = array();
        if (Mage::registry('stuntcoders_googleshopping_feed')) {
            $data = Mage::registry('stuntcoders_googleshopping_feed')->getData();
        }

        $fieldset = $form->addFieldset('googleshopping_form', array(
            'legend' => $this->helper()->__('Google Shopping Feed')
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('stores', 'select', array(
                'name' => 'stores[]',
                'label' => $this->helper()->__('Store View'),
                'title' => $this->helper()->__('Store View'),
                'required' => true,
                'values' => Mage::getSingleton('adminhtml/system_store')
                    ->getStoreValuesForForm(false, true),
            ));
        }
        else {
            $fieldset->addField('stores', 'hidden', array(
                'name' => 'stores[]',
                'value' => Mage::app()->getStore(true)->getId()
            ));
        }

        $fieldset->addField('path', 'text', array(
            'label' => $this->helper()->__('Path for Feed .xml'),
            'name' => 'path',
            'required'  => true,
        ));

        $fieldset->addField('title', 'text', array(
            'label' => $this->helper()->__('Feed Title'),
            'name' => 'title',
            'required'  => true,
        ));

        $fieldset->addField('description', 'textarea', array(
            'label' => $this->helper()->__('Feed Description'),
            'name' => 'description',
            'required'  => true,
        ));

        $fieldset->addField('categories', 'multiselect', array(
            'label' => $this->helper()->__('Feed Categories'),
            'name' => 'categories',
            'values' => $this->helper()->getCategoriesOptions(),
            'required'  => true,
        ));

        $fieldset->addField('attributes', 'textarea', array(
            'label' => $this->helper()->__('Feed Attributes'),
            'name' => 'attributes',
            'style' => 'width:500px; height:500px',
        ));

        $form->setUseContainer(true);
        $this->setForm($form);
        $form->setValues($data);
        return parent::_prepareForm();
    }
}