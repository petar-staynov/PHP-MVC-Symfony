<?php

namespace WebStoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Item
 *
 * @ORM\Table(name="items")
 * @ORM\Entity(repositoryClass="WebStoreBundle\Repository\ItemRepository")
 * @Vich\Uploadable()
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
     * @ORM\Column(name="discount_value", type="integer")
     * @Assert\GreaterThanOrEqual(value = 0, message="Discount should be equal or more than 0%")
     * @Assert\LessThan(value=100, message="Discount should be less than 100%")
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
     * @ORM\Column(name="ownerId", type="integer", nullable=false)
     */
    private $ownerId;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="WebStoreBundle\Entity\User", inversedBy="items")
     * @ORM\JoinColumn(name="ownerId", referencedColumnName="id")
     */
    private $owner;

    /**
     * @var Category
     * @ORM\ManyToOne(targetEntity="WebStoreBundle\Entity\Category", inversedBy="items")
     * @Assert\NotBlank()
     */
    private $category;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="WebStoreBundle\Entity\Comment", mappedBy="item")
     */
    private $comments;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * @Vich\UploadableField(mapping="product_image", fileNameProperty="imageName")
     * @var File
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $imageName;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $updatedAt;


    public function __construct()
    {
        $this->dateAdded = new \DateTime('now');
        $this->comments = new ArrayCollection();
        $this->getDiscounted();
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
        if ($this->discountValue == 0) {
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
        if (strlen($this->getDescription()) <= $length) {
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
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @return string
     */
    public function getOwnerUsername()
    {
        return $this->getOwner()->getUsername();
    }

    /**
     * @return string
     */
    public function getOwnerName()
    {
        return $this->getOwner()->getFullName();
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
     * Check if item is on discount
     * @return bool
     */
    public function getDiscounted()
    {
        if ($this->discountValue < 0 && $this->getDiscountExpirationDate() >= new \DateTime('now')){
            return 0;
        }
        return $this->discounted;
    }

    /**
     * @param  boolean $switch
     * @return Item
     */
    public function setDiscounted(bool $switch)
    {
        if ($this->discountValue < 0 && $this->getDiscountExpirationDate() >= new \DateTime('now')){
            $this->discounted = 0;
            return $this;
        }
        $this->discounted = $switch;
        return $this;
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
        $this->discountExpirationDate = $date;
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

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     *
     * @return Item
     */
    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;

        if ($image) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    /**
     * @return File|null
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * @param string $imageName
     *
     * @return Item
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getImageName()
    {
        return $this->imageName;
    }

    /**
     * @return string
     */
    public function getImagePath()
    {
        if(!$this->imageName) {
            return 'images/items/default.png';
        }
        return 'images/items/' . $this->imageName;
    }
}

