<?php
/**
 * This file is part of the project FPVSpots.info.
 * Copyright (c) 2017 Lukáš Vlček (http://www.vlceklukas.cz)
 */

namespace App\Libs\Responses;

use App\Presenters\BasePresenter;
use Nette;
use Nette\Application\IResponse;

class XmlResponse implements IResponse
{
	/** @var XmlResponseTemplate */
	private $template;

	/** @var BasePresenter */
	private $presenter;

	/**
	 * @param XmlResponseTemplate $template
	 */
	public function setXmlTemplate(XmlResponseTemplate $template)
	{
		$this->template = $template;
	}

	public function setPresenter(BasePresenter $presenter)
	{
		$this->presenter = $presenter;
	}

	/**
	 * @param Nette\Http\IRequest $httpRequest
	 * @param Nette\Http\IResponse $httpResponse
	 */
	public function send(Nette\Http\IRequest $httpRequest, Nette\Http\IResponse $httpResponse)
	{
		$httpResponse->setContentType($this->template->getContentType(), 'UTF-8');
		$httpResponse->setExpiration(FALSE);
		echo $this->GetSitemapXML();
		exit;
	}

	/**
	 * @return mixed
	 */
	public function GetSitemapXML()
	{
		$xml = new \SimpleXMLElement($this->template->getRootElement());

		$rootAttributes = $this->template->getRootElementAttributes();

		if (is_array($rootAttributes)) {
			foreach ($rootAttributes as $key=>$rootAttribute) {
				$xml->addAttribute($key, $rootAttribute);
			}
		}

		$addLinkSitemap = function ($link, $args = [], $changefreq = 'daily') use ($xml) {
			$item = $xml->addChild('url');
			$item->addChild('loc', $this->presenter->link($link, $args));
			$item->addChild('changefreq', $changefreq);
		};

		$addLinkSitemap('//Places:default');
		$addLinkSitemap('//Machines:default');
		$addLinkSitemap('//Machines:top5');
		$addLinkSitemap('//Pilots:default');
		$addLinkSitemap('//Events:default');
		$addLinkSitemap('//:Admin:Sign:in');
		$addLinkSitemap('//:Admin:Register:in');

		$qb = $this->presenter->entityManager->getRepository(\App\Entities\PlaceEntity::getClassName())->createQueryBuilder('p');
		$qb->select('p,ph,u');
		$qb->join('p.user', 'u');
		$qb->leftJoin('p.photos', 'ph');

		$places = $qb->getQuery()->getArrayResult();

		foreach ($places as $place) {
			$slug = Nette\Utils\Strings::webalize($place['name']);
			$addLinkSitemap('//Places:detail', ['id' => $place['id'], 'slug' => !empty($slug) ? $slug : 'noname'], 'monthly');
		}


		$qb = $this->presenter->entityManager->getRepository(\App\Entities\UserEntity::getClassName())->createQueryBuilder('u');
		$qb->select('u');
		$qb->whereCriteria(['u.public' => true]);

		$pilots = $qb->getQuery()->getArrayResult();

		foreach ($pilots as $pilot) {
			$addLinkSitemap('//Pilots:detail', ['username' => $pilot['username']]);
		}

		return $xml->asXML();
	}
}

class XmlResponseTemplate
{
    use Nette\SmartObject;

	private $rootElement;

	private $rootElementAttributes = null;

	private $contentType;

	public function __construct($contentType = null)
	{
		$this->contentType = $contentType !== null ? $contentType : 'text/xml';
	}

	public function setRootElement($rootElement = 'data', $attributes = null)
	{
		$this->rootElement = '<' . $rootElement . '/>';
		if (is_array($attributes)) {
			$this->rootElementAttributes = $attributes;
		}
	}

	public function getRootElement() {
		return $this->rootElement;
	}

	public function getRootElementAttributes() {
		return $this->rootElementAttributes;
	}

	public function getContentType() {
		return $this->contentType;
	}
}