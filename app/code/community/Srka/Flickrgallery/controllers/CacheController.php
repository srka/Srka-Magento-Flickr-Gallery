<?php
class Srka_Flickrgallery_CacheController extends Mage_Adminhtml_Controller_Action {

    public function indexAction(){
        $this->clearAction();
    }

    public function clearAction(){
        $cacheData = Mage::getModel('flickrgallery/flickrgallery')->getCollection();
        foreach($cacheData as $cacheItem){
            $cacheItem->delete();
        }
        $this->_getSession()->addSuccess(Mage::helper('adminhtml')->__("The Flickr API request cache storage has been flushed."));
        $this->_redirect('adminhtml/system_config/edit/section/flickrgalleryconfig');
    }

}