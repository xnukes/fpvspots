<?php
namespace App\Entities;

use App\Models\BaseEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Nette\Utils\DateTime;
use Nette\Utils\Strings;

/**
 * @property integer $id
 * @property EventEntity $event
 * @property UserEntity $user
 * @property integer $state
 * @property DateTime $created
 *
 * @ORM\Entity(repositoryClass="\App\Entities\EventUserEntity")
 * @ORM\Entity
 * @ORM\Table(name="events_users")
 */

class EventUserEntity extends BaseEntity
{
	const STATE_WAIT = 0;

	const STATE_JOIN = 1;

	const STATE_STAFF = 2;

	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @ORM\Column(name="event_user_id")
	 */
	protected $id;

	/**
	 * @ORM\ManyToOne(targetEntity="EventEntity", inversedBy="users")
	 */
	protected $event;

	/**
	 * @ORM\OneToOne(targetEntity="UserEntity")
	 */
	protected $user;

	/**
	 * @ORM\Column(type="integer", name="user_state")
	 */
	protected $state;

	/**
	 * @ORM\Column(type="datetime", name="created")
	 */
	protected $created;

	public function __construct()
	{
		$this->created = new DateTime();
	}
}