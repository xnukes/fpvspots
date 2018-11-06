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
 * @property EventEntity $event
 * @property ArrayCollection $children
 * @property EventsCommentsEntity $parent
 * @property UserEntity $user
 * @property string $text
 * @property DateTime $created
 *
 * @ORM\Entity(repositoryClass="\App\Entities\EventsCommentsEntity")
 * @ORM\Entity
 * @ORM\Table(name="events_comments")
 */
class EventsCommentsEntity extends BaseEntity
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/**
	 * @ORM\ManyToOne(targetEntity="EventEntity")
	 */
	protected $event;

	/**
	 * @ORM\OneToMany(targetEntity="EventsCommentsEntity", mappedBy="parent")
	 */
	protected $children;

	/**
	 * @ORM\ManyToOne(targetEntity="EventsCommentsEntity", inversedBy="children")
	 * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
	 */
	protected $parent;

	/**
	 * @ORM\OneToOne(targetEntity="UserEntity")
	 */
	protected $user;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $text;

	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $created;

	public function __construct()
	{
		$this->created = new DateTime();
		$this->children = new ArrayCollection();
	}
}