<?php

namespace WebStoreBundle\Entity;

use Doctrine\DBAL\Types\ArrayType;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Cart
 *
 * @ORM\Table(name="carts")
 * @ORM\Entity(repositoryClass="WebStoreBundle\Repository\CartRepository")
 */
class Cart
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     * @ORM\OneToOne(targetEntity="WebStoreBundle\Entity\User", inversedBy="cart")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $owner;

    /**
     * @var int
     */
    private $ownerId;

    /**
     * @var ArrayCollection
     * @ORM\Column(name="items", type="array", nullable=true)
     */
    private $items;

    /**
     * @var string
     */
    private $totalCost;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set ownerId
     *
     * @param integer $ownerId
     *
     * @return Cart
     */
    public function setOwnerId($ownerId)
    {
        $this->ownerId = $ownerId;

        return $this;
    }

    /**
     * Get ownerId
     *
     * @return int
     */
    public function getOwnerId()
    {
        return $this->ownerId;
    }

    /**
     * Set items
     *
     * @param Item $item
     *
     * @return Cart
     */
    public function addItem($item)
    {
        $this->items[] = $item;
        return $this;
    }

    /**
     * Get items
     *
     * @return ArrayCollection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Set new array for items
     * @param $itemsArr
     * @return Cart
     */
    public function setItems($itemsArr)
    {
        $this->items = $itemsArr;
        return $this;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getItem($id){
        foreach ($this->items as  $item){
            if($item['id'] == $id){
                return $item;
            }
        }
        return null;
    }

    /**
     * @return User
     */
    public function getOwner(): User
    {
        return $this->owner;
    }

    /**
     * @param User $owner
     */
    public function setOwner(User $owner)
    {
        $this->owner = $owner;
    }
}