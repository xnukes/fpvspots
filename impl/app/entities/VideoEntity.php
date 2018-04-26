<?php
/**************************************************************************************************
 * This file is part of the github-fpvspots.                                                      *
 * Licence: GNU General Public License.                                                           *
 * Copyright (c) 2018 Lukáš Vlček (http://www.vlceklukas.cz)                                      *
 **************************************************************************************************/

namespace App\Entities;

use App\Models\BaseEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Nette\Utils\DateTime;

/**
 * @property integer $id
 * @property string $link
 * @property string $name
 * @property string $desc
 * @property string $type
 * @property DateTime $createdOn
 * @property DateTime $changedOn
 * @property UserEntity $user
 *
 * @ORM\Entity(repositoryClass="\App\Entities\VideoEntity")
 * @ORM\Entity
 * @ORM\Table(name="videos")
 */
class VideoEntity extends BaseEntity
{
	const TYPES = [
		self::TYPE_YOUTUBE => 'YouTube.com'
	];

	const TYPE_YOUTUBE = 'youtube';

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $link;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $name;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $desc;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $type;

	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $createdOn;

	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $changedOn;

	/**
	 * @ORM\ManyToOne(targetEntity="UserEntity")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 */
	protected $user;

	public function __construct()
	{
		$this->createdOn = new DateTime();
		$this->changedOn = new DateTime();
	}
}