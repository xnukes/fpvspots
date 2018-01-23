<?php
namespace App\AdminModule\Components\Wall;

use App\AdminModule\Components\BaseComponent;
use App\Models\Form;

class Wall extends BaseComponent
{
	/** @persitent int */
	public $page = 0;

	public function render()
	{
		$this->template->setFile(__DIR__ . DIRECTORY_SEPARATOR . 'Wall.latte');

		$this->template->wallMessages = $this->getPresenter()->entityManager->getRepository(\App\Entities\WallMessageEntity::class)
			->findBy(['parent' => null], ['createdOn' => 'DESC'], 20);

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