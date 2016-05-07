<?php

namespace App\AdminModule\Presenters;

use Nette,
	App\Model;

use App\Model\Sale;
use App\Model\Goods;
use Nette\Database\Context;
use Nette\Application\UI;


/**
 * Sale edit presenter.
 */
class SalePresenter extends BasePresenter
{
	/**
	 * @var Model\Sale
	 * @inject
	 */
	public $sale;

	/**
	 * @var Model\Goods
	 * @inject
	 */
	public $goods;

	public function actionDefault() {
	}

	public function renderDefault()
	{
		$this->template->sales = $this->sale->order('id DESC');
	}

	/**
	 * @param $id
	 */
	public function actionEdit($id) {
		$sale = $this->sale->get($id);
		if(!$sale) {
			$this->error('Data nebyla nalezena v databázi.', 404);
		}
		$defaults = $sale->toArray();
		$defaults['start'] = new \DateTime($sale->start);
		$defaults['start'] = $defaults['start']->format('Y-m-d\TH:i:s');

		// neomezeny?
		$defaults['unlimited'] = !((bool)$defaults['end']);

		$defaults['end'] = new \DateTime($sale->end);
		$defaults['end'] = $defaults['end']->format('Y-m-d\TH:i:s');

		$this['saleForm']->setDefaults($defaults);
		$this->template->sale = $sale;
	}

	protected function createComponentSaleForm()
	{
		$form = new UI\Form;

		$form->addTextArea('text', 'Popis akce:')
			->setAttribute('class', 'tinyMCE');

		$form->addText('color', 'Barva písma')
			->addRule($form::PATTERN, 'Barva písma je ve špatném formátu, použijte #RRGGBB', '(\#[0-9A-Fa-f]{6})?')
			->setAttribute('type', 'color');

		$form->addText('bgcolor', 'Barva pozadí')
			->addRule($form::PATTERN, 'Barva pozadí je ve špatném formátu, použijte #RRGGBB', '(\#[0-9A-Fa-f]{6})?')
			->setAttribute('type', 'color');

		$form->addText('border', 'Barva ohraničení')
			->addRule($form::PATTERN, 'Barva ohraničení je ve špatném formátu, použijte #RRGGBB', '(\#[0-9A-Fa-f]{6})?')
			->setAttribute('type', 'color');

		$form->addText('start', 'Počátek akce')
			->setAttribute('type', 'datetime-local');

		$form->addText('end', 'Konec akce')
			->setAttribute('type', 'datetime-local');

		$form->addCheckbox('unlimited', 'Neomezená platnost');

		$form->addCheckbox('enable', 'Aktivní');

		$form->addSubmit('save', 'Uložit')
			->setAttribute('class', 'btn btn-primary');

		$form->onValidate[] = array($this, 'saleFormValidate');
		$form->onSuccess[] = array($this, 'saleFormSucceeded');

		return $form;
	}

	public function saleFormValidate(UI\Form $form)
	{
		$values = $form->getValues();

		if (!$values['unlimited']) {
			$start = new \DateTime($values['start']);
			$end = new \DateTime($values['end']);
			if ($start > $end) {
				$form->addError('Začátek akce musí být dříve než její konec. Opravte data počátku nebo konce a nebo dejte akci neomezenou.');
			}
		}
	}


	public function saleFormSucceeded(UI\Form $form, $values)
	{
		$sale = $this->sale->get($this->getParameter('id'));
		if (!$sale) {
			$this->error('Data nebyla nalezena v databázi.', '404');
		} else {
			$update = $values;
			$update['end'] = $values['unlimited'] ? null : $values['end'];
			unset($update['unlimited']);

			$sale->update($values);
		}
		$this->flashMessage('Změny uloženy.', 'success');

		$this->redirect('edit', $sale->id);
	}

	public function actionCreate($id) {
		if ($good = $this->goods->get($id)) {
			$saleId = $this->sale->insert(
				[
					'goods_id' => $id,
					'enable' => 0,
					'color' => '#333333',
					'bgcolor' => '#FFFFF8',
					'border' => '#FFFF33',
					'start' => new \DateTime('now'),
				]
			);
			$this->flashMessage('Akce k výrobku '. $good->manufacturer->name .' '. $good->name .' byla vytvořena.', 'success');
			$this->redirect('edit', $saleId);
		} else {
			$this->flashMessage('Vytvoření akce selhalo.', 'danger');
			$this->redirect('default');
		}
	}

	public function actionDelete($id) {
		$this->sale->get($id)->delete();
		$this->flashMessage('Akce byla odstraněna.', 'success');
		$this->redirect('default');
	}
}
