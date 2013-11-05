<?php
class Srka_Flickrgallery_Block_Toolbar extends Srka_Flickrgallery_Block_View {

    private $_toolbar_data, $_toolbar_page_number, $_toolbar_per_page, $_toolbar_pages, $_toolbar_total;
    private $_toolbar_items_from, $_toolbar_items_to, $_id_postfix;

    public function setToolbarData($data){
        $this->_toolbar_data = $data;
        $this->_toolbar_page_number = ($this->_toolbar_data['page_number']) ? $this->_toolbar_data['page_number'] : 1;
        $this->_toolbar_per_page = $this->_toolbar_data['per_page'];
        $this->_toolbar_pages = $this->_toolbar_data['pages'];
        $this->_toolbar_total = $this->_toolbar_data['total'];
        $this->_toolbar_items_from = ($this->_toolbar_page_number - 1) * $this->_toolbar_per_page + 1;
        $this->_toolbar_items_to = $this->_toolbar_page_number * $this->_toolbar_per_page;
        $this->_id_postfix = $this->_toolbar_data['id_postfix'];
    }

    public function getToolbarPageNumber(){
        return $this->_toolbar_page_number;
    }

    public function getToolbarPages(){
        return $this->_toolbar_pages;
    }

    public function getToolbarTotal(){
        return $this->_toolbar_total;
    }

    public function getToolbarItemsFrom(){
        return $this->_toolbar_items_from;
    }

    public function getToolbarItemsTo(){
        return $this->_toolbar_items_to;
    }

    public function getToolbarUrl($data, $is_ajax = false){ // $data can be type ('prev', 'next') or page number
        $base_url = '#';

        $view = $this->_toolbar_data['view'];
        if($view == 'photosets'){
            $base_url = Mage::getUrl("gallery/sets/page/");
        }elseif($view == 'photoset'){
            if($is_ajax){
                $base_url = Mage::getUrl() . "gallery/index/rendercustomset/id/" . $this->_toolbar_data['photoset_id'] . "/page/";
            }else{
                $base_url = Mage::getUrl() . "gallery/set/id/" . $this->_toolbar_data['photoset_id'] . "/page/";
            }
        }

        if(is_numeric($data)){
            $page_number = $data;
            return $base_url . $page_number;
        }else{
            $type = $data;
            if(strcasecmp($type, 'prev') == 0 || strcasecmp($type, 'previous') == 0){
                return $base_url . intval($this->_toolbar_page_number - 1);
            }elseif(strcasecmp($type, 'next') == 0){
                return $base_url . intval($this->_toolbar_page_number + 1);
            }
        }

        return '#';
    }

    public function getIdPostfix(){
        return $this->_id_postfix;
    }

}