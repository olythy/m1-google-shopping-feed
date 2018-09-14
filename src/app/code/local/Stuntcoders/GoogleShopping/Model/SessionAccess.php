<?php
/**
 * @copyright  Copyright (c) 2018 OlyThy <olythy@gmail.com>
 * @license    http://opensource.org/licenses/mit-license.php  The MIT License
 */

trait Stuntcoders_GoogleShopping_Model_SessionAccess
{
    /**
     * @return Mage_Adminhtml_Model_Session
     */
    public function session($type = 'adminhtml')
    {
        return  Mage::getSingleton("{$type}/session");
    }
}