<?php 
$installer = $this;
$installer->startSetup();
$installer->run("
-- DROP TABLE IF EXISTS {$this->getTable('flickrgallery')};
CREATE TABLE {$this->getTable('flickrgallery')} (
  `flickrgallery_id` int(11) unsigned NOT NULL auto_increment,
  `response_type` varchar(255) NOT NULL default '',
  `content` text NULL,
  `created_time` datetime NULL,
  PRIMARY KEY (`flickrgallery_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
"); 
$installer->endSetup();