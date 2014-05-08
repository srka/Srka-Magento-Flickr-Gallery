<?php
class Srka_Flickrgallery_Block_Photosets extends Srka_Flickrgallery_Block_View {

    private $_page_number, $_per_page, $_thumb_size_prefix, $_raw_photosets, $_id_postfix;

    protected function _construct(){
        parent::_construct();

        $this->_id_postfix = uniqid();
        $this->_page_number = $this->getPageNumber();
        $this->_per_page = $this->getPerPage();
        $this->_thumb_size_prefix = $this->getThumbSizePrefix();
        if($this->getRequest()->getActionName() == 'rendersets')
            $this->_raw_photosets = $this->getRawPhotosets($this->_page_number, $this->_per_page);
    }

    public function getPerPage(){
        if(isset($this->_per_page)) return $this->_per_page;

        $this->_per_page = Mage::getStoreConfig('flickrgalleryconfig/photosets/photosets_per_page');
        return $this->_per_page;
    }

    public function getThumbSizePrefix(){
        if(isset($this->_thumb_size_prefix)) return $this->_thumb_size_prefix;

        $this->_thumb_size_prefix = Mage::getStoreConfig('flickrgalleryconfig/photosets/thumbsize');
        if(!isset($this->_thumb_size_prefix) || empty($this->_thumb_size_prefix)) $this->_thumb_size_prefix = 'q';
        return $this->_thumb_size_prefix;
    }

    public function getRawPhotosets($page_number = 1, $per_page = 0){
        if(isset($this->_raw_photosets)) return $this->_raw_photosets;

        if(!isset($page_number) || $page_number == '') $page_number = 1;
        if(!isset($per_page) || $per_page == '') $per_page = 0;

        $apikey = Mage::getStoreConfig('flickrgalleryconfig/general/apikey');
        $userid = Mage::getStoreConfig('flickrgalleryconfig/general/user');

        $cacheModel = Mage::getModel('flickrgallery/flickrgallery');
        $cacheCollection = $cacheModel->getCollection();
        $cacheData = $cacheCollection->getData();
        $hasCache = $cacheCollection->hasResponseType('getPhotoSets');

        if(empty($cacheData) || $hasCache === false){
            $raw_photosets = file_get_contents("https://api.flickr.com/services/rest/?method=flickr.photosets.getList&api_key=$apikey&user_id=$userid&format=rest");
            $cacheModel->setResponseType('getPhotoSets')
                ->setContent($raw_photosets)
                ->setCreatedTime(date('Y-m-d H:i:s'))
                ->save();
        }elseif($hasCache !== false && $cacheCollection->cacheExpired('getPhotoSets')){
            $raw_photosets = file_get_contents("https://api.flickr.com/services/rest/?method=flickr.photosets.getList&api_key=$apikey&user_id=$userid&format=rest");
            $cachedItem = $cacheModel->load($hasCache);
            $cachedItem->setResponseType('getPhotoSets')
                ->setContent($raw_photosets)
                ->setCreatedTime(date('Y-m-d H:i:s'))
                ->save();
        }else{
            $raw_photosets = $cacheModel->load($hasCache)->getContent();
        }

        $photosets_xml = new SimpleXMLElement($raw_photosets);
        $photosets = array();
        $selected_sets = Mage::getStoreConfig('flickrgalleryconfig/general/photosets');
        if($selected_sets <> ''){
            $selected_sets_array = explode(',', $selected_sets);
            if($photosets_xml['stat'] == 'ok'){
                $_item_count = 0;
                foreach($photosets_xml->photosets->photoset as $photoset){
                    if(in_array($photoset['id'], $selected_sets_array)){
                        $_item_count++;
                        if( (($_item_count > ($page_number - 1) * $per_page) && ($_item_count <= ($page_number * $per_page))) || $per_page == 0 ){
                            $photosets[] = array('id' => $photoset['id'], 'title' => $photoset->title, 'description' => $photoset->description , 'cover' => $photoset['primary'], 'photos' => $photoset['photos'], 'videos' => $photoset['videos'], 'server' => $photoset['server'], 'farm' => $photoset['farm'], 'secret' => $photoset['secret']);
                        }
                    }
                }
                $_result['sets'] = $photosets;
                $_result['total'] = $_item_count;
                $_result['pages'] = (!empty($per_page) && ($page_number != 0)) ? ceil($_item_count / $per_page) : 1;
            }
        }

        $this->_raw_photosets = $_result;
        return $this->_raw_photosets;
    }

