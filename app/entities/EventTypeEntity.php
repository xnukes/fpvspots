<?php
namespace App\Entities;

use App\Models\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @property integer $id
 * @property string $name
 *
 * @ORM\Entity(repositoryClass="\App\Entities\EventTypeEntity")
 * @ORM\Entity
 * @ORM\Table(name="events_types")
 */
class EventTypeEntity extends BaseEntity
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
}