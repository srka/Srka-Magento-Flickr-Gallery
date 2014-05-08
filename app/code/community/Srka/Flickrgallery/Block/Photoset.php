<?php
class Srka_Flickrgallery_Block_Photoset extends Srka_Flickrgallery_Block_View {

	private $_photoset_id, $_page_number, $_per_page, $_thumb_size_prefix, $_full_size_prefix, $_raw_photoset, $_custom, $_id_postfix;

	protected function _construct(){
		parent::_construct();

		$this->initPhotoset();
	}

	public function initPhotoset(){
		$this->_custom = false;
		$this->_photoset_id = $this->getPhotosetId();
		$this->_page_number = $this->getPageNumber();
		$this->_per_page = $this->getPerPage();
		$this->_thumb_size_prefix = $this->getThumbSizePrefix();
		$this->_full_size_prefix = $this->getFullSizePrefix();
	}

	public function initRenderPhotoset(){
		$custom_per_page = $this->getRequest()->getParam('per_page');
		$custom_thumb_size_prefix = $this->getRequest()->getParam('thumb_size_prefix');
		if(!empty($custom_per_page)) $this->setPerPage($custom_per_page);
		if(!empty($custom_thumb_size_prefix)) $this->setThumbSizePrefix($custom_thumb_size_prefix);
		$this->_raw_photoset = $this->getRawPhotoset($this->_photoset_id, $this->_page_number, $this->_per_page);
	}

	public function isCustom($is_custom = NULL){
		if($is_custom != NULL) $this->_custom = $is_custom;
		return $this->_custom;
	}


	public function setPhotosetId($photoset_id){
		$this->_photoset_id = $photoset_id;
		$this->_custom = true;
	}

	public function getPhotosetId(){
		if(isset($this->_photoset_id)) return $this->_photoset_id;

		$this->_photoset_id = $this->getRequest()->getParam('id');
		return $this->_photoset_id;
	}

	public function setPerPage($per_page){
		$this->_per_page = $per_page;
		$this->_custom = true;
	}

	public function getPerPage(){
		if(isset($this->_per_page)) return $this->_per_page;

		$this->_per_page = Mage::getStoreConfig('flickrgalleryconfig/photoset/photos_per_page');
		return $this->_per_page;
	}

	public function getThumbSizePrefix(){
		if(isset($this->_thumb_size_prefix)) return $this->_thumb_size_prefix;

		$this->_thumb_size_prefix = Mage::getStoreConfig('flickrgalleryconfig/photoset/thumbsize');
		if(!isset($this->_thumb_size_prefix) || empty($this->_thumb_size_prefix)) $this->_thumb_size_prefix = 't';
		return $this->_thumb_size_prefix;

	}

	public function setThumbSizePrefix($thumb_size_prefix){
		$this->_thumb_size_prefix = $thumb_size_prefix;
		$this->_custom = true;
	}

	public function getFullSizePrefix(){
		if(isset($this->_full_size_prefix)) return $this->_full_size_prefix;

		$this->_full_size_prefix = Mage::getStoreConfig('flickrgalleryconfig/photoset/fullsize');
		if(!isset($this->_full_size_prefix) || empty($this->_full_size_prefix)) $this->_full_size_prefix = 'o';
		return $this->_full_size_prefix;

	}

	public function showOriginal(){
		return (bool) (!isset($this->_full_size_prefix) || empty($this->_full_size_prefix) || $this->_full_size_prefix == 'o');
	}

