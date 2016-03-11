<?php

class Stuntcoders_GoogleShopping_Block_AddForm extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_headerText = Mage::helper('stuntcoders_googleshopping')->__('Google Shopping Feed Manager');;
        parent::__construct();
        $this->setTemplate('stuntcoders/googleshopping/add.phtml');
    }

    protected function _prepareLayout()
    {
        $this->setChild('googleshopping.savenew', $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label' =>  Mage::helper('stuntcoders_googleshopping')->__('Save'),
                'onclick' => "googleshopping_form.submit()",
                'class' => 'save'
            )));

        $feed = Mage::registry('stuntcoders_googleshopping_feed');
        if ($feed) {
            $this->setChild('googleshopping.delete', $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' =>  Mage::helper('stuntcoders_googleshopping')->__('Delete'),
                    'onclick' => $this->_getDeleteOnClickHandler($feed),
                    'class' => 'delete')
                ));

            $this->setChild('googleshopping.generate', $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' =>  Mage::helper('stuntcoders_googleshopping')->__('Generate XML File'),
                    'onclick' => $this->_getGenerateXmlOnClickHandler($feed),
                    'class' => 'generate'
                )));
        }

        $this->setChild('googleshopping_form', $this->getLayout()->createBlock('stuntcoders_googleshopping/add_form'));
    }

    public function getAddNewButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    public function getFormHtml()
    {
        return $this->getChildHtml('googleshopping_form');
    }

    protected function _getDeleteOnClickHandler($feed)
    {
        return "setLocation('" . $this->getUrl('*/*/delete', array('id' => $feed->getId())) . "')";
    }

    protected function _getGenerateXmlOnClickHandler($feed)
    {
        return "setLocation('" . $this->getUrl('*/*/generatexml', array('id' => $feed->getId())) . "')";
    }
}