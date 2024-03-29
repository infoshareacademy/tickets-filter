<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ticket
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\TicketRepository")
 */
class Ticket
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=250)
     */
    public $title;

    /**
     * @var string
     *
     * @ORM\Column(name="auctionUrl", type="string", length=250)
     */
    public $auctionUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=2000)
     */
    public $description;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="string", length=11)
     */
    public $price;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=11)
     */
    public $type;


    public function __construct(){

    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Ticket
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set auctionUrl
     *
     * @param string $auctionUrl
     *
     * @return Ticket
     */
    public function setAuctionUrl($auctionUrl)
    {
        $this->auctionUrl = $auctionUrl;

        return $this;
    }

    /**
     * Get auctionUrl
     *
     * @return string
     */
    public function getAuctionUrl()
    {
        return $this->auctionUrl;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Ticket
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
     * Set price
     *
     * @param integer $price
     *
     * @return Ticket
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return integer
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     *
     * @return Ticket
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \Type
     */
    public function getType()
    {
        return $this->type;
    }
}