	public function getPhotoSetNameFromID($setid){
		$result = '';
		$apikey = Mage::getStoreConfig('flickrgalleryconfig/general/apikey');
		$userid = Mage::getStoreConfig('flickrgalleryconfig/general/user');

		$cacheModel = Mage::getModel('flickrgallery/flickrgallery');
		$cacheCollection = $cacheModel->getCollection();
		$cacheData = $cacheCollection->getData();
		$hasCache = $cacheCollection->hasResponseType('getPhotoSetNameFromID-' . $setid);

		if(empty($cacheData) || $hasCache === false){
			$raw_photosets = file_get_contents("https://api.flickr.com/services/rest/?method=flickr.photosets.getList&api_key=$apikey&user_id=$userid&format=rest");
			$cacheModel->setResponseType('getPhotoSetNameFromID-' . $setid)
				->setContent($raw_photosets)
				->setCreatedTime(date('Y-m-d H:i:s'))
				->save();
		}elseif($hasCache !== false && $cacheCollection->cacheExpired('getPhotoSetNameFromID-' . $setid)){
			$raw_photosets = file_get_contents("https://api.flickr.com/services/rest/?method=flickr.photosets.getList&api_key=$apikey&user_id=$userid&format=rest");
			$cachedItem = $cacheModel->load($hasCache);
			$cachedItem->setResponseType('getPhotoSetNameFromID-' . $setid)
				->setContent($raw_photosets)
				->setCreatedTime(date('Y-m-d H:i:s'))
				->save();
		}else{
			$raw_photosets = $cacheModel->load($hasCache)->getContent();
		}

		$photosets_xml = new SimpleXMLElement($raw_photosets);
		if($photosets_xml['stat'] == 'ok'){
			foreach($photosets_xml->photosets->photoset as $photoset){
				if((string)$photoset['id'] == $setid){
					$result = $photoset->title;
					break;
				}
			}
		}
		return $result;
	}

	public function getPhotoSetFromID($setid){
		$result = '';
		$apikey = Mage::getStoreConfig('flickrgalleryconfig/general/apikey');
		$userid = Mage::getStoreConfig('flickrgalleryconfig/general/user');

		$cacheModel = Mage::getModel('flickrgallery/flickrgallery');
		$cacheCollection = $cacheModel->getCollection();
		$cacheData = $cacheCollection->getData();
		$hasCache = $cacheCollection->hasResponseType('getPhotoSetFromID-' . $setid);

		if(empty($cacheData) || $hasCache === false){
			$raw_photosets = file_get_contents("https://api.flickr.com/services/rest/?method=flickr.photosets.getList&api_key=$apikey&user_id=$userid&format=rest");
			$cacheModel->setResponseType('getPhotoSetFromID-' . $setid)
				->setContent($raw_photosets)
				->setCreatedTime(date('Y-m-d H:i:s'))
				->save();
		}elseif($hasCache !== false && $cacheCollection->cacheExpired('getPhotoSetFromID-' . $setid)){
			$raw_photosets = file_get_contents("https://api.flickr.com/services/rest/?method=flickr.photosets.getList&api_key=$apikey&user_id=$userid&format=rest");
			$cachedItem = $cacheModel->load($hasCache);
			$cachedItem->setResponseType('getPhotoSetFromID-' . $setid)
				->setContent($raw_photosets)
				->setCreatedTime(date('Y-m-d H:i:s'))
				->save();
		}else{
			$raw_photosets = $cacheModel->load($hasCache)->getContent();
		}

		$photosets_xml = new SimpleXMLElement($raw_photosets);
		if($photosets_xml['stat'] == 'ok'){
			foreach($photosets_xml->photosets->photoset as $photoset){
				if((string)$photoset['id'] == $setid){
					$result = array('id' => $photoset['id'], 'title' => $photoset->title, 'description' => $photoset->description , 'cover' => $photoset['primary'], 'photos' => $photoset['photos'], 'videos' => $photoset['videos'], 'server' => $photoset['server'], 'farm' => $photoset['farm'], 'secret' => $photoset['secret']);
					break;
				}
			}
		}
		return $result;
	}

