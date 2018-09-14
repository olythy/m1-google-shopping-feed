<?php
/**
 * @copyright  Copyright (c) 2018 OlyThy <olythy@gmail.com>
 * @license    http://opensource.org/licenses/mit-license.php  The MIT License
 */

trait Stuntcoders_GoogleShopping_Model_HelperAccess
{
    /**
     * @param string $name
     * @return Mage_Core_Helper_Abstract
     */
    protected function helper($name = null)
    {
        $name = $name ? "stuntcoders_googleshopping/{$name}" : 'stuntcoders_googleshopping';
        return Mage::helper($name);
    }
}