<?php

namespace App\Model;

/**
 * Persistent link category <--> goods.
 */
class CategoryGoods extends \Nette\Database\Table\Selection {
	private $db;
	private $table = "category_goods";

	public function __construct(\Nette\Database\Context $database) {
		parent::__construct($database, $database->getConventions(), $this->table);
		$this->db = $database;
	}

}
