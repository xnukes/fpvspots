<?php
/**
 * Class Grid.php , Last changed 29.10.16 1:41
 * This file is part of the drones-map
 * Copyright (c) 2017 Lukáš Vlček (http://www.vlceklukas.cz)
 */

namespace App\Models;

use Nette;

/**
 * Class Grid
 * @package App\Models
 * @author Lukáš Vlček
 */
class Grid extends \Ublaboo\DataGrid\DataGrid
{
	public function __construct($parent, $name)
	{
		parent::__construct($parent, $name);

		$this->setTemplateFile( __DIR__ . DIRECTORY_SEPARATOR . 'grid' . DIRECTORY_SEPARATOR . 'datagrid.latte');

		/**
		 * Localization
		 */
		$translator = new \Ublaboo\DataGrid\Localization\SimpleTranslator([
			'ublaboo_datagrid.no_item_found_reset' => 'Žádné položky nenalezeny. Filtr můžete vynulovat',
			'ublaboo_datagrid.no_item_found' => 'Žádné položky nenalezeny.',
			'ublaboo_datagrid.here' => 'zde',
			'ublaboo_datagrid.items' => 'Položky',
			'ublaboo_datagrid.all' => 'všechny',
			'ublaboo_datagrid.from' => 'z',
			'ublaboo_datagrid.reset_filter' => 'Resetovat filtr',
			'ublaboo_datagrid.group_actions' => 'Hromadné akce',
			'ublaboo_datagrid.show_all_columns' => 'Zobrazit všechny sloupce',
			'ublaboo_datagrid.hide_column' => 'Skrýt sloupec',
			'ublaboo_datagrid.action' => 'Akce',
			'ublaboo_datagrid.previous' => 'Předchozí',
			'ublaboo_datagrid.next' => 'Další',
			'ublaboo_datagrid.choose' => 'Vyberte',
			'ublaboo_datagrid.execute' => 'Provést',
			'ublaboo_datagrid.save' => 'Uložit',
			'ublaboo_datagrid.cancel' => 'Zrušit',
			'ublaboo_datagrid.filter_submit_button' => 'Filtrovat',
			'ublaboo_datagrid.show_filter' => 'Filtrování',
			'ublaboo_datagrid.per_page_submit' => 'Odeslat',
		]);

		$this->setTranslator($translator);

		$this->setRefreshUrl(false);

		$this->setItemsPerPageList([10, 20, 50, 100]);
	}
}