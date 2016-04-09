<?php

namespace App\FrontModule\Presenters;

use Nette,
	App\Model;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
	/**
	 * DPH in percent, 21%
	 */
	const DPH = 21;
	/**
	 * @var Model\Article
	 * @inject
	 */
	public $article;

	public function beforeRender()
	{
		$this->template->articles = $this->article->where('menu IS NOT NULL');
		$this->template->DPH = self::DPH;
	}
}