	public function getMaxSizePhoto($photo_id){
		if(isset($photo_id) && !empty($photo_id)){
			$apikey = Mage::getStoreConfig('flickrgalleryconfig/general/apikey');

			$cacheModel = Mage::getModel('flickrgallery/flickrgallery');
			$cacheCollection = $cacheModel->getCollection();
			$cacheData = $cacheCollection->getData();
			$hasCache = $cacheCollection->hasResponseType('getMaxSizePhoto-' . $photo_id);

			if(empty($cacheData) || $hasCache === false){
				$raw_photo = file_get_contents("https://api.flickr.com/services/rest/?method=flickr.photos.getSizes&api_key=$apikey&photo_id=$photo_id&format=rest");
				$cacheModel->setResponseType('getMaxSizePhoto-' . $photo_id)
					->setContent($raw_photo)
					->setCreatedTime(date('Y-m-d H:i:s'))
					->save();
			}elseif($hasCache !== false && $cacheCollection->cacheExpired('getMaxSizePhoto-' . $photo_id)){
				$raw_photo = file_get_contents("https://api.flickr.com/services/rest/?method=flickr.photos.getSizes&api_key=$apikey&photo_id=$photo_id&format=rest");
				$cachedItem = $cacheModel->load($hasCache);
				$cachedItem->setResponseType('getMaxSizePhoto-' . $photo_id)
					->setContent($raw_photo)
					->setCreatedTime(date('Y-m-d H:i:s'))
					->save();
			}else{
				$raw_photo = $cacheModel->load($hasCache)->getContent();
			}

			$photo_xml = new SimpleXMLElement($raw_photo);
			if($photo_xml['stat'] == 'ok'){
				$sizes_xml = $photo_xml->sizes;
				$sizes_children = $sizes_xml->children();
				$sizes_children_last_index = count($sizes_xml->children()) - 1;
				$photo_max_size = $sizes_children[$sizes_children_last_index];
				$photo_max_size_url = $photo_max_size['source'];
				return $photo_max_size_url;
			}

		}else{
			return NULL;
		}
	}

	public function getRawPhotoset($setid, $page = 1, $per_page = 0){
		if(isset($this->_raw_photoset)) return $this->_raw_photoset;

		if(!isset($page) || $page == '') $page = 1;
		if(!isset($per_page) || $per_page == '') $per_page = 0;
		$apikey = Mage::getStoreConfig('flickrgalleryconfig/general/apikey');

		$cacheModel = Mage::getModel('flickrgallery/flickrgallery');
		$cacheCollection = $cacheModel->getCollection();
		$cacheData = $cacheCollection->getData();
		$hasCache = $cacheCollection->hasResponseType('getPhotoSet-' . $setid);

		if(empty($cacheData) || $hasCache === false){
			$raw_photoset = file_get_contents("https://api.flickr.com/services/rest/?method=flickr.photosets.getPhotos&api_key=$apikey&photoset_id=$setid&format=rest&extras=description");
			$cacheModel->setResponseType('getPhotoSet-' . $setid)
				->setContent($raw_photoset)
				->setCreatedTime(date('Y-m-d H:i:s'))
				->save();
			//Mage::log('Cache not used for getPhotoSet with ID = ' . $setid, NULL, 'flickrdb.log');
		}elseif($hasCache !== false && $cacheCollection->cacheExpired('getPhotoSet-' . $setid)){
			$raw_photoset = file_get_contents("https://api.flickr.com/services/rest/?method=flickr.photosets.getPhotos&api_key=$apikey&photoset_id=$setid&format=rest&extras=description");
			$cachedItem = $cacheModel->load($hasCache);
			$cachedItem->setResponseType('getPhotoSet-' . $setid)
				->setContent($raw_photoset)
				->setCreatedTime(date('Y-m-d H:i:s'))
				->save();
			//Mage::log('Cache refreshed for getPhotoSet with ID = ' . $setid, NULL, 'flickrdb.log');
		}else{
			$raw_photoset = $cacheModel->load($hasCache)->getContent();
			//Mage::log('Cache used for getPhotoSet with ID = ' . $setid, NULL, 'flickrdb.log');
		}

		$_result = array();
		if(!empty($raw_photoset)){
			$photoset_xml = new SimpleXMLElement($raw_photoset);
			$photos = array();
			if($photoset_xml['stat'] == 'ok'){
				$_item_count = 0;
				foreach($photoset_xml->photoset->photo as $photo){
					$_item_count++;
					if( (($_item_count > ($page - 1) * $per_page) && ($_item_count <= ($page * $per_page))) || $per_page == 0 ){
						$max_size = $this->getMaxSizePhoto($photo['id']);
						$photos[] = array('id' => $photo['id'], 'title' => $photo['title'], 'server' => $photo['server'], 'farm' => $photo['farm'], 'secret' => $photo['secret'], 'maxsize' => $max_size, 'description' => $photo->description);
					}
				}

				$_result['photoset'] = $this->getPhotoSetFromID($setid);
				$_result['photos'] = $photos;
				$_result['total'] = $_item_count;
				$_result['pages'] = (!empty($per_page) && ($page != 0)) ? ceil($_item_count / $per_page) : 1;
			}

		}

		$this->_raw_photoset = $_result;
		return $this->_raw_photoset;
	}

