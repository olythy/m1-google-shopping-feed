<?php

class Stuntcoders_GoogleShopping_Adminhtml_GoogleshoppingController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/config/stuntcoders_googleshopping');
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function addAction()
    {
        $feedId = $this->getRequest()->getParam('id');
        if ($feedId) {
            $feed = Mage::getModel('stuntcoders_googleshopping/feed')->load($feedId);
            Mage::register('stuntcoders_googleshopping_feed', $feed);
        }

        $this->loadLayout();
        return $this->renderLayout();
    }

    public function saveAction()
    {
        $feed = Mage::getModel('stuntcoders_googleshopping/feed');


         $array_store_request = $this->getRequest()->getParam('stores');
        if(isset($array_store_request)) {
            if(in_array('0',$array_store_request)){
                $data_store = '0';
            }
            else{
                $data_store = implode(",", $array_store_request);
            }
            //unset($data['stores']);
        }


        $feed->addData(array(
            'id' => $this->getRequest()->getParam('id'),
            'path' => $this->getRequest()->getParam('path'),
            'title' => $this->getRequest()->getParam('title'),
            'description' => $this->getRequest()->getParam('description'),
            'categories' => implode(',', $this->getRequest()->getParam('categories')),
            'attributes' => $this->getRequest()->getParam('attributes'),
            'stores' => $data_store
        ));


        $errors = $feed->validate();
        if (!empty($errors)) {
            foreach ($errors as $error) {
                Mage::getSingleton('core/session')->addError($error);
            }

            return $this->_redirect('*/*/add');
        }

        $feed->save();

        return $this->_redirect('*/*/index');
    }

    public function deleteAction()
    {
        $feedId = $this->getRequest()->getParam('id');
        if ($feedId) {
            Mage::getModel('stuntcoders_googleshopping/feed')->setId($feedId)->delete();
            Mage::getSingleton('core/session')->addSuccess($this->__('Feed successfully deleted'));
        }

        return $this->_redirect('*/*/index');
    }

    public function generatexmlAction()
    {
        $feedId = $this->getRequest()->getParam('id');
        $feed = Mage::getModel('stuntcoders_googleshopping/feed')->load($feedId);

        $file = new Varien_Io_File();
        $file->mkdir(dirname($feed->getPath()), 755, true);
        if($file->fileExists($feed->getPath())){
            if($file->isWriteable($feed->getPath())){
                $file->filePutContent('',$feed->getPath());
            }
        }
        $file->write($feed->getPath(), $feed->generateXml());

        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

        Mage::getSingleton('core/session')->addSuccess($this->__('Google feed successfully generated'));

        return $this->_redirectReferer('*/*/index');
    }
}