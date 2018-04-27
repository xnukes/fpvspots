<?php
/**
 * Created by PhpStorm.
 * User: tech
 * Date: 29.7.2016
 * Time: 11:04
 */

namespace App\AdminModule\Forms;

use App\Models\Form;
use Nette;

/**
 * Class BaseForm
 * @package App\AdminModule\Forms
 * @author Lukáš Vlček
 */
class BaseForm extends Nette\Object
{
	/** @var Form */
	protected $form;

	public function create(\App\AdminModule\Presenters\BasePresenter $presenter, $name)
	{
		$form = new Form($presenter, $name);

		$form->setTranslator($presenter->getTranslator());

		return $form;
	}
}