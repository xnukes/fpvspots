<?php
namespace App\Entities;

use App\Models\BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use Nette\Utils\DateTime;

/**
 * @property integer $id
 * @property UserEntity $user
 * @property UserEntity $buddy
 * @property DateTime $createdOn
 *
 * @ORM\Entity(repositoryClass="\App\Entities\BuddyEntity")
 * @ORM\Entity
 * @ORM\Table(name="buddies")
 */
class BuddyEntity extends BaseEntity
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/**
	 * @ORM\ManyToOne(targetEntity="UserEntity")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 */
	protected $user;

	/**
	 * @ORM\ManyToOne(targetEntity="UserEntity")
	 * @ORM\JoinColumn(name="buddy_user_id", referencedColumnName="id")
	 */
	protected $buddy;

	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $createdOn;

	public function __construct()
	{
		$this->createdOn = new DateTime();
	}
}