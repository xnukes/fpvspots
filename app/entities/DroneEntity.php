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
 * $property integer $type
 * @property UserEntity $user
 * @property DateTime $createdOn
 * @property DateTime $changedOn
 * @property ArrayCollection $ratings
 * @property ArrayCollection $photos
 *
 * @ORM\Entity(repositoryClass="\App\Entities\DroneEntity")
 * @ORM\Entity
 * @ORM\Table(name="drones")
 */
class DroneEntity extends BaseEntity
{
    const TYPE_RACE = 1;

    const TYPE_VIDEO = 2;

    const TYPE_WHOOP = 3;

    const TYPES = [
        self::TYPE_RACE => 'administrator.drones.type.race',
        self::TYPE_VIDEO => 'administrator.drones.type.video',
        self::TYPE_WHOOP => 'administrator.drones.type.whoop',
    ];

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
     * @ORM\Column(type="integer")
     */
    protected $type;

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
	 * @ORM\OneToMany(targetEntity="DroneRatingEntity", mappedBy="drone", cascade={"persist", "remove"})
	 */
    protected $ratings;

	/**
	 * Many Drones have Photos Groups.
	 * @ORM\ManyToMany(targetEntity="PhotoEntity", inversedBy="drones")
	 * @ORM\JoinTable(name="drones_photos")
	 */
    protected $photos;

    public function __construct()
    {
        $this->createdOn = new DateTime();
        $this->changedOn = new DateTime();
		$this->photos = new \Doctrine\Common\Collections\ArrayCollection();
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

	public function getCalculatedRating()
	{
		if (count($this->ratings)) {
			$rates = [];
			foreach ($this->ratings as $rating)
				$rates[] = $rating->rate;
			return number_format(array_sum($rates) / count($rates), 2, '.', ' ');
		} else {
			return 0;
		}
	}

	public function hasIpRating()
	{
		foreach ($this->ratings as $rating) {
			if($rating->ipAddress == $_SERVER['REMOTE_ADDR']) {
				return true;
				break;
			}
		}
		return false;
	}

	public function getWebalizeName()
	{
		return Strings::webalize($this->name);
	}
}