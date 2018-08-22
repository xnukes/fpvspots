<?php
namespace App\Entities;

use App\Models\BaseEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Nette\Utils\DateTime;
use Nette\Utils\Strings;

/**
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $mapPlace
 * @property boolean $isPrivate
 * @property integer $maxUsers
 * @property EventTypeEntity $eventType
 * @property UserEntity $user
 * @property \Doctrine\ORM\PersistentCollection $users
 * @property DateTime $eventDate
 * @property DateTime $createdOn
 * @property DateTime $changedOn
 * @property \Doctrine\ORM\PersistentCollection $photos
 *
 * @ORM\Entity(repositoryClass="\App\Entities\EventEntity")
 * @ORM\Entity
 * @ORM\Table(name="events")
 */
class EventEntity extends BaseEntity
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $name;

	/**
	 * @ORM\Column(type="text")
	 */
	protected $description;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $mapPlace;

	/**
	 * @ORM\Column(type="boolean")
	 */
	protected $isPrivate;

	/**
	 * @ORM\Column(type="integer")
	 */
	protected $maxUsers;

	/**
	 * @ORM\OneToOne(targetEntity="EventTypeEntity")
	 * @ORM\JoinColumn(name="event_type_id", referencedColumnName="id")
	 */
	protected $eventType;

	/**
	 * @ORM\ManyToOne(targetEntity="UserEntity")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 */
	protected $user;

	/**
	 * @ORM\OneToMany(targetEntity="EventUserEntity", mappedBy="event")
	 * @ORM\JoinTable(name="events_users")
	 */
	protected $users;

	/**
	 * @ORM\ManyToMany(targetEntity="PhotoEntity", inversedBy="events")
	 * @ORM\JoinTable(name="events_photos")
	 */
	protected $photos;

	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $eventDate;

	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $createdOn;

	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $changedOn;

	public function __construct()
	{
		$this->createdOn = new DateTime();
		$this->changedOn = new DateTime();
		$this->photos = new \Doctrine\Common\Collections\ArrayCollection();
	}

	/**
	 * @return \Doctrine\ORM\PersistentCollection
	 */
	public function getPhotos()
	{
		return $this->photos;
	}

	public function getSlug()
	{
		return Strings::webalize($this->name);
	}

	public function isJoined($user)
	{
		if(!$user) return false;
		$joined = $this->users->exists(function($index, $eventUser) use ($user) {
			return $eventUser->user == $user;
		});
		return $joined;
	}

	public function hasOwner($user)
	{
		return ($this->user == $user);
	}

	public function hasStaff($user)
	{
		if(!$user) return false;
		$eventuser = $this->users->filter(function ($eventUser) use ($user) {
			return $eventUser->user == $user;
		});
		if(count($eventuser))
			return ($eventuser->get(0)->state > 0 ? true : false);
		else
			false;
	}

	public function getJoinedStatus($user)
	{
		if(!$user) return false;
		$eventuser = $this->users->filter(function ($eventUser) use ($user) {
			return $eventUser->user == $user;
		});
		if(count($eventuser))
			return ($eventuser->get(0)->state > 0 ? true : false);
		else
			false;
	}

	public function getStaffUsers()
	{
		return $this->users->filter(function ($entity) { return $entity->state == 2; });
	}

	public function getConfirmedUsers()
	{
		return $this->users->filter(function ($entity) { return $entity->state == 1; });
	}

	public function getUnConfirmedUsers()
	{
		return $this->users->filter(function ($entity) { return $entity->state == 0; });
	}
}