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

	/**
	 * generate slug of this event name
	 * @return string
	 */
	public function getSlug()
	{
		return Strings::webalize($this->name);
	}

	/**
	 * get all joined users in collection
	 * @return \Doctrine\ORM\PersistentCollection
	 */
	public function getUsers()
	{
		return $this->users;
	}

	/**
	 * return 0 if is unlimited
	 * @return int
	 */
	public function getMaxUsers()
	{
		return $this->maxUsers;
	}

	/**
	 * get count of all joined users with staff's
	 * @return int
	 */
	public function getJoinedCount()
	{
		return $this->getUsers()->count();
	}

	public function isJoined($user)
	{
		if(!$user instanceof UserEntity) return false;
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
		if(!$this->isJoined($user)) return false;
		if($this->getJoinedStatus($user) == 2) {
			return true;
		} else {
			return false;
		}
	}

	public function getJoinedStatus($user)
	{
		if(!$this->isJoined($user)) return false;
		foreach ($this->getUsers() as $eventUser) {
			if($eventUser->user == $user) return $eventUser->state;
		}
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

	public function removeFromUsers(UserEntity $userEntity)
	{
		/** @var EventUserEntity $eventUser */
		foreach ($this->getUsers() as $key=>$eventUser) {
			if($eventUser->user == $userEntity) {
				return $this->users->remove($key);
			}
		}
		return false;
	}
}