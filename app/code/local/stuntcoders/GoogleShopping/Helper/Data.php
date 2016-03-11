<?php

class Stuntcoders_GoogleShopping_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getCategoriesOptions()
    {
        $categories = Mage::getModel('catalog/category')->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToFilter('name', array('neq' => ''));

        $values = array();

        foreach ($categories as $category) {
            $values[] = array(
                'value' => $category->getId(),
                'label' => $category->getName(),
            );
        }

        return $values;
    }
}