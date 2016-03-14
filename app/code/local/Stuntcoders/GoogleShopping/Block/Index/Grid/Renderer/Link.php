<?php

class Stuntcoders_GoogleShopping_Block_Index_Grid_Renderer_Link
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    public function render(Varien_Object $row)
    {
        $this->getColumn()->setActions(array(array(
            'url' => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . $row->getPath(),
            'caption' => Mage::helper('stuntcoders_googleshopping')->__($row->getPath()),
        )));

        return parent::render($row);
    }
}