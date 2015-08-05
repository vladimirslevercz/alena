<?php

namespace App\Model;

/**
 * Persistent object manufacturer.
 */
class Goods extends \Nette\Database\Table\Selection {
    private $db;
    private $table = "goods";

	public function __construct(\Nette\Database\Context $database) {
		parent::__construct($database, $database->getConventions(), $this->table);
		$this->db = $database;
	}
    
}
