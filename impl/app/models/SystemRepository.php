<?php
/**
 * Class SystemRepository.php , Last changed 20.1.17 23:06
 * This file is part of the drones-map
 * Copyright (c) 2017 Lukáš Vlček (http://www.vlceklukas.cz)
 */

namespace App\Models;

use Nette;

/**
 * Class SystemRepository
 * @package App\Models
 * @author Lukáš Vlček
 */
abstract class SystemRepository
{
    use Nette\SmartObject;

	const SystemApp			= 'FPVSpots.info';

	const SystemVersion 	= '1.0';

	const SystemSubVersion	= '2';

	const SystemYear		= '2016 - 2018';

	const SystemAuthorName 	= 'Lukáš Vlček';

	const SystemAuthorEmail = 'xnukes@gmail.com';

	const SystemName 		= '<a href="mailto:' . self::SystemAuthorEmail . '" target="_blank">' .self::SystemApp . '</a> powered by <abbr class="initialism">Nette Framework</abbr>.';

	public static function getSystemVersion(\Kdyby\Translation\Translator $translator)
	{
		return self::SystemName
			. '<br />'
			. 'Copyright © '
			. self::SystemYear
			. ' all rights reserved. '
			. $translator->translate('language.default.version')
			. ': '
			. self::SystemVersion
			. '.'
			. self::SystemSubVersion;
	}
}