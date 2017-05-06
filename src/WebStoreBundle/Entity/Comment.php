<?php

namespace WebStoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Comment
 *
 * @ORM\Table(name="comments")
 * @ORM\Entity(repositoryClass="WebStoreBundle\Repository\CommentRepository")
 */
class Comment
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
     * @ORM\Column(name="content", type="text")
     * @Assert\NotBlank(message="Content Field Is Required!")
     * @Assert\Length(min="5", max="250")
     */
    private $content;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="WebStoreBundle\Entity\User", inversedBy="comments")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    private $author;

    /**
     * @var int
     * @ORM\Column(name="author_id", type="integer")
     */
    private $authorId;

    /**
     * @var Item
     * @ORM\ManyToOne(targetEntity="WebStoreBundle\Entity\Item", inversedBy="comments")
     * @ORM\JoinColumn(name="item_id", referencedColumnName="id")
     */
    private $item;

    /**
     * @var int
     * @ORM\Column(name="item_id", type="integer")*
     */
    private $itemId;

    /**
     * @var \DateTime
     * @ORM\Column(name="dateAdded", type="datetime")
     */
    private $dateAdded;


    public function __construct()
    {
        $this->dateAdded = new \DateTime('now');
//        $this->authorId = $this->getAuthor()->getId();
//        $this->itemId = $this->getItem()->getId();
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
     * Set content
     *
     * @param string $content
     *
     * @return Comment
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     *
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return Item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param Item $item
     */
    public function setItem(Item $item)
    {
        $this->item = $item;
    }

    /**
     * @return int
     */
    public function getItemId()
    {
        return $this->itemId;
    }

    /**
     * @param mixed $itemId
     */
    public function setItemId($itemId)
    {
        $this->itemId = $itemId;
    }

    /**
     * @return bool|string
     */
    public function getCommentSummary($length = 200)
    {
        if(strlen($this->getContent()) <= $length) return $this->getContent();
        return substr($this->getContent(), 0, $length) . "...";
    }

    /**
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param User $author
     */
    public function setAuthor(User $author)
    {
        $this->author = $author;
    }

    /**
     * @return int
     */
    public function getAuthorId(): int
    {
        return $this->authorId;
    }

    /**
     * @param mixed $authorId
     */
    public function setAuthorId($authorId)
    {
        $this->authorId = $authorId;
    }
}

