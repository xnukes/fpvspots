<?php
/**
 * This file is part of the project FPVSpots.info.
 * Copyright (c) 2017 Lukáš Vlček (http://www.vlceklukas.cz)
 */

namespace App\FrontModule\Presenters;

use App\Libs\Responses\XmlResponse;
use App\Libs\Responses\XmlResponseTemplate;

class SitemapPresenter extends BasePresenter
{
	protected function startup()
	{
		parent::startup();
	}

	/**
	 * @throws \Nette\Application\AbortException
	 */
	public function renderDefault()
	{
		$xmlTemplate = new XmlResponseTemplate();
		$xmlTemplate->setRootElement('urlset', ['xmlns' => 'http://www.sitemaps.org/schemas/sitemap/0.9']);

		$response = new XmlResponse();
		$response->setPresenter($this);
		$response->setXmlTemplate($xmlTemplate);

		$this->sendResponse($response);
	}
}