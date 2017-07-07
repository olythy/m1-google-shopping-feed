<?php

class Stuntcoders_GoogleShopping_Model_Observer
{
    public function setFeeds()
    {
        Mage::log('---->START', Zend_Log::INFO, 'Stuntcoders_Googleshopping.log');
        $feeds = Mage::getModel('stuntcoders_googleshopping/feed')->getCollection();

        foreach ($feeds as $feed) {
            //$feedModel = Mage::getModel('stuntcoders_googleshopping/feed')->load($feed->getId());
            try{
                $file = new Varien_Io_File();
                $file->mkdir(dirname($feed->getPath()), 755, true);
                if($file->fileExists($feed->getPath())){
                    if($file->isWriteable($feed->getPath())){
                        $file->filePutContent('',$feed->getPath());
                    }
                }
            } catch (Exception $e){
                Mage::log('Error creating local xml file', Zend_Log::ERR, 'Stuntcoders_Googleshopping_Error.log');
            }
            $file->write($feed->getPath(), $feed->generateXml());
        }
        Mage::log('---->STOP', Zend_Log::INFO, 'Stuntcoders_Googleshopping.log');
    }
}