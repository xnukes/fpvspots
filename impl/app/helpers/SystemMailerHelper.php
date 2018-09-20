<?php
/**************************************************************************************************
 * This file is part of the github-fpvspots.                                                      *
 * Licence: GNU General Public License.                                                           *
 * Copyright (c) 2018 Lukáš Vlček (http://www.vlceklukas.cz)                                      *
 **************************************************************************************************/

namespace App\Helpers;

use App\Entities\EventEntity;
use App\Entities\UserEntity;
use App\Models\ConfigRepository;
use Latte\Runtime\Template;;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;
use Nette\Mail\SmtpMailer;
use Nette\SmartObject;

class SystemMailerHelper
{
	use SmartObject;

	/** @var ConfigRepository */
	public $configRepository;

	private $mailer;

	private $message;

	private $mailerTemplate;

	private $templateFactory;

	private $sendTo = [];

	protected $params = [];

	public function __construct(ConfigRepository $configRepository, \Nette\Application\UI\ITemplateFactory $templateFactory)
	{
		$this->templateFactory = $templateFactory;
		$this->configRepository = $configRepository;
		$this->mailer = new \Nette\Mail\SmtpMailer([
			'host' => $this->configRepository->mailerHost,
			'username' => $this->configRepository->mailerUsername,
			'password' => $this->configRepository->mailerPassword,
			'secure' => $this->configRepository->mailerSecurity,
		]);
		$this->message = new Message();
		$this->message->setFrom($this->configRepository->mailerFrom, $this->configRepository->sitetitle);
	}

	public function sendMailEventRequestJoin(EventEntity $event, UserEntity $user)
	{
		$this->setMailerTemplate('event-request-join');

		$this->addParam('event', $event);
		$this->addParam('user', $user);

		$this->setSubject('Žádost o přihlášení');
		$this->addSendTo($user->email);
		$this->addSendTo($event->user->email);

		try {
			$result = $this->send();
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage());
		}

		return $result;
	}

	public function sendMailEventAcceptJoin(EventEntity $event, UserEntity $user)
	{
		$this->setMailerTemplate('event-accept-join');

		$this->addParam('event', $event);
		$this->addParam('user', $user);

		$this->setSubject('Žádost o přihlášení - Přijato');
		$this->addSendTo($user->email);

		try {
			$result = $this->send();
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage());
		}

		return $result;
	}

	public function addSendTo($email)
	{
		$this->sendTo[] = $email;
	}

	public function addParam($paramName, $paramValue)
	{
		$this->params[$paramName] = $paramValue;
	}

	public function setSubject($subject)
	{
		$this->message->setSubject($subject);
	}

	public function send()
	{
		$template = $this->templateFactory->createTemplate();
		$template->setFile($this->mailerTemplate);

		foreach ($this->params as $key=>$value) {
			$template->$key = $value;
		}
		foreach ($this->sendTo as $mailTo) {
			$this->message->addTo($mailTo);
		}

		$this->message->setHtmlBody($template);
		return $this->mailer->send($this->message);
	}

	private function setMailerTemplate($template)
	{
		$templatePath = __DIR__ . DS . 'SystemMailerTemplates' . DS . $template . '.latte';
		if(!file_exists($templatePath)) {
			throw new \Exception('Template for system mailer not found !');
		}
		$this->mailerTemplate = $templatePath;
	}
}