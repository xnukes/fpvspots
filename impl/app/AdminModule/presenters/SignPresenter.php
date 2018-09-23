<?php
/**
 * Class SignPresenter.php , Last changed 20.1.17 23:24
 * This file is part of the drones-map
 * Copyright (c) 2017 Lukáš Vlček (http://www.vlceklukas.cz)
 */

namespace App\AdminModule\Presenters;

use App\Models\Form;
use Nette;
/**
 * Class SignPresenter
 * @package App\AdminModule\Presenters
 * @author Lukáš Vlček
 */
class SignPresenter extends BasePresenter
{
	/** @var \App\Managers\UserManager @inject */
	public $userManager;

	public function actionIn()
    {
		if ($this->user->isLoggedIn() && $this->user->isAllowed($this->name, $this->action)) {
			$this->flashMessage('Již jste přihlášeni.');
			$this->redirect('Dashboard:');
		} else {
			$this->setLayout('login');
		}
	}

	public function actionLost()
	{
		if ($this->user->isLoggedIn() && $this->user->isInRole('admin')) {
			$this->flashMessage('Již jste přihlášeni.');
			$this->redirect('Dashboard:');
		} else {
			$this->setLayout('lost');
		}
	}

	public function actionOut()
	{
		if ($this->user->isLoggedIn()){
			$this->user->logout();
		}
		$this->redirect('Sign:in');
	}

	public function createComponentSignInForm($name)
	{
		$form = new Form($this, $name);

		$form->addText('username', 'Uživatelské jméno')
			->setRequired('Zadejte prosím uživatelské jméno.');

		$form->addPassword('password', 'Vaše heslo')
			->setRequired('Zadejte prosím heslo.');

		$form->addCheckbox('remember', 'Pamatovat si mé přihlášení.');

		$form->addSubmit('send', 'Přihlásit');

		$form->onSuccess[] = array($this, 'signInFormSuccess');

		return $form;
	}

	public function signInFormSuccess(Nette\Application\UI\Form $form, $vars)
	{
		if ($vars->remember) {
			$this->user->setExpiration('14 days', false);
		} else {
			$this->user->setExpiration('1 hour', true);
		}
		try {
			$this->user->login($vars->username, $vars->password);
			$this->restoreRequest($this->backlink);
			$this->redirect('Dashboard:');
		} catch (Nette\Security\AuthenticationException $e) {
			$form->addError($e->getMessage());
		}
	}

	public function createComponentSignLostForm($name)
	{
		$form = new Form($this, $name);

		$form->addText('username', 'Uřivatelské jméno:')
			->setRequired('Prosím zadejte váše uživatelské jméno.');

		$form->addText('email', 'Váš e-mail:')
			->addRule(Form::EMAIL, 'Zadejte platný e-mail.')
			->setRequired('Prosím zadejte váš platný e-mail.');

		$form->addSubmit('send', 'Zaslat ověřovací e-mail');

		$form->onSuccess[] = array($this, 'signLostFormSuccess');

		return $form;
	}

	public function signLostFormSuccess(Nette\Application\UI\Form $form, $vars)
	{
		// TODO: dodělat obnovu hesla
		try {
			$message = $this->userManager->createLostPsw($vars->username, $vars->email);
			$this->flashMessage($message, 'info');
			$this->redirect('Sign:in');
		} catch (Nette\Application\BadRequestException $e) {
			$form->addError($e->getMessage());
		}
	}
}