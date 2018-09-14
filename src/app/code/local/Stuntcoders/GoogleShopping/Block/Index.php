<?php

class Stuntcoders_GoogleShopping_Block_Index extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    use Stuntcoders_GoogleShopping_Model_HelperAccess;

    protected function _prepareLayout()
    {
        $this->setChild('googleshopping.addnew',
            $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
                'label' => $this->helper()->__('Add New Feed'),
                'onclick' => "setLocation('".$this->getUrl('*/*/add')."')",
                'class' => 'add'
            ))
        );
    }
}
