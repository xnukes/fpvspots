<?php
/**
 * Created by PhpStorm.
 * User: Xnukes
 * Date: 1.11.2016
 * Time: 21:34
 */

namespace App\AdminModule\Forms;

use Nette;

/**
 * Class UsersEditForm
 * @package App\AdminModule\Forms
 * @author Lukáš Vlček
 */
class UsersEditForm extends Nette\Object implements IBaseForm
{
	/** @var BaseForm */
	public $form;

	public function __construct(BaseForm $baseForm)
	{
		$this->form = $baseForm;
	}

	public function setDefaultData($entity)
    {
        // TODO: Implement setDefaultData() method.
    }

    public function create(\App\AdminModule\Presenters\BasePresenter $presenter, $name)
	{
		$form = $this->form->create($presenter, $name);

		$form->addText('username', 'Uživatelské jméno')
			->setRequired('Prosím vyplňte uživatelské jméno.');

		return $form;
	}
}