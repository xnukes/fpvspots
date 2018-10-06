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
 * @property string $username
 * @property string $password
 * @property string $token
 * @property string $firstName
 * @property string $lastName
 * @property string $email
 * @property string $phone
 * @property PhotoEntity $photo
 * @property DateTime $createdOn
 * @property DateTime $changedOn
 * @property DateTime $visitedOn
 * @property boolean $enabled
 * @property boolean $public
 * @property string $pageWebsite
 * @property string $pageFacebook
 * @property string $pageGoogleplus
 * @property string $pageContent
 * @property boolean $shopEnabled
 * @property string $shopTitle
 * @property string $shopCoverBg
 * @property string $shopDesc
 * @property ArrayCollection $shopProducts
 * @property string $role
 * @property ArrayCollection $events
 * @property ArrayCollection $drones
 * @property ArrayCollection $places
 * @property ArrayCollection $videos
 * @property ArrayCollection $buddies
 *
 * @ORM\Entity(repositoryClass="\App\Entities\UserEntity")
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class UserEntity extends BaseEntity
{
    const ROLE_USER = 'user';

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $username;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $password;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $token;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $firstName;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $lastName;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $email;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $phone;

	/**
	 * @ORM\OneToOne(targetEntity="PhotoEntity", cascade={"persist", "remove"})
	 * @ORM\JoinColumn(name="photo_id", referencedColumnName="id")
	 */
	protected $photo;

	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $createdOn;

	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $changedOn;

	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $visitedOn;

	/**
	 * @ORM\Column(type="boolean")
	 */
	protected $enabled;

	/**
	 * @ORM\Column(type="boolean")
	 */
	protected $public;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $pageWebsite;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $pageFacebook;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $pageGoogleplus;

	/**
	 * @ORM\Column(type="text")
	 */
	protected $pageContent;

	/**
	 * @ORM\Column(type="boolean")
	 */
	protected $shopEnabled;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $shopTitle;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $shopCoverBg;

	/**
	 * @ORM\Column(type="text")
	 */
	protected $shopDesc;

	/**
	 * @ORM\OneToMany(targetEntity="UserProductEntity", mappedBy="user", cascade={"persist", "remove"})
	 */
	protected $shopProducts;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $role;

	/**
	 * @ORM\OneToMany(targetEntity="EventEntity", mappedBy="user")
	 */
	protected $events;

	/**
	 * @ORM\OneToMany(targetEntity="DroneEntity", mappedBy="user")
	 */
	protected $drones;

	/**
	 * @ORM\OneToMany(targetEntity="PlaceEntity", mappedBy="user", cascade={"persist", "remove"})
	 */
	protected $places;

	/**
	 * @ORM\OneToMany(targetEntity="VideoEntity", mappedBy="user", cascade={"persist", "remove"})
	 */
	protected $videos;

	/**
	 * @ORM\OneToMany(targetEntity="BuddyEntity", mappedBy="user")
	 */
	protected $buddies;

    public function __construct()
    {
        $this->createdOn = new DateTime();
        $this->changedOn = new DateTime();
        $this->visitedOn = new DateTime();
        $this->enabled = true;
        $this->public = false;
        $this->role = self::ROLE_USER;
		$this->events = new ArrayCollection();
		$this->drones = new ArrayCollection();
		$this->places = new ArrayCollection();
		$this->shopProducts = new ArrayCollection();
		$this->shopCoverBg = '';
		$this->shopTitle = '';
		$this->shopDesc = '';
		$this->shopEnabled = false;
    }
}