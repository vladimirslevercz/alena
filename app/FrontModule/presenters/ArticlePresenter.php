<?php

namespace App\FrontModule\Presenters;

use Nette,
    App\Model;

use App\Model\Article;
use App\Model\Category;
use Nette\Database\Context;

/**
 * Article presenter.
 */
class ArticlePresenter extends BasePresenter
{
	/**
	 * @var Model\Article
	 * @inject
	 */
	public $article;

	public function actionShow($id)
	{
		if (!$this->article->get($id)) {
			$this->flashMessage('Stránka neexistuje.', 'warning');
			$this->redirect('Homepage:default');
		}
	}

	public function renderShow($id) {
		$this->template->article = $this->article->get($id);
	}

}
