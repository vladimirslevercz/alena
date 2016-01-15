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

	/**
	 * @var \Nette\Database\Context
	 * @inject
	 */
 	public $database;

	public function beforeRender()
	{
		$this->template->categories = $this->category->where('parent IS NULL');
		$this->category->createSelectionInstance();
		$this->template->manufacturers = $this->manufacturer;
		$this->template->filterName = 'Doporučené';
		$this->template->goodsRecommended =  array();
		$this->template->goodsOther =  array();
		$this->template->selectedCategoryId = null;
		$this->template->selectedSubCategoryId = null;
		$this->template->selectedManufacturerId = null;
	}

	public function renderDefault()
	{
		$this->template->setFile(__DIR__.'/../templates/Catalog/filter.latte');
		$this->template->goodsRecommended = $this->good->where('recommended != 0')->order('id DESC')->limit(6);
		$this->template->goodsOther = array();
		$this->template->filterName = 'Doporučené';
	}

	public function renderFilter($categoryId = null, $manufacturerId = null)
	{
		$this->template->goodsRecommended = array();
		$this->template->goodsOther = array();
		$this->template->selectedCategoryId = $categoryId;
		$this->template->selectedSubcategoryId = $categoryId;
		$this->template->selectedManufacturerId = $manufacturerId;

		if ($categoryId && $manufacturerId) {
			// TODO
		} elseif ($categoryId) {
			$category = $this->category->createSelectionInstance()->get($categoryId);
			if (!$category) {
				$this->flashMessage('Kategorie nebyla nalezena.', 'warning');
			} else {
				$parentCategory = $category->parent ? $this->category->createSelectionInstance()->get($category->parent) : null;
				$this->template->filterName = ($parentCategory ? $parentCategory->name . ' - ' : '') . $category->name;

				$in = $category . ',';

				$subcategories = $category->related('category.parent');
				foreach ($subcategories as $subcategory) {
					$in .= $subcategory . ',';
				}

				$in = rtrim($in, ',');
				$sql = "SELECT g.id
						FROM category_goods cg
						JOIN goods g ON cg.`goods_id` = g.id
						JOIN category c ON cg.`category_id` = c.id
						WHERE cg.`category_id` IN ($in) AND g.recommended = ";


				$this->template->goodsRecommended = $this->good->createSelectionInstance()->where('id', $this->database->getConnection()->query($sql . "1 GROUP BY id")->fetchPairs());
				$this->template->goodsOther = $this->good->createSelectionInstance()->where('id', $this->database->getConnection()->query($sql . "0 GROUP BY id")->fetchPairs());
				if ($category->parent) {
					$this->template->selectedCategoryId = $category->parent;
					$this->template->selectedSubCategoryId = $categoryId;
				} else {
					$this->template->selectedCategoryId = $categoryId;
				}
			}

		} else {
			$manufacturer = $this->manufacturer->get($manufacturerId);
			if (!$manufacturer) {
				$this->flashMessage('Výrobce nebyl nalezen.', 'warning');
			} else {
				$this->template->filterName = $manufacturer->name;
				$this->template->goodsRecommended = $this->good->where('manufacturer_id = ? AND recommended != 0', $manufacturerId)->order('id DESC');
				$this->template->goodsOther = $this->good->createSelectionInstance()->where('manufacturer_id = ? AND recommended = 0', $manufacturerId)->order('id DESC');
				$this->template->selectedManufacturer = $manufacturerId;
			}
		}


	}

}
