<?php
/**
 * Class Authenticator.php , Last changed 19.10.16 12:43
 * This file is part of the drones-map
 * Copyright (c) 2017 Lukáš Vlček (http://www.vlceklukas.cz)
 */

namespace App\Models;

use App\Entities\UserEntity;
use Kdyby\Doctrine\EntityManager;
use Nette;
use	Nette\Security\Passwords;

/**
 * Class Authenticator
 * @package App\Models
 * @author Lukáš Vlček
 */
class Authenticator extends Nette\Object implements Nette\Security\IAuthenticator
{
	/** @var EntityManager */
	public $entityManager;

	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	/**
	 * Performs an authentication.
	 * @return Nette\Security\Identity
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		list($username, $password) = $credentials;

		$entity = $this->entityManager->getRepository(UserEntity::class)->findOneBy(['username' => $username]);

		if (!$entity)
		{
			throw new Nette\Security\AuthenticationException('Uživatelské jméno nebylo nenalezeno. Prosím zkontroluje, zda zadáváte správný e-mail včetně teček.', self::IDENTITY_NOT_FOUND);
		}
		elseif (!$entity->enabled)
		{
			throw new Nette\Security\AuthenticationException('Uživatelský účet ještě nebyl povolen. Povolte jej kliknutím na ověřovací odkaz v zaslaném e-mailu.', self::INVALID_CREDENTIAL);
		}
		elseif (!Passwords::verify($password, $entity->password))
		{
			throw new Nette\Security\AuthenticationException('Nesprávné heslo.', self::INVALID_CREDENTIAL);
		}
		elseif (Passwords::needsRehash($entity->password))
		{
			$entity->password = Passwords::hash($password);
			$this->entityManager->persist($entity)->flush();
		}

		return new Nette\Security\Identity($entity->id, $entity->role, $entity);
	}
}

class DuplicateNameException extends \Exception
{

}
