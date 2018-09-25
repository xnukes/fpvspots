<?php
namespace App\Entities;

use App\Models\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @property integer $id
 * @property string $filename
 * @property integer $filesize
 * @property string $mimetype
 * @property string $filehash
 * @property \Doctrine\Common\Collections\ArrayCollection $drones
 *
 * @ORM\Entity(repositoryClass="\App\Entities\PhotoEntity")
 * @ORM\Entity
 * @ORM\Table(name="photos")
 */
class PhotoEntity extends BaseEntity
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
	protected $filename;

	/**
	 * @ORM\Column(type="integer")
	 */
	protected $filesize;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $mimetype;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $filehash;

	/**
	 * @ORM\ManyToMany(targetEntity="DroneEntity", mappedBy="photos")
	 */
	protected $drones;

	/**
	 * @ORM\ManyToMany(targetEntity="EventEntity", mappedBy="photos")
	 */
	protected $events;

	/**
	 * @ORM\ManyToMany(targetEntity="UserProductEntity", mappedBy="photos")
	 */
	protected $usersProducts;

	public function __construct()
	{
		$this->drones = new \Doctrine\Common\Collections\ArrayCollection();
	}
}