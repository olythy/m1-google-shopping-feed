<?php

class Stuntcoders_GoogleShopping_Model_Resource_Feed_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('stuntcoders_googleshopping/feed');
    }

    public function addStoreFilter($store, $withAdmin = true){

        if ($store instanceof Mage_Core_Model_Store) {
            $store = array($store->getId());
        }

        if (!is_array($store)) {
            $store = array($store);
        }

        $this->addFilter('stores', array('in' => $store));

        return $this;
    }

}