	public function getPhotoset(){
		return (!empty($this->_raw_photoset) && is_array($this->_raw_photoset) && array_key_exists('photoset', $this->_raw_photoset)) ? $this->_raw_photoset['photoset'] : array();
	}

	public function getPhotos(){
		return (!empty($this->_raw_photoset) && is_array($this->_raw_photoset) && array_key_exists('photos', $this->_raw_photoset)) ? $this->_raw_photoset['photos'] : array();
	}

	public function getPhotosetTitle(){
		return (!empty($this->_raw_photoset) && is_array($this->_raw_photoset)) ? $this->_raw_photoset['photoset']['title'] : array();
	}

	public function getPhotosetDescription(){
		return (!empty($this->_raw_photoset) && is_array($this->_raw_photoset)) ? $this->_raw_photoset['photoset']['description'] : array();
	}

	public function getPhotoId($photo){
		return (!empty($photo) && is_array($photo) && array_key_exists('id', $photo)) ? $photo['id'] : '';
	}

	public function getPhotoTitle($photo){
		return (!empty($photo) && is_array($photo) && array_key_exists('title', $photo)) ? $photo['title'] : '';
	}

	public function getPhotoDescription($photo){
		return (!empty($photo) && is_array($photo) && array_key_exists('description', $photo)) ? $photo['description'] : '';
	}

	public function getOriginalSizeUrl($photo){
		return (!empty($photo) && is_array($photo) && array_key_exists('maxsize', $photo)) ? $photo['maxsize'] : '';
	}

	public function getFullSizeUrl($photo){
		return sprintf('https://farm%s.static.flickr.com/%s/%s_%s_%s.jpg', $photo['farm'], $photo['server'], $photo['id'], $photo['secret'], $this->_full_size_prefix);
	}

	public function getThumbSizeUrl($photo){
		return sprintf('https://farm%s.static.flickr.com/%s/%s_%s_%s.jpg', $photo['farm'], $photo['server'], $photo['id'], $photo['secret'], $this->_thumb_size_prefix);
	}

	public function getPhotoRel(){
		return (Mage::getStoreConfig('flickrgalleryconfig/lightbox/use_lightbox')) ? "lightbox[" . $this->getPhotosetTitle() . "]" : $this->getPhotosetTitle();
	}

	public function getPhotoTarget(){
		return (Mage::getStoreConfig('flickrgalleryconfig/lightbox/image_target')) ? 'target="_blank"' : '';
	}

	public function getPhotoClass(){
		return Mage::getStoreConfig('flickrgalleryconfig/lightbox/custom_class');
	}

	public function getPhotosetImageUrl($photoset){
		return sprintf('https://farm%s.static.flickr.com/%s/%s_%s_%s.jpg', $photoset['farm'], $photoset['server'], $photoset['cover'], $photoset['secret'], $this->_thumb_size_prefix);
	}

	public function getToolbarData(){
		$data['view']           = 'photoset';
		$data['total']          = $this->_raw_photoset['total'];
		$data['pages']          = $this->_raw_photoset['pages'];
		$data['page_number']    = $this->_page_number;
		$data['per_page']       = $this->_per_page;
		$data['photoset_id']    = (string) $this->_raw_photoset['photoset']['id'];
		$data['id_postfix']     = $this->_id_postfix;

		return $data;
	}

	public function getPhotosetRenderUrl(){
		if($this->_custom){
			return Mage::getUrl('gallery/index/rendercustomset/') . "id/$this->_photoset_id/page/$this->_page_number";
		}else{
			return Mage::getUrl('gallery/index/renderset/') . "id/$this->_photoset_id/page/$this->_page_number";
		}
	}

	public function generateIdPostfix($prefix = '_'){
		return ($this->_custom) ? $this->_id_postfix = $prefix . uniqid() : '';
	}

	public function setIdPostfix($id_postfix){
		$this->_id_postfix = $id_postfix;
	}

	public function getIdPostfix(){
		return $this->_id_postfix;
	}
}