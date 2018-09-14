<?php

class Stuntcoders_GoogleShopping_Helper_Data extends Mage_Core_Helper_Abstract
{

    use Stuntcoders_GoogleShopping_Model_ModelAccess;

    public function getCategoriesOptions()
    {
        return $this->model('system_config_source_category')->toOptionArray();
    }

    public function toFile(Stuntcoders_GoogleShopping_Model_Feed $feed)
    {
        $fileName = implode('/', [MAGENTO_ROOT, $feed->getPath()]);
        $file = new Varien_Io_File();
        $file->mkdir(dirname($fileName), 0755, true);
        if ($file->fileExists($fileName) && $file->isWriteable($fileName)) {
            $file->filePutContent('', $fileName);
        }
        $file->write($fileName, $feed->generateXml());
    }
}