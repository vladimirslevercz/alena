<?php

namespace App\FrontModule\Presenters;

use Nette,
	App\Model;

use App\Model\Sale;
use App\Model\Goods;
use Nette\Database\Context;

/**
 * Sale presenter.
 */
class SalePresenter extends BasePresenter
{

	const SALE_PAGE_MENU = "sale";

	/**
	 * @var Model\Sale
	 * @inject
	 */
	public $sale;

	/**
	 * @var Model\Article
	 * @inject
	 */
	public $article;

	/**
	 * @var Model\Goods
	 * @inject
	 */
	public $goods;

	public function renderDefault() {
		$currentDate = date('Y-m-d H:i:s');
		// povolene akce s aktualnim datem platnosti serazene od nejnovejsi
		$this->template->sales = $this->sale->where("enable != 0 AND ((start IS NULL AND end IS NULL) OR (start < '$currentDate' AND end > '$currentDate') OR (start IS NULL AND end > '$currentDate') OR (start < '$currentDate' AND end IS NULL))")->order('created DESC');
		$this->template->goodsRecommended = $this->goods->where('recommended != 0')->order('id DESC')->limit(6);
		$this->template->text = "";
		foreach($this->article->where(['menu' => self::SALE_PAGE_MENU])->limit(1) as $a) {
			if ( strip_tags($a->text) ) {
				$this->template->text = $a->text;
			}
		}
	}

}
