<?php
/**
 * Created by PhpStorm.
 * User: Xnukes
 * Date: 18.11.2016
 * Time: 23:32
 */

namespace App\AdminModule\Forms;

/**
 * Interface IBaseForm
 * @package App\AdminModule\Forms
 * @author Lukáš Vlček
 */
interface IBaseForm
{
	public function create(\App\AdminModule\Presenters\BasePresenter $presenter, $name);

	public function setDefaultData($entity);
}