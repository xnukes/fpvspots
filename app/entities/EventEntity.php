<?php
namespace App\Entities;

use App\Models\BaseEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Nette\Utils\DateTime;

/**
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $mapPlace
 * @property boolean $isPrivate
 * @property integer $maxUsers
 * @property EventTypeEntity $eventType
 * @property UserEntity $user
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
}