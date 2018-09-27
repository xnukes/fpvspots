<?php
namespace App\AdminModule\Components\Wall;

use App\AdminModule\Components\BaseComponent;
use App\Models\Form;
use Nette\ComponentModel\IContainer;

class Wall extends BaseComponent
{
	/** @persistent */
	public $page = 1;

	/** @var int */
	public $perPage = 6;

	/** @var int */
	private $totalItems = 0;

	private $loadedItems;

	public function __construct(IContainer $parent, $name)
	{
		parent::__construct($parent, $name);

		$this->totalItems = $this->getPresenter()->entityManager->getRepository(\App\Entities\WallMessageEntity::class)->count(['parent' => null]);

		$this->loadedItems = $this->getPresenter()->entityManager
			->getRepository(\App\Entities\WallMessageEntity::class)
			->findBy(['parent' => null], ['createdOn' => 'DESC'], $this->perPage, ($this->perPage * ($this->page - 1)));
	}

	public function handlesetPage($page)
	{
		$this->page = $page;
		$this->redirect('this');
	}

	public function render()
	{
		$this->template->setFile(__DIR__ . DIRECTORY_SEPARATOR . 'Wall.latte');

		$this->template->wallMessages = $this->loadedItems;

		$this->template->totalItems = $this->totalItems;
		$this->template->perPage = $this->perPage;
		$this->template->page = $this->page;
		$this->template->totalPages = round($this->totalItems / $this->perPage);

		$this->template->render();
	}

	public function createComponentWallForm($name)
	{
		$form = new Form($this, $name);

		$form->getElementPrototype()->setAttribute('class', 'well');

		$form->addTextArea('message', 'Zpráva')
			->setAttribute('placeholder', 'Sdílejte vaši aktivitu ...')
			->setAttribute('rows', 2)
			->setAttribute('class', 'form-control')
			->setRequired('Zpráva je povinná !');

		$form->addSubmit('send', 'Odeslat zprávu')
			->setAttribute('class', 'btn btn-sm btn-primary pull-right');

		$form->onSuccess[] = [$this, 'wallFormSuccess'];

		return $form;
	}

	public function wallFormSuccess($form, $vars)
	{
		$message = new \App\Entities\WallMessageEntity();
		$message->user = $this->getPresenter()->userEntity;
		$message->message = $vars->message;

		$this->getPresenter()->entityManager->persist($message)->flush();
		$this->getPresenter()->flashMessage('Zpráva byla odeslána.');
		$this->getPresenter()->redirect('this');
	}

	public function handleRemove($wallMessageId)
	{
		$msg = $this->getPresenter()->entityManager->getRepository(\App\Entities\WallMessageEntity::class)->find($wallMessageId);

		if($msg->user <> $this->getPresenter()->userEntity) {
			throw new \Exception('This is not your message.');
		}

		$this->getPresenter()->entityManager->remove($msg)->flush();
		$this->getPresenter()->flashMessage('Zpráva byla smazána.');
		$this->getPresenter()->redirect('this');
	}
}