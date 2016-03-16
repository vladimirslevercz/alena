<?php

namespace App\FrontModule\Presenters;

use Nette,
    App\Model;

use App\Model\Article;
use App\Model\Category;
use Nette\Database\Context;

/**
 * Homepage presenter.
 */
class HomepagePresenter extends BasePresenter
{
	/**
	 * @var Model\Goods
	 * @inject
	 */
	public $goods;

	public function renderDefault()
	{
		$this->template->goodsRecommended = $this->goods->where('recommended != 0')->order('id DESC')->limit(6);
	}

}
