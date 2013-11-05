<?php
class Srka_Flickrgallery_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction(){
        $this->setsAction();
    }

    // Photosets (Gallery)
    public function setsAction(){
        $this->loadLayout();
        $this->_initLayoutMessages('core/session');
        $this->renderLayout();
    }
    public function renderSetsAction(){
        echo $this->loadLayout()->getLayout()->getBlock('photosets')->getChildHtml('photosetsView');
    }

    // Photoset (Photos)
    public function setAction(){
        $this->loadLayout();
        $this->_initLayoutMessages('core/session');
        $this->renderLayout();
    }
    public function renderCustomSetAction(){
        $view = $this->loadLayout('flickrgallery_index_rendercustomset')->getLayout()->getBlock('photoset_custom_view');
        $view->setIdPostfix($this->getRequest()->getParam('id_postfix'));
        echo $view->toHtml();
    }

    // Carousle
    public function renderCarouselAction(){
        echo $this->getLayout()->createBlock('flickrgallery/photosets')->setTemplate('flickrgallery/carousel/view.phtml')->toHtml();
    }
    public function renderSetAction(){
        echo $this->loadLayout()->getLayout()->getBlock('photoset')->getChildHtml('photosetView');
    }
}