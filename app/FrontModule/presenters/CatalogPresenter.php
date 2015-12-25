<?php

namespace App\FrontModule\Presenters;

use Nette,
	App\Model;

use App\Model\Article;
use App\Model\Category;
use Nette\Database\Context;

/**
 * Catalog presenter.
 */
class CatalogPresenter extends BasePresenter
{

	/**
	 * @var Model\Category
	 * @inject
	 */
	public $category;

	/**
	 * @var Model\Manufacturer
	 * @inject
	 */
	public $manufacturer;

	/**
	 * @var Model\Goods
	 * @inject
	 */
	public $good;

	public function beforeRender()
	{
		$this->template->categories = $this->category->where('parent IS NULL');
		$this->template->manufacturers = $this->manufacturer;
		$this->template->filterName = 'Doporučené';
		$this->template->goodsRecommended =  array();
		$this->template->goodsOther =  array();
	}

	public function renderDefault()
	{
		$this->template->goodsRecommended = $this->good->where('stock > 0 AND recommended != 0')->order('id DESC')->limit(6);
		$this->template->goodsOther = array();
		$this->template->filterName = 'Doporučené';
	}

	public function renderFilter($categoryId = null, $manufacturerId = null)
	{
		$this->template->goodsRecommended = array();
		$this->template->goodsOther = array();
		$this->template->selectedCategoryId = $categoryId;
		$this->template->selectedManufacturerId = $manufacturerId;

		if ($categoryId && $manufacturerId) {
			// TODO
		} elseif ($categoryId) {
			// TODO
		} else {
			$manufacturer = $this->manufacturer->get($manufacturerId);
			if (!$manufacturer) {
				$this->flashMessage('Výrobce nebyl nalezen.', 'warning');
			} else {
				$this->template->filterName = $manufacturer->name;
				$this->template->goodsRecommended = $this->good->where('manufacturer_id = ? AND recommended != 0', $manufacturerId)->order('id DESC');
				$this->template->goodsOther = $this->good->createSelectionInstance()->where('manufacturer_id = ? AND recommended = 0', $manufacturerId)->order('id DESC');

			}
		}


	}

}
