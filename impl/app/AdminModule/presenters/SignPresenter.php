<?php
/**
 * Class SignPresenter.php , Last changed 20.1.17 23:24
 * This file is part of the drones-map
 * Copyright (c) 2017 Lukáš Vlček (http://www.vlceklukas.cz)
 */

namespace App\AdminModule\Presenters;

use App\Entities\UserEntity;
use App\Helpers\SystemMailerHelper;
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

	/** @var SystemMailerHelper @inject */
	public $systemMailerHelper;

	public $token;

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
		if ($this->user->isLoggedIn()) {
			$this->flashMessage('Již jste přihlášeni.');
			$this->redirect('Dashboard:');
		} else {
			$this->setLayout('lost');
		}
	}

	public function actionLostRecovery($token)
	{
		$this->token = $token;

		if(!$this->userManager->isTokenIsExists($token)) {
			$this->flashMessage('Token neexistuje ! Prosím zkuste to znovu.', 'danger');
			$this->redirect('Sign:in');
		}

		if ($this->user->isLoggedIn()) {
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
		try {
			$token = $this->userManager->createLostPsw($vars->username, $vars->email);

			$recoveryLink = $this->link('//Sign:lostRecovery', ['token' => $token]);

			/** @var UserEntity $user */
			$user = $this->entityManager->getRepository(\App\Entities\UserEntity::getClassName())->findOneBy(['username' => $vars->username, 'email' => $vars->email]);

			$this->systemMailerHelper->sendMailUserLostRecovery($user, $recoveryLink);

			$this->flashMessage('Obnovovácí odkaz vám byl zaslán na e-mail.', 'info');
			$this->redirect('Sign:in');
		} catch (\Exception $e) {
			$this->flashMessage($e->getMessage(), 'danger');
			$this->redirect('Sign:lost');
		}
	}

	public function createComponentSignLostRecoveryForm($name)
	{
		$form = new Form($this, $name);

		$form->addPassword('password', 'Vaše heslo')
			->setAttribute('class', 'form-control');

		$form->addPassword('passwordVerify', 'Heslo pro kontrolu')
			->setRequired(false)
			->setAttribute('class', 'form-control')
			->addRule(Form::EQUAL, 'Hesla se neshodují', $form['password'])
			->addCondition(Form::FILLED, $form['password'])
			->setRequired('Prosím vypňte potvrzovací heslo.');

		$form->addSubmit('send', 'Uložit heslo');

		$form->onSuccess[] = array($this, 'signLostRecoveryFormSuccess');

		return $form;
	}

	public function signLostRecoveryFormSuccess(Nette\Application\UI\Form $form, $vars)
	{
		try {
			$user = $this->userManager->changePasswordByToken($this->token, $vars->password);
			$this->flashMessage('Vaše heslo bylo uloženo :)');
			$this->redirect('Sign:in');
		} catch (\Exception $exception) {
			$this->flashMessage($exception->getMessage(), 'danger');
			$this->redirect('Sign:lost');
		}
	}
}