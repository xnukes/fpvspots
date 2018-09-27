<?php

namespace App\AdminModule\Presenters;

use Nette,
	Nette\Security\IUserStorage;
/**
 * Class BasePresenter
 * @package App\AdminModule\Presenters
 * @author Lukáš Vlček
 */
class BasePresenter extends \App\Presenters\BasePresenter
{
	protected function startup()
	{
		parent::startup();

		if (!$this->user->isLoggedIn() and !$this->user->isAllowed($this->name, $this->action)) {
			if ($this->user->getLogoutReason() === IUserStorage::INACTIVITY) {
				$this->flashMessage('Byli jste příliš dlouho neaktivní. Přihlašte se prosím znovu.', 'warning');
			}
			$this->redirect('Sign:in', ['backlink' => $this->storeRequest()]);
		} elseif (!$this->user->isAllowed($this->name, $this->action)) {
			$this->flashMessage('Přístup nepovolen.', 'warning');
			$this->redirect('Dashboard:');
		} elseif($this->user->isLoggedIn() and $this->user->isAllowed($this->name, $this->action)) {
			$this->template->user = $this->user->identity->data;
		}

//		$locale = $this->getParameter('locale');
//		if(!isset($locale)) {
//			$this->locale = 'cs';
//		} else {
//			$this->locale = $locale;
//		}
	}
}