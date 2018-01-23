<?php
/**
 * Class Form.php , Last changed 29.7.16 11:16
 * This file is part of the drones-map
 * Copyright (c) 2017 Lukáš Vlček (http://www.vlceklukas.cz)
 */

namespace App\Models;

use Nette;

/**
 * Class Form
 * @package App\Models
 * @author Lukáš Vlček
 */
class Form extends \Nette\Application\UI\Form
{
	public function __construct(Nette\ComponentModel\IContainer $parent, $name)
	{
		parent::__construct($parent, $name);
	}

    /**
     * @param $name
     * @param null $label
     * @param null $cols
     * @param null $maxLength
     * @return Form\TextInput
     */
    public function addText($name, $label = NULL, $cols = NULL, $maxLength = NULL)
    {
        return $this[$name] = (new Form\TextInput($label, $maxLength))
            ->setHtmlAttribute('size', $cols);
    }

    public function addUploadInput($name, $label = NULL, $multiple = FALSE)
    {
        return $this[$name] = (new Form\UploadInput($label, $multiple));
    }

    public function addMapPlacePicker($name, $label = NULL, $width, $height)
	{
		return $this[$name] = (new \App\Models\Form\MapPlacePicker($label, $width, $height));
	}

    /**
     * @param bool $throw
     * @return \App\Presenters\BasePresenter|NULL
     */
    public function getPresenter($throw = TRUE)
    {
        return parent::getPresenter($throw);
    }
}