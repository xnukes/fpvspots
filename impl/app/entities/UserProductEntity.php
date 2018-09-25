<?php
/**************************************************************************************************
 * This file is part of the github-fpvspots.                                                      *
 * Licence: GNU General Public License.                                                           *
 * Copyright (c) 2018 Lukáš Vlček (http://www.vlceklukas.cz)                                      *
 **************************************************************************************************/

namespace App\Entities;

use App\Models\BaseEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @property integer $id
 * @property UserEntity $user
 * @property string $productTitle
 * @property string $productDesc
 * @property string $productDescShort
 * @property float $productPrice
 * @property float $productShipment
 * @property integer $productStock
 * @property integer $productState
 * @property ArrayCollection $photos
 * @property integer $published
 *
 * @ORM\Entity(repositoryClass="\App\Entities\UserProductEntity")
 * @ORM\Entity
 * @ORM\Table(name="users_products")
 */
class UserProductEntity extends BaseEntity
{
	const STATES = [
		self::STATE_NEWEST,
		self::STATE_USED,
	];

	const STATE_NEWEST = 0;

	const STATE_USED = 1;

	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @ORM\Column(name="id")
	 */
	protected $id;

	/**
	 * @ORM\OneToOne(targetEntity="UserEntity")
	 */
	protected $user;

	/**
	 * @ORM\Column(type="string", name="product_title")
	 */
	protected $productTitle;

	/**
	 * @ORM\Column(type="text", name="product_desc")
	 */
	protected $productDesc;

	/**
	 * @ORM\Column(type="text", name="product_desc_short")
	 */
	protected $productDescShort;

	/**
	 * @ORM\Column(type="float", name="product_price")
	 */
	protected $productPrice;

	/**
	 * @ORM\Column(type="float", name="product_shipment")
	 */
	protected $productShipment;

	/**
	 * @ORM\Column(type="integer", name="product_stock")
	 */
	protected $productStock;

	/**
	 * @ORM\Column(type="integer", name="product_state")
	 */
	protected $productState;

	/**
	 * @ORM\ManyToMany(targetEntity="PhotoEntity", inversedBy="usersProducts")
	 * @ORM\JoinTable(name="users_products_photos")
	 */
	protected $photos;

	/**
	 * @ORM\Column(type="integer", name="published")
	 */
	protected $published;

	public function __construct()
	{
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

	public function getState()
	{
		$state = null;

		if($this->productState == self::STATE_NEWEST)
			$state = 'Nové';
		if($this->productState == self::STATE_USED)
			$state = 'Použité';

		return $state;
	}
}