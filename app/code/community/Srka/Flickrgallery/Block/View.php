<?php
class Srka_Flickrgallery_Block_View extends Mage_Core_Block_Template {

    public function _prepareLayout(){
        return parent::_prepareLayout();
    }

    public function getPageNumber(){
        return $this->getRequest()->getParam('page');
    }

    public function canDisplay($photoset_id){
        $selected_sets = Mage::getStoreConfig('flickrgalleryconfig/general/photosets');
        $selected_sets_array = explode(',', $selected_sets);
        return in_array($photoset_id, $selected_sets_array);
    }

    public function carouselHasTooltip(){
        return Mage::getStoreConfig('flickrgalleryconfig/carousel/tooltip');
    }

    public function carouselGetTooltipStyle(){
        if($this->carouselHasTooltip()){
            return Mage::getStoreConfig('flickrgalleryconfig/carousel/tooltip_style');
        }else{
            return false;
        }
    }

    public function hasTooltip(){
        return Mage::getStoreConfig('flickrgalleryconfig/photoset/tooltip');
    }

    public function getTooltipStyle(){
        return ($this->hasTooltip()) ? Mage::getStoreConfig('flickrgalleryconfig/photoset/tooltip_style') : false;
    }

}