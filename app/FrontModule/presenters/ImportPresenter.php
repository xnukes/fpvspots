<?php
/**
 * This file is part of the Fast n' Simple Shop.
 * Copyright (c) 2017 Lukáš Vlček (http://www.vlceklukas.cz)
 *
 */

namespace App\FrontModule\Presenters;


class ImportPresenter extends BasePresenter
{
	public function actionDefault()
	{
		$louky = new \DOMDocument();
		$louky->load(__DIR__ . DIRECTORY_SEPARATOR . 'Miniquad FPV CZ.kml');

		/** @var \DOMElement $item */
		foreach ($louky->getElementsByTagName('Placemark') as $item) {
			$name = $item->getElementsByTagName('name')->item(0)->nodeValue;
			$point = explode(',', trim($item->getElementsByTagName('Point')->item(0)->getElementsByTagName('coordinates')->item(0)->nodeValue));
			$desc = $item->getElementsByTagName('description')->length ? $item->getElementsByTagName('description')->item(0)->nodeValue : '';

			$pointer = $point[1] . ';' . $point[0] . ';' . '18';

			echo 'INSERT INTO places (`user_id`, `name`, `description`, `map_place`, `created_on`, `changed_on`) VALUES (1, "'.$name.'", "'.addslashes($desc).'", "'.$pointer.'", NOW(), NOW());' . PHP_EOL;
		}

		exit;
	}
}