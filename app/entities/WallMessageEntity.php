<?php
namespace App\Entities;

use App\Models\BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use Nette\Utils\DateTime;

/**
 * @property integer $id
 * @property WallMessageEntity $children
 * @property WallMessageEntity $parent
 * @property UserEntity $user
 * @property string $message
 * @property DateTime $createdOn
 * @property DateTime $changedOn
 *
 * @ORM\Entity(repositoryClass="\App\Entities\WallMessageEntity")
 * @ORM\Entity
 * @ORM\Table(name="wall_messages")
 */
class WallMessageEntity extends BaseEntity
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/**
	 * @ORM\OneToMany(targetEntity="WallMessageEntity", mappedBy="parent")
	 */
	private $children;

	/**
	 * @ORM\ManyToOne(targetEntity="WallMessageEntity", inversedBy="children")
	 * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
	 */
	private $parent;

	/**
	 * @ORM\ManyToOne(targetEntity="UserEntity")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 */
	protected $user;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $message;

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
	}
}