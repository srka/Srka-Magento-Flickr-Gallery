<?php
class Srka_Flickrgallery_Model_Observer extends Mage_Core_Model_Abstract {

    private $_block, $_module_name, $_action_name;

    public function _construct(){
        parent::_construct();

        $this->_block = new Srka_Flickrgallery_Block_View();
        $this->_module_name = $this->_block->getRequest()->getControllerModule();
        $this->_action_name = $this->_block->getRequest()->getActionName();
    }

    private function _isFlickrgallery(){
        return (bool) ($this->_module_name == 'Srka_Flickrgallery');
    }
    private function _isPhotosetsView(){
        return (bool) ($this->_isFlickrgallery() && ($this->_action_name == 'sets' || $this->_action_name == 'index' || $this->_action_name == 'rendersets'));
    }
    private function _isPhotosetView(){
        return (bool) ($this->_isFlickrgallery() && ($this->_action_name == 'set' || $this->_action_name == 'renderset'));
    }
    private function _hasLightbox(){
        return (bool) Mage::getStoreConfig('flickrgalleryconfig/lightbox/include_lightbox');
    }
    private function _hasTooltips(){
        return (bool) Mage::getStoreConfig('flickrgalleryconfig/photoset/tooltip_style');
    }

    public function addHandles($observer){
        $update = $observer->getEvent()->getLayout()->getUpdate();

        if($this->_isFlickrgallery()) $update->addHandle('flickrgallery');
        if($this->_isPhotosetsView()) $update->addHandle('flickrgallery_photosets');
        if($this->_isPhotosetView()){
            $update->addHandle('flickrgallery_photoset');
            if($this->_hasLightbox()) $update->addHandle('flickrgallery_lightbox');
            if($this->_hasTooltips()) $update->addHandle('flickrgallery_tooltips');
        }

        return $this;
    }

}