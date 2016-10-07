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

	/**
	 * @var Model\CategoryGoods
	 * @inject
	 */
	public $categoryGoods;

	private $good;
	private $goodsSimilar;

	public function actionShow($id)
	{
		$this->good = $this->goods->get($id);
		if (!$this->good) {
			$this->flashMessage('Výrobek se nepodařilo načíst.', 'danger');
			$this->redirect(':Front:Catalog:default');
		}

		$goodsSimilar = array();
		$goodsSameManufacturer = $this->goods->createSelectionInstance()->where("manufacturer_id = ?", $this->good->manufacturer->id)->order('id DESC')->limit(2);
		$goodCG = $this->good->related('category_goods');
		$goodCategoryIds = array();
		foreach($goodCG as $cg) { $goodCategoryIds[] = $cg->category->id;}

		$goodsSimilarCG = $this->categoryGoods->createSelectionInstance()->where('category_id IN ?', $goodCategoryIds);
		foreach($goodsSimilarCG as $cg) {
			if ($cg->good->id == $this->good->id) continue;
			$goodsSimilar[] = $cg->good;
		}
		foreach($goodsSameManufacturer as $good) {
			if ($good->id == $this->good->id) continue;
			$goodsSimilar[] = $good;
		}

		$this->goodsSimilar = array_unique($goodsSimilar);

	}

	/**
	 * @param $id GoodsId
	 */
	public function renderShow($id)
	{
		$this->template->good = $this->good;
		$this->template->goodsSimilar = $this->goodsSimilar;

		$og = array();
		foreach ($this->good->related('photo')->limit(1) as $image) {
			$imageinfo = getimagesize("../www/content/photo/" . $image->id . ".big.jpg");
			$og['image:width'] = $imageinfo[0];
			$og['image:height'] = $imageinfo[1];
			$og['image:type'] = $imageinfo['mime'];
		}
		$og['title'] = $this->good->name . " - alena.cz";
		$og['description'] = substr(strip_tags($this->good->description), 0, 140) . "...";

		$this->template->og = $og;
	}
}