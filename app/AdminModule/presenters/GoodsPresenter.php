<?php

namespace App\AdminModule\Presenters;

use Nette,
	App\Model;

use Nette\Database\Context;
use Nette\Application\UI;


/**
 * Article edit presenter.
 */
class GoodsPresenter extends BasePresenter
{
	const PAGE_LIMIT = 50;

	/**
	 * @var Model\Goods
	 * @inject
	 */
	public $item;

	/**
	 * @var Model\Category
	 * @inject
	 */
	public $category;

	/**
	 * @var Model\CategoryGoods
	 * @inject
	 */
	public $categoryGoods;

	/**
	 * @var Model\Manufacturer
	 * @inject
	 */
	public $manufacturer;

	/**
	 * @persist
	 */
	public $page;

	/**
	 * @var Model\Photo
	 * @inject
	 */
	public $photo;

	/**
	 * Default page logic
	 * @param int $page
	 */
	public function actionDefault($page = 0) {
		if (!$this->page || $page) {
			$this->page = $page;
		}
	}

	/**
	 *  Default page render
	 */
	public function renderDefault()
	{
		$this->template->item = $this->item->order('manufacturer.name, name')->limit(self::PAGE_LIMIT, $this->page * self::PAGE_LIMIT);
		$this->template->page = $this->page;
		$this->template->search = '';
	}

	/**
	 * Search page logic
	 * @param $search
	 */
	public function actionSearch($search, $page = 0)
	{
		if (!$search) {
			$this->redirect('default');
		}
		if (!$this->page || $page) {
			$this->page = $page;
		}
	}

	/**
	 * Render search page
	 * @param $search
	 */
	public function renderSearch($search)
	{
		$this->setView('default');
		$this->template->item = $this->item->order('manufacturer.name, name')
			->where('manufacturer.name LIKE ? OR goods.name LIKE ?', "%$search%", "%$search%")
			->limit(self::PAGE_LIMIT, $this->page * self::PAGE_LIMIT);
		$this->template->page = $this->page;
		$this->template->search = $search;
	}

	/**
	 * @param $id
	 */
	public function actionEdit($id) {
		$item = $this->item->get($id);
		if(!$item) {
			$this->error('Data nebyla nalezena v databázi.', 404);
		}
		$defaults = $item->toArray();
		if (!$defaults['manufacturer_id']) {
			$defaults['manufacturer_id'] = '';
		}

		$defaults['category_id'] = $this->categoryGoods->where('goods_id = ?', $id)->fetchPairs(null, 'category_id');
		$this['itemForm']->setDefaults($defaults);
	}

	protected function createComponentItemForm()
	{
		$form = new UI\Form;

		$form->addText('name', 'Název výrobku:')
			->setRequired();

		$categories = $this->category->order('name')->fetchPairs('id', 'name');

		$manufacturers = $this->manufacturer->order('name')->fetchPairs('id', 'name');
		$manufacturers[null] = '--- Žádný výrobce ---';

		$form->addCheckboxList('category_id', 'Kategorie', $categories);
		$form['category_id']->setAttribute('class', 'small');

		$form->addSelect('manufacturer_id', 'Výrobce', $manufacturers)->setDefaultValue('');

		$form->addCheckbox('showPrice', 'Zobrazovat cenu?')
			->addCondition($form::EQUAL, TRUE)
				->toggle('price');

		$form->addText('price', 'Cena za ks včetně DPH')->setType('number')->setDefaultValue(0)
			->setOption('id', 'price')
			->addRule($form::RANGE, 'Cena nemůže být záporná.', array(0, 1000000000));


		$form->addCheckbox('recommended', 'Je zboží doporučené?');
		$form->addCheckbox('new', 'Je to novinka?');
		$form->addCheckbox('clearance_sale', 'Doprodej?');
		$form->addCheckbox('stock', 'Skladem?');
		$form->addCheckbox('order', 'Na objednávku?');


		$form->addUpload('photo', 'Přidat obrázek výrobku:');

		$form->addTextArea('description', 'Popis:')
			->setAttribute('class', 'tinyMCE');

		$form->addSubmit('save', 'Uložit')
			->setAttribute('class', 'btn btn-primary');

		$form->onSuccess[] = array($this, 'itemFormSucceeded');

		return $form;
	}


	public function itemFormSucceeded(UI\Form $form, $values)
	{

		if (isset($values['manufacturer_id']) && !$values['manufacturer_id'] ) {
			$values['manufacturer_id'] = $values['manufacturer_id'] ? $values['manufacturer_id'] : null;
		}
		foreach($values as $k => $v) {
			if ($k != 'category_id' && $k != 'photo') {
				$updateOrInsert[$k] = $v;
			}
		}

		$itemId = $this->getParameter('id');
		if ($itemId) {
			$item = $this->item->get($itemId);
			if (!$item) {
				$this->error('Data nebyla nalezena v databázi.', 404);
			} else {
				$item->update($updateOrInsert);
			}

			$this->categoryGoods->where('goods_id', $itemId)->delete();
			$forInsert = array();
			foreach ($values['category_id'] as $categoryId) {
				$forInsert[] = ['category_id' => $categoryId, 'goods_id' => $itemId];
			}
			$this->categoryGoods->insert($forInsert);

			$this->flashMessage('Změny uloženy.', 'success');

		} else {
			$item = $this->item->insert($updateOrInsert);

			// categories
			$forInsert = array();
			foreach ($values['category_id'] as $categoryId) {
				$forInsert[] = ['category_id' => $categoryId, 'goods_id' => $item->id];
			}
			$this->categoryGoods->insert($forInsert);
			$this->flashMessage('Výrobek vložen do databáze.', 'success');
		}

		if ($item && $values['photo']) {
			if ($this->photo->insertPhoto($values['photo'], $item->id)) {
				$this->flashMessage('Byl přiložen obrázek.', 'success');
			}
		}

		$this->redirect('edit', $item->id);
	}

	public function actionDelete($id) {
		try {
			$this->item->get($id)->delete();
			$this->flashMessage('Výrobek odstraněn.', 'success');
		} catch (\Exception $e) {
			$this->flashMessage('Výrobek se nepodařilo odebrat.', 'danger');
		}
		$this->redirect('default');
	}

	public function actionToggleRecommended($id) {
		$item = $this->item->get($id);
		if (!$item) {
			$this->error('Data pod ID '. $id .' nebyla nalezena v databázi.', 404);
		}
		$item->update(array('recommended' => !$item->recommended));
		$this->flashMessage('Doporučení výrobku '. $item->name ." bylo změněno.", 'success');
		$this->redirect('default');
	}

	/**
	 * Create Search form
	 * @return UI\Form
	 */
	protected function createComponentSearchForm()
	{
		$form = new UI\Form;
		$form->addText('search', 'Vyhledávání:')
			->addRule($form::FILLED)
			->setAttribute('placeholder', 'Název nebo výrobce');
		$form->addSubmit('send', 'Hledat');
		$form->onSuccess[] = array($this, 'searchFormSucceeded');
		return $form;
	}

	public function searchFormSucceeded(UI\Form $form, $values)
	{
		$this->redirect('search', $values['search']);
	}

}
