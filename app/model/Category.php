<?php

namespace App\Model;

/**
 * Persistent object category.
 */
class Category extends \Nette\Database\Table\Selection {
    private $db;
    private $table = "category";

	const CREATE_SYNTAX = "CREATE TABLE `category` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `name` varchar(255) NOT NULL,
	  `visible` tinyint(4) NOT NULL DEFAULT '1',
	  `parent` int(11) DEFAULT NULL,
	  `description` longtext,
	  PRIMARY KEY (`id`),
	  KEY `parent` (`parent`),
	  CONSTRAINT `category_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `category` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
	) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8;";

	public function __construct(\Nette\Database\Context $database) {
		parent::__construct($database, $database->getConventions(), $this->table);
		$this->db = $database;
	}
    
}
