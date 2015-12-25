<?php

namespace App\Model;

/**
 * Persistent object manufacturer.
 */
class Goods extends \Nette\Database\Table\Selection {

	const CREATE_SYNTAX = "CREATE TABLE `goods` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `name` varchar(255) NOT NULL,
	  `category_id` int(11) DEFAULT NULL,
	  `description` longtext NOT NULL,
	  `stock` int(11) NOT NULL,
	  `recommended` tinyint(4) NOT NULL,
	  `manufacturer_id` int(11) DEFAULT NULL,
	  `price` int(11) NOT NULL DEFAULT '0',
	  `showPrice` tinyint(1) NOT NULL DEFAULT '0',
	  PRIMARY KEY (`id`),
	  KEY `category_id` (`category_id`),
	  KEY `manufcturer_id` (`manufacturer_id`),
	  CONSTRAINT `goods_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE SET NULL,
	  CONSTRAINT `goods_ibfk_2` FOREIGN KEY (`manufacturer_id`) REFERENCES `manufacturer` (`id`) ON DELETE SET NULL
	) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;";

	private $db;
    private $table = "goods";

	public function __construct(\Nette\Database\Context $database) {
		parent::__construct($database, $database->getConventions(), $this->table);
		$this->db = $database;
	}


}
