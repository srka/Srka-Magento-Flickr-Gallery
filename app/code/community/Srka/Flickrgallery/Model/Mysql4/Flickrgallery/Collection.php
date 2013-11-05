<?php 
class Srka_Flickrgallery_Model_Mysql4_Flickrgallery_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract{

	public function _construct(){
		$this->_init('flickrgallery/flickrgallery');
	}
	
	public function hasResponseType($response_type){
		$this->addFilter('response_type', array('eq' => $response_type))->load();
		$result = $this->getData();
        return (empty($result) ? false : $result[0]['flickrgallery_id']);
	}
	
	public function cacheExpired($response_type){
		
		$cacheExpirationHours = Mage::getStoreConfig('flickrgalleryconfig/cache/timeout');
		$hasCache = $this->hasResponseType($response_type);
		if($hasCache !== false){
			$this->addFilter('flickrgallery_id', array('eq' => $hasCache))->load();
			$result = $this->getData();
			$item = $result[0];
			$itemTime = $item['created_time'];
			$cacheExpiration = strtotime("+$cacheExpirationHours hours", strtotime($itemTime));
			
			return (time() > $cacheExpiration);
		}else{
			return NULL;
		}

	}

}