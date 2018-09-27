<?php
/**************************************************************************************************
 * This file is part of the github-fpvspots.                                                      *
 * Licence: GNU General Public License.                                                           *
 * Copyright (c) 2018 Lukáš Vlček (http://www.vlceklukas.cz)                                      *
 **************************************************************************************************/

namespace App\FrontModule\Presenters;


use App\Entities\UserEntity;
use App\Helpers\SystemMailerHelper;
use App\Managers\EshopManager;
use App\Models\Form;
use Nette\Application\BadRequestException;

class EshopPresenter extends BasePresenter
{
	public $shops;

	/** @var EshopManager @inject */
	public $eshopManager;

	/** @var SystemMailerHelper @inject */
	public $systemMailerHelper;

	public function actionDefault()
	{
		$this->shops = $this->entityManager->getRepository(UserEntity::class)->findBy(['shopEnabled' => true]);

		$this->template->shops = $this->shops;
	}

	public function actionDetail($slug)
	{
		try {
			$this->template->shop = $this->eshopManager->getBySlug($slug);
		} catch (BadRequestException $e) {
			$this->flashMessage($e->getMessage(), 'danger');
		}
	}

	public function actionProduct($slug, $pid)
	{
		try {
			$this->template->shop = $this->eshopManager->getBySlug($slug);
			$this->template->product = $this->eshopManager->getUserProductById($pid);
		} catch (BadRequestException $e) {
			$this->flashMessage($e->getMessage(), 'danger');
		}
	}

	public function createComponentUserProductBuyForm($name)
	{
		$form = new Form($this, $name);

		$form->addText('buyerName', 'Vaše jméno')
			->setAttribute('class', 'form-control')
			->setRequired('Prosím zadejte vaše jméno.');

		$form->addText('buyerEmail', 'Váš e-mail')
			->setAttribute('class', 'form-control')
			->addRule(Form::EMAIL, 'Prosím zadejte platný e-mail')
			->setRequired('Prosím zadejte váš e-mail.');

		$form->addTextArea('buyerMessage', 'Text zprávy')
			->setAttribute('class', 'form-control')
			->setRequired('Prosím zadejte text vaší zprávy.');

		$form->addSubmit('send', 'Odeslat poptávku')
			->setAttribute('class', 'btn btn-success');

		$form->onSuccess[] = [$this, 'UserProductBuyFormSuccess'];

		return $form;
	}

	public function UserProductBuyFormSuccess($form, $vars)
	{
		$result = $this->systemMailerHelper->sendMailUserProductBuy($this->template->product, $this->template->shop, $vars);

		if ($result)
			$this->flashMessage('Vaše poptávka byla odeslána. Děkujeme za využití služeb FPVSpots.info');

		$this->redirect('this');
	}
}