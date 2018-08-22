<?php
/**
 * Class UserManager.php , Last changed 20.1.17 23:50
 * This file is part of the drones-map
 * Copyright (c) 2017 Lukáš Vlček (http://www.vlceklukas.cz)
 */

namespace App\Managers;

use	Nette\Security\Passwords;
use Nette\Utils\Strings;

/**
 * Class UserManager
 * @package App\Managers
 */
class UserManager extends BaseManager
{
    public function registerUser($username, $password, $email)
    {
        $password = Passwords::hash($password);

        $user = new \App\Entities\UserEntity();
        $user->username = Strings::normalize($username);
        $user->password = $password;
        $user->email    = $email;

        $this->entityManager->persist($user)->flush();

        return $user;
    }

    public function isUsernameExists($username)
	{
		return $this->entityManager->getRepository(\App\Entities\UserEntity::getClassName())->findOneBy(['username' => $username]) ? true : false;
	}

	public function createLostPsw($username, $email)
	{
		$exists = $this->entityManager->getRepository(\App\Entities\UserEntity::getClassName())->findOneBy(['username' => $username, 'email' => $email]) ? true : false;
		if(!$exists) {
			return 'Tato kombinace uživ. jména a emailu nebyla nalezena.';
		}
	}
}