    public function getPhotosets(){
        return ( (array_key_exists('sets', $this->_raw_photosets)) ? $this->_raw_photosets['sets'] : array() );
    }

    public function getPhotosetId($photoset){
        return $photoset['id'];
    }

    public function getPhotosetTitle($photoset){
        return $photoset['title'];
    }

    public function getPhotosetDescription($photoset){
        return $photoset['description'];
    }

    public function getPhotosetUrl($photoset){
        return Mage::getUrl('gallery/set/id/') . $photoset['id'];
    }

    public function getPhotosetImageUrl($photoset){
        return sprintf('https://farm%s.static.flickr.com/%s/%s_%s_%s.jpg', $photoset['farm'], $photoset['server'], $photoset['cover'], $photoset['secret'], $this->_thumb_size_prefix);
    }

    public function getToolbarData(){
        $data['view']           = 'photosets';
        $data['total']          = $this->_raw_photosets['total'];
        $data['pages']          = $this->_raw_photosets['pages'];
        $data['page_number']    = $this->_page_number;
        $data['per_page']       = $this->_per_page;

        return $data;
    }

    public function getPhotosetsRenderUrl(){
        return Mage::getUrl('gallery/index/rendersets/') . "page/$this->_page_number";
    }

    public function getCarouselPhotosets(){
        $rawPhotosets = $this->getRawPhotosets();
        return (array_key_exists('sets', $rawPhotosets)) ? $rawPhotosets['sets'] : array();
    }

    public function getCarouselUrl(){
        return Mage::getUrl('gallery/index/rendercarousel/');
    }

    public function getCarouselOptions(){

        if(Mage::getStoreConfig('flickrgalleryconfig/carousel/duration')) $options['duration'] = Mage::getStoreConfig('flickrgalleryconfig/carousel/duration');
        $options['auto'] = (Mage::getStoreConfig('flickrgalleryconfig/carousel/auto')) ? 'true' : 'false';
        if(Mage::getStoreConfig('flickrgalleryconfig/carousel/frequency')) $options['frequency'] = Mage::getStoreConfig('flickrgalleryconfig/carousel/frequency');
        if(Mage::getStoreConfig('flickrgalleryconfig/carousel/visibleslides')){
            $options['visibleSlides'] = Mage::getStoreConfig('flickrgalleryconfig/carousel/visibleslides');
        } else {
            $options['visibleSlides'] = '1';
        }
        $options['circular'] = (Mage::getStoreConfig('flickrgalleryconfig/carousel/circular')) ? 'true' : 'false';
        $options['wheel'] = (Mage::getStoreConfig('flickrgalleryconfig/carousel/wheel')) ? 'true' : 'false';
        if(Mage::getStoreConfig('flickrgalleryconfig/carousel/transition')) $options['transition'] = "'" . Mage::getStoreConfig('flickrgalleryconfig/carousel/transition') . "'";

        $result = '';
        foreach($options as $name => $value){
            $result .= $name . ': ' . $value . ', ';
        }

        return $result;
    }

    public function isCarouselCircular(){
        return Mage::getStoreConfig('flickrgalleryconfig/carousel/circular');
    }

    public function getCarouselVisibleCount(){
        if(Mage::getStoreConfig('flickrgalleryconfig/carousel/visibleslides')){
            return Mage::getStoreConfig('flickrgalleryconfig/carousel/visibleslides');
        } else {
            return '1';
        }
    }

    public function getIdPostfix(){
        return $this->_id_postfix;
    }

}