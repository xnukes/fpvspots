<?php
namespace App\Entities;

use App\Models\BaseEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @property integer $id
 * @property DroneEntity $drone
 * @property string $ipAddress
 * @property integer $rate
 *
 * @ORM\Entity(repositoryClass="\App\Entities\DroneRatingEntity")
 * @ORM\Entity
 * @ORM\Table(name="drones_ratings")
 */
class DroneRatingEntity extends BaseEntity
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/**
	 * @ORM\OneToOne(targetEntity="DroneEntity", cascade={"persist", "remove"})
	 * @ORM\JoinColumn(name="drone_id", referencedColumnName="id")
	 */
	protected $drone;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $ipAddress;

	/**
	 * @ORM\Column(type="integer")
	 */
	protected $rate;
}