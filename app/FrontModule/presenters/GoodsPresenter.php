<?php

namespace App\FrontModule\Presenters;

use Nette,
	App\Model;


/**
 * Catalog presenter.
 */
class GoodsPresenter extends BasePresenter
{
	/**
	 * @var Model\Goods
	 * @inject
	 */
	public $goods;

	private $good;

	public function actionShow($id)
	{
		$this->good = $this->goods->get($id);
		if (!$this->good) {
			$this->flashMessage('Výrobek se nepodařilo načíst.', 'danger');
			$this->redirect(':Front:Catalog:default');
		}
	}

	/**
	 * @param $id GoodsId
	 */
	public function renderShow($id)
	{
		$this->template->good = $this->good;
	}
}