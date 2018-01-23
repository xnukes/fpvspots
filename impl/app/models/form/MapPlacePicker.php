<?php
/**
 * Class MapPlacePicker.php , Last changed 22.09.2017
 * This file is part of the fpvspots
 * Copyright (c) 2017 Lukáš Vlček (http://www.vlceklukas.cz)
 */

namespace App\Models\Form;

use App\Presenters\BasePresenter;
use Nette;

class MapPlacePicker extends TextInput
{
	const PREFIX_ENTITY_ID = 'google-map-';

	private $width;

	private $height;

	public function __construct($label = NULL, $width, $height)
	{
		parent::__construct($label, null);
		$this->width = $width;
		$this->height = $height;
	}

	public function getControl()
	{
		$control = parent::getControl();
		$control->setAttribute('style', 'display: none;');

		$container = Nette\Utils\Html::el('div')
			->setAttribute('class', 'gmaps-container');

		$mapContainer = Nette\Utils\Html::el('div')
			->setAttribute('style', 'width: ' . $this->width . '; height: ' . $this->height . ';')
			->setAttribute('class', 'google-map')
			->setAttribute('id', $this->getName());

		$container->addHtml($mapContainer);
		$container->addHtml($control);

		return $container;
	}

	public function getName()
	{
		return self::PREFIX_ENTITY_ID . parent::getName();
	}
}