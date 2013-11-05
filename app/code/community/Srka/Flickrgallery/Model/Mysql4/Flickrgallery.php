<?php 
class Srka_Flickrgallery_Model_Mysql4_Flickrgallery extends Mage_Core_Model_Mysql4_Abstract{

	public function _construct(){   
		$this->_init('flickrgallery/flickrgallery', 'flickrgallery_id');
	}

}