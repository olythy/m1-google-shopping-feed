<?php

class Stuntcoders_GoogleShopping_Model_Observer
{
    use Stuntcoders_GoogleShopping_Model_HelperAccess;
    use Stuntcoders_GoogleShopping_Model_ModelAccess;

    public function setFeeds()
    {
        $feeds = $this->model()->getCollection();

        foreach ($feeds as $feed) {
            try{
                $this->helper()->toFile($feed);
            } catch (Exception $e){
                Mage::logException($e);
            }
        }
    }
}