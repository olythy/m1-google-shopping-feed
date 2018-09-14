<?php

/**
 * @copyright  Copyright (c) 2018 OlyThy <olythy@gmail.com>
 * @license    http://opensource.org/licenses/mit-license.php  The MIT License
 */

class Stuntcoders_GoogleShopping_Model_System_Config_Source_Category
{
    public function toOptionArray()
    {
        $collection = Mage::getResourceModel('catalog/category_collection');

        $collection->addAttributeToSelect('name')
            ->addFieldToFilter('path', array('neq' => '1'))
            ->setOrder('path')
            ->load();

        $options = array();

        foreach ($collection as $category) {
            $depth = count(explode('/', $category->getPath())) - 2;
            $indent = str_repeat(" - ", max($depth * 2, 0)) . ' ˪ ';
            $options[] = array(
                'label' => $indent . $category->getName(),
                'value' => $category->getId()
            );
        }

        return $options;
    }
}