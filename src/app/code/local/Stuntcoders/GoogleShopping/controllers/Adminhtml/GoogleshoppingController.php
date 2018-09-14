<?php


class Stuntcoders_GoogleShopping_Adminhtml_GoogleshoppingController extends Mage_Adminhtml_Controller_Action
{
    use Stuntcoders_GoogleShopping_Model_ModelAccess;
    use Stuntcoders_GoogleShopping_Model_HelperAccess;
    use Stuntcoders_GoogleShopping_Model_SessionAccess;

    protected function _isAllowed()
    {
        return $this->session('admin')->isAllowed('system/config/stuntcoders_googleshopping');
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function addAction()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $feed = $this->model()->load($id);
            Mage::register('stuntcoders_googleshopping_feed', $feed);
        }

        $this->loadLayout();
        $this->renderLayout();
    }

    public function saveAction()
    {
        $data = $this->getRequest()->getParams();

        /** @var Stuntcoders_GoogleShopping_Model_Feed $feed */
        $feed = $this->model()->addData($data);

        $errors = collect($feed->validate() ?: [])->each(function ($error) {
            $this->session()->addError($error);
        });

        if ($errors->count()) {
            return $this->_redirect('*/*/add', ['id' => $feed->getId()]);
        }

        try {
            $feed->save();
            $this->session()->addSuccess($this->helper()->__('The feed has been saved.'));
        } catch (Exception $e) {
            $this->session()->addError($e->getMessage());
        }

        if ($this->getRequest()->getParam('back')) {
            $this->_redirect('*/*/add', ['id' => $feed->getId()]);
        } else {
            $this->_redirect('*/*/index');
        }
    }

    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $this->model()->setId($id)->delete();
            $this->session()->addSuccess($this->__('Feed successfully deleted'));
        }

        $this->_redirect('*/*/index');
    }

    public function generateXmlAction()
    {
        $feedId = $this->getRequest()->getParam('id');

        /** @var Stuntcoders_GoogleShopping_Model_Feed $feed */
        $feed = Mage::getModel('stuntcoders_googleshopping/feed')->load($feedId);

        $this->helper()->toFile($feed);
        $this->session()->addSuccess($this->__('Google feed successfully generated'));

        $this->_redirect('*/*/index');


//        $file = new Varien_Io_File();
//        $file->mkdir(dirname($feed->getPath()), 755, true);
//        if($file->fileExists($feed->getPath())){
//            if($file->isWriteable($feed->getPath())){
//                $file->filePutContent('',$feed->getPath());
//            }
//        }
//        $file->write($feed->getPath(), $feed->generateXml());

//        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
//
    }

}