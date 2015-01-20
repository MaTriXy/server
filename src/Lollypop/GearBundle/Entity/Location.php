<?php

namespace Lollypop\GearBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Location
 *
 * @author seshachalam
 */

/**
 * @ORM\Entity
 * @ORM\Table(name="location")
 */
class Location
{

    const GOOGLE_MAPS_URL = 'http://maps.google.com/maps?z=12&t=m&q=loc:';
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="float", nullable=false)
     */
    private $latitude;

    /**
     * @ORM\Column(type="float", nullable=false)
     */
    private $longitude;

    /**
     * @ORM\Column(type="string", length=12, nullable=false)
     */
    private $geo_hash;
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $address;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="locations")
     */
    private $users;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

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
     * Set latitude
     *
     * @param string $latitude
     *
     * @return Location
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return string 
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param string $longitude
     *
     * @return Location
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return string 
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->created_at = new \DateTime();
    }

    /**
     * Set geo_hash
     *
     * @param string $geoHash
     * @return Location
     */
    public function setGeoHash($geoHash)
    {
        $this->geo_hash = $geoHash;

        return $this;
    }

    /**
     * Get geo_hash
     *
     * @return string 
     */
    public function getGeoHash()
    {
        return $this->geo_hash;
    }

    /**
     * Add users
     *
     * @param \Lollypop\GearBundle\Entity\User $users
     * @return Location
     */
    public function addUser(\Lollypop\GearBundle\Entity\User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \Lollypop\GearBundle\Entity\User $users
     */
    public function removeUser(\Lollypop\GearBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Location
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return Location
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;

        return $this;
    }

    /**
     * Get updated_at
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Return the board hash
     * @return string
     */
    public function getBoardHash()
    {
        return substr($this->geo_hash, 0, 4);
    }


    /**
     * Set address
     *
     * @param string $address
     * @return Location
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }
    
    public function getLocationLink(){
        return self::GOOGLE_MAPS_URL . $this->latitude . "+" . $this->longitude;
    }
}
