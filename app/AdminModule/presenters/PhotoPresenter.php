<?php

namespace App\AdminModule\Presenters;

use Nette,
	App\Model;

use Nette\Database\Context;
use Nette\Application\UI;


/**
 * Photo edit presenter.
 */
class PhotoPresenter extends BasePresenter
{
	const PAGE_LIMIT = 50;

	/**
	 * @var Model\Photo
	 * @inject
	 */
	public $item;

	/**
	 * @var Model\Manufacturer
	 * @inject
	 */
	public $manufacturer;

	/**
	 * @var Model\Goods
	 * @inject
	 */
	public $goods;

	/**
	 * @persist
	 */
	public $page;

	public function actionDefault($page = 0) {
		if (!$this->page || $page) {
			$this->page = $page;
		}
	}

	public function renderDefault()
	{
		$this->template->item = $this->item->order('manufacturer.name, name')->limit(self::PAGE_LIMIT, $this->page * self::PAGE_LIMIT);
		$this->template->page = $this->page;
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

		if (!$defaults['goods']) {
			$defaults['goods'] = '';
		}
		$this['itemForm']->setDefaults($defaults);
	}

	protected function createComponentItemForm()
	{
		$form = new UI\Form;

		$form->addText('name', 'Název fotografie:');

		$form->addTextArea('description', 'Popis:')
			->setAttribute('class', 'tinyMCE');

		$form->addUpload('photo', 'Fotografie JPG')
			->setRequired();

		$form->addCheckbox('attach', 'Přiřadit k výrobku:')
			->addCondition($form::EQUAL, TRUE)
			->toggle('goods_form');

		$manufacturers = $this->manufacturer->order('name')->fetchPairs('id', 'name');
		$manufacturers[null] = '--- Žádný výrobce ---';

		$goods = $this->goods->order('manufacturer.name, name')->fetchPairs('id', 'name');
		$goods[null] = '--- Žádný výrobek ---';

		$form->addSelect('manufacturer_id', 'Výrobce', $manufacturers)->setDefaultValue('');

		$form->addSelect('goods', 'Výrobek', $goods)->setDefaultValue('');


		$form->addSubmit('save', 'Uložit')
			->setAttribute('class', 'btn btn-primary');

		$form->onSuccess[] = array($this, 'itemFormSucceeded');

		return $form;
	}

	public function attachementUpdateFormSucceeded($form)
	{
		$values = $form->getValues();

		$this->attachement = new Attachement($this->database);
		$this->attachement = $this->attachement->where('id', $values['attachement_id'])->fetch();

		$topic_id = $this->attachement->topic_id;
		$ext = $this->attachement->extension;
		$name = $this->attachement->name;

		$authorizator = new MyAuthorizator();
		$authorizator->injectDatabase($this->database);
		$this->user->setAuthorizator($authorizator);
		if (!$this->user->isAllowed('attachement', 'update')) {
			$this->flashMessage('Nemáte oprávnění upravit přílohu.','warning');
			$this->redirect('Topic:show', $topic_id);
		}

		if (!$this->attachement) { // kontrola existence záznamu
			throw new BadRequestException;
		}

		if (!Attachement::checkFilename($values['name'])) {
			$this->flashMessage('Neplatné jméno, .','warning');
			$this->redirect('Attachement:update', $values['attachement_id']);
		}

		$this->attachement = new Attachement($this->database);
		$this->attachement->where('id',$values['attachement_id'])->update(
			array(
				'description' => $values['description'],
				'name' => trim($values['name']) . '.' . $ext
			)
		);

		$this->flashMessage('Příloha '. $name .' byla upravena ('. $values['name'] .').', 'success');
		$this->redirect('Topic:show', $topic_id);
	}

	public function itemFormSucceeded(UI\Form $form, $values)
	{
		if (isset($values['category_id']) && !$values['category_id'] ) {
			$values['category_id'] = $values['category_id'] ? $values['category_id'] : null;
		}
		if (isset($values['manufacturer_id']) && !$values['manufacturer_id'] ) {
			$values['manufacturer_id'] = $values['manufacturer_id'] ? $values['manufacturer_id'] : null;
		}
		$itemId = $this->getParameter('id');
		if ($itemId) {
			$item = $this->item->get($itemId);
			if (!$item) {
				$this->error('Data nebyla nalezena v databázi.', 404);
			} else {
				$item->update($values);
			}
			$this->flashMessage('Změny uloženy.', 'success');

		} else {
			$item = $this->item->insert($values);
			$this->flashMessage('Výrobek vložen do databáze.', 'success');
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
		$item->update(['recommended' => !$item->recommended]);
		$this->flashMessage('Doporučení výrobku '. $item->name ." bylo změněno.", 'success');
		$this->redirect('default');
	}
}
