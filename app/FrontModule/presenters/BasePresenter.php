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
	 * @var Model\Menu
	 * @inject
	 */
	public $menu;

	public function beforeRender()
	{
		$this->template->menus = $this->menu->where('menu_id', null);
	}
}
