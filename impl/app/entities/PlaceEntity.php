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
 * @property string $plusDesc
 * @property string $minusDesc
 * @property string $mapPlace
 * @property UserEntity $user
 * @property DateTime $createdOn
 * @property DateTime $changedOn
 * @property ArrayCollection $photos
 *
 * @ORM\Entity(repositoryClass="\App\Entities\PlaceEntity")
 * @ORM\Entity
 * @ORM\Table(name="places")
 */
class PlaceEntity extends BaseEntity
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
	protected $plusDesc;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $minusDesc;

	/**
	 * @ORM\Column(type="float")
	 */
	protected $latitude;

	/**
	 * @ORM\Column(type="integer")
	 */
	protected $zoom;

	/**
	 * @ORM\Column(type="float")
	 */
	protected $longitude;

	/**
	 * @ORM\ManyToOne(targetEntity="UserEntity")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 */
	protected $user;

	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $createdOn;

	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $changedOn;

	/**
	 * @ORM\ManyToMany(targetEntity="PhotoEntity", inversedBy="places")
	 * @ORM\JoinTable(name="places_photos")
	 */
	protected $photos;

	public function __construct()
	{
		$this->createdOn = new DateTime();
		$this->changedOn = new DateTime();
	}

	public function addPhoto(PhotoEntity $photoEntity)
	{
		$this->photos->add($photoEntity);
	}

	public function removePhoto($photoKey)
	{
		$this->photos->remove($photoKey);
	}

	public function getPhoto($photoKey)
	{
		return $this->photos->get($photoKey);
	}
}