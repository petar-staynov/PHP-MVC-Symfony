<?php

namespace WebStoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Item
 *
 * @ORM\Table(name="items")
 * @ORM\Entity(repositoryClass="WebStoreBundle\Repository\ItemRepository")
 */
class Item
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="description", type="text")
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="price", type="decimal", precision=10, scale=2)
     * @Assert\NotBlank()
     */
    private $price;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateAdded", type="datetime")
     */
    private $dateAdded;

    /**
     * @var bool
     * @ORM\Column(name="promotion", type="boolean")
     */
    private $onPromotion;

    /**
     * @var int
     *
     * @ORM\Column(name="ownerId", type="integer")
     */
    private $ownerId;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="WebStoreBundle\Entity\User", inversedBy="items")
     * @ORM\JoinColumn(name="ownerId", referencedColumnName="id")
     */
    private $owner;


    public function __construct()
    {
        $this->dateAdded = new \DateTime('now');
        $this->onPromotion = false;
    }

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
     * Set name
     *
     * @param string $name
     *
     * @return Item
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set price
     *
     * @return Item
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Item
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set dateAdded
     *
     * @param \DateTime $dateAdded
     *
     * @return Item
     */
    public function setDateAdded($dateAdded)
    {
        $this->dateAdded = $dateAdded;

        return $this;
    }

    /**
     * @param integer $ownerId
     * @return Item
     */
    public function setOwnerId($ownerId)
    {
        $this->ownerId = $ownerId;
        return $this;
    }

    /**
     * @return int
     */
    public function getOwnerId()
    {
        return $this->ownerId;
    }

    /**
     * @param User $owner
     *
     * @return Item
     */
    public function setOwner(User $owner)
    {
        $this->owner = $owner;
        return $this;
    }

    /**
     * @return string
     */
    public function getOwner()
    {
        return $this->owner->getFullName();
    }

    /**
     * Get dateAdded
     *
     * @return \DateTime
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }
}

