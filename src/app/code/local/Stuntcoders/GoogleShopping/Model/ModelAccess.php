<?php
/**
 * @copyright  Copyright (c) 2018 OlyThy <olythy@gmail.com>
 * @license    http://opensource.org/licenses/mit-license.php  The MIT License
 */

trait Stuntcoders_GoogleShopping_Model_ModelAccess
{
    /**
     * @param string $name
     * @return false|Mage_Core_Model_Abstract
     */
    public function model($name = 'feed')
    {
        return Mage::getModel("stuntcoders_googleshopping/{$name}");
    }
}