<?php

namespace App\Model;

/**
 * Persistent object manufacturer.
 */
class Sale extends \Nette\Database\Table\Selection {
	private $db;
	private $table = "sale";

	const CREATE_TABLE_SYNTAX = "CREATE TABLE `sale` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `goods_id` int(11) NOT NULL COMMENT 'zbozi',
		  `text` longtext COMMENT 'popis',
		  `color` varchar(16) DEFAULT NULL COMMENT 'barva textu',
		  `bgcolor` varchar(16) DEFAULT NULL COMMENT 'barva podkladu',
		  `border` varchar(16) DEFAULT NULL COMMENT 'Barva okraje',
		  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'datum vytvoreni',
		  `start` datetime DEFAULT NULL COMMENT 'Datum začátku akce.',
		  `end` datetime DEFAULT NULL COMMENT 'Datum konce akce.',
		  `enable` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'Viditelne',
		  PRIMARY KEY (`id`),
		  KEY `goods_id` (`goods_id`),
		  CONSTRAINT `sale_ibfk_1` FOREIGN KEY (`goods_id`) REFERENCES `goods` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
		) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;";

	public function __construct(\Nette\Database\Context $database) {
		parent::__construct($database, $database->getConventions(), $this->table);
		$this->db = $database;
	}

}
