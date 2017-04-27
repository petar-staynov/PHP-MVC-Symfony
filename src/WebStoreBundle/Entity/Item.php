<?php

namespace WebStoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
     * @Assert\NotBlank()
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
     * @Assert\GreaterThanOrEqual(value = 0, message="Price should be 0 or more")
     */
    private $price;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_added", type="datetime")
     */
    private $dateAdded;

    /**
     * @var boolean
     * @ORM\Column(name="discounted", type="boolean")
     */
    private $discounted;

    /**
     * @var string
     * @ORM\Column(name="discount", type="integer")
     * @Assert\GreaterThan(value = 0, message="Discount should be more than 0%")
     * @Assert\LessThan(value=99, message="Discount should be less than 99%")
     */
    private $discountValue;

    /**
     * @var \DateTime
     * @ORM\Column(name="discountExpirationDate", type="datetime", nullable=true)
     */
    private $discountExpirationDate;

    /**
     * @var int
     * @ORM\Column(name="quantity", type="integer")
     * @Assert\NotBlank()
     * @Assert\GreaterThanOrEqual(value = 0, message="Quantity should be 0 or more")
     */
    private $quantity;

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

    /**
     * @var Category
     * @ORM\ManyToOne(targetEntity="WebStoreBundle\Entity\Category", inversedBy="items")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    private $category;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="WebStoreBundle\Entity\Comment", mappedBy="item")
     */
    private $comments;

    public function __construct()
    {
        $this->dateAdded = new \DateTime('now');
        $this->discounted = 0;
        $this->comments = new ArrayCollection();
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
        if ($this->discounted == 0) {
            return $this->price;
        }
        return $this->price - ($this->price * ($this->getDiscount() / 100));
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
     * @return bool|string
     */
    public function getDescriptionSummary($length = 100)
    {
        if(strlen($this->getDescription()) <= $length){
            return $this->getDescription();
        }
        return substr($this->getDescription(), 0, $length) . "...";
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
    public function getOwnerUsername()
    {
        return $this->owner->getUsername();
    }

    /**
     * @return string
     */
    public function getOwnerName()
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

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Category $category
     *
     * @return Item
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Set discount
     * @param mixed $discountValue
     * @return Item
     */
    public function setDiscount($discountValue)
    {
        if (!$this->discounted) {
            $this->setDiscountExpirationDate(null);
            $discountValue = 0;
        }
        $this->discountValue = $discountValue;
        return $this;
    }

    /**
     * Get discount
     *
     * @return string
     */
    public function getDiscount()
    {
        if ($this->getDiscountExpirationDate() !== null
            && new \DateTime('now') > $this->getDiscountExpirationDate()) {
            $this->setDiscountExpirationDate(null);
            $this->setDiscount(0);
            $this->discounted = 0;
        }
        return $this->discountValue;
    }

    /**
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param mixed $quantity
     *
     * @return Item
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDiscounted()
    {
        return $this->discounted;
    }
    /**
     * @param bool $switch
     */
    public function setDiscounted($switch)
    {
        $this->discounted = $switch;
    }

    /**
     * @return \DateTime|null
     */
    public function getDiscountExpirationDate()
    {
        return $this->discountExpirationDate;
    }
    /**
     * @param \DateTime|null $date
     */
    public function setDiscountExpirationDate($date)
    {
        if(!$this->isDiscounted())$date = null;
        elseif($date === null && $this->discountExpirationDate !== null){
            $this->setDiscount(0);
        }
        $this->dateDiscountExpires = $date;
    }
    /**
     * @return float
     */
    public function getOriginalPrice()
    {
        return $this->price;
    }

    /**
     * @return ArrayCollection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param Comment $comment
     * @return Item
     */
    public function addComment(Comment $comment)
    {
        $this->comments[] = $comment;
        return $this;
    }
}

