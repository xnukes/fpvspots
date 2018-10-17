<?php
/**
 * Class RegisterPresenter.php , Last changed 30.05.2017
 * This file is part of the fpvspots
 * Copyright (c) 2017 Lukáš Vlček (http://www.vlceklukas.cz)
 */

namespace App\AdminModule\Presenters;

use App\Models\Form;
use Nette;

class RegisterPresenter extends BasePresenter
{
    /** @var \App\Managers\UserManager @inject */
    public $userManager;

    public function actionIn()
    {
        if ($this->user->isLoggedIn() && $this->user->isInRole('admin')) {
            $this->flashMessage('Již jste přihlášeni.');
            $this->redirect('Dashboard:');
        } else {
            $this->setLayout('register');
        }
    }

    public function createComponentRegisterForm($name)
    {
        $form = new Form($this, $name);

        $form->addText('username', 'Uživatelské jméno')
            ->setRequired('Prosím zadejte přihlašovací jméno.');

        $form->addText('email', 'E-mail')
            ->setRequired('Zadejte prosím váš e-mail')
            ->addRule(Form::EMAIL, 'Zadejtee prosím platný e-mail.');

        $form->addPassword('password', 'Vaše heslo')
            ->setRequired('Prosím zadejte vaše heslo.')
            ->addRule(Form::MIN_LENGTH, 'Heslo musí obsahovat minimálně %d znaků', 5);

        $form->addPassword('passwordVerify', 'Heslo pro kontrolu')
            ->setRequired('Zadejte prosím heslo ještě jednou pro kontrolu')
            ->addRule(Form::EQUAL, 'Hesla se neshodují', $form['password']);

        $form->addSubmit('send', 'Registrovat Účet');

        $form->onSuccess[] = [$this, 'onSuccessRegisterForm'];

        return $form;
    }

    public function onSuccessRegisterForm(Form $form, $vars)
    {
		$vars->username = Nette\Utils\Strings::webalize($vars->username);

		$exists = $this->userManager->isUsernameExists($vars->username);

		$existsEmail = $this->userManager->isEmailExists($vars->email);

		$user = null;
		if(!$exists && !$existsEmail)
        	$user = $this->userManager->registerUser($vars->username, $vars->password, $vars->email);

        if ($user && $exists == false && $existsEmail == false) {
            $this->flashMessage('Děkujeme za registraci, nyní se prosím přihlašte. <strong>Váš login: ' . $vars->username . '</strong>');
            $this->redirect('Sign:in');
        } else {
            $this->flashMessage('Nastala chyba při registraci.', 'warning');
			if($exists)
            	$this->flashMessage('Toto přihlašovací jméno již existuje.', 'warning');
			if($existsEmail)
            	$this->flashMessage('Tento email již existuje.', 'warning');
            $this->redirect('Register:in');
        }
    }
}