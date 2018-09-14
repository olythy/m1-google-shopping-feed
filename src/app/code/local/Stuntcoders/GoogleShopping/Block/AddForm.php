<?php

class Stuntcoders_GoogleShopping_Block_AddForm extends Mage_Adminhtml_Block_Widget_Form_Container
{
    use Stuntcoders_GoogleShopping_Model_HelperAccess;

    protected $_blockGroup = false;

    public function __construct()
    {
        parent::__construct();

        $feed = Mage::registry('stuntcoders_googleshopping_feed');

        $this->_headerText = $this->helper()->__('Google Shopping Feed Manager');
        $this->_addButton(
            'save_and_edit_button',
            array(
                'label'     => $this->helper()->__('Save and Continue Edit'),
                'onclick'   => 'saveAndContinueEdit()',
                'class'     => 'save'
            ),
            100
        );
        $this->_addButton(
            'generate_xml',
            array(
                'label'     => $this->helper()->__('Generate XML File'),
                'onclick'   => $this->_getGenerateXmlOnClickHandler($feed),
                'class'     => 'go'
            ),
            100
        );
        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/add/');
            }
        ";
    }

    protected function _prepareLayout()
    {
        $this->setChild('form', $this->getLayout()->createBlock('stuntcoders_googleshopping/add_form'));
        return parent::_prepareLayout();
    }

    public function getAddNewButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    public function getFormHtml()
    {
        return $this->getChildHtml('form');
    }

    protected function _getGenerateXmlOnClickHandler($feed)
    {
        return "setLocation('{$this->getUrl('*/*/generateXml', ['id' => $feed->getId()])}')";
    }
}