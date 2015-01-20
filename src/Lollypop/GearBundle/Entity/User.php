<?php

namespace Lollypop\GearBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Description of User
 *
 * @author seshachalam
 */
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="gear_user")
 */
class User extends BaseUser implements EquatableInterface {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $is_password_set;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $first_name;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $last_name;

    /**
     * @ORM\OneToMany(targetEntity="GCMRegID", mappedBy="user")
     */
    private $gcm_reg_ids;

    /**
     * @ORM\ManyToMany(targetEntity="Location", inversedBy="users")
     * @ORM\JoinTable(name="users_locations")
     */
    private $locations;

    /**
     * @ORM\OneToOne(targetEntity="Location")
     * @ORM\JoinColumn(name="location", referencedColumnName="id", onDelete="CASCADE")
     * */
    private $current_location;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $active_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * Constructor
     */
    public function __construct() {

        parent::__construct();

        $this->gcm_reg_ids = new \Doctrine\Common\Collections\ArrayCollection();
        $this->locations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->created_at = new \DateTime();
        $this->plainPassword = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $this->is_password_set = false;
        $this->roles = array('ROLE_USER'); //need to check with rams
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set first_name
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName) {
        $this->first_name = $firstName;

        return $this;
    }

    /**
     * Get first_name
     *
     * @return string 
     */
    public function getFirstName() {
        return $this->first_name;
    }

    /**
     * Set last_name
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName) {
        $this->last_name = $lastName;

        return $this;
    }

    /**
     * Get last_name
     *
     * @return string 
     */
    public function getLastName() {
        return $this->last_name;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email) {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Add gcm_reg_ids
     *
     * @param \Lollypop\GearBundle\Entity\GCMRegID $gcmRegIds
     *
     * @return User
     */
    public function addGcmRegId(\Lollypop\GearBundle\Entity\GCMRegID $gcmRegIds) {
        $this->gcm_reg_ids[] = $gcmRegIds;

        return $this;
    }

    /**
     * Remove gcm_reg_ids
     *
     * @param \Lollypop\GearBundle\Entity\GCMRegID $gcmRegIds
     */
    public function removeGcmRegId(\Lollypop\GearBundle\Entity\GCMRegID $gcmRegIds) {
        $this->gcm_reg_ids->removeElement($gcmRegIds);
    }

    /**
     * Get gcm_reg_ids
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGcmRegIds() {
        return $this->gcm_reg_ids;
    }

    /**
     * Add locations
     *
     * @param \Lollypop\GearBundle\Entity\Location $locations
     *
     * @return User
     */
    public function addLocation(\Lollypop\GearBundle\Entity\Location $locations) {
        $this->locations[] = $locations;

        return $this;
    }

    /**
     * Remove locations
     *
     * @param \Lollypop\GearBundle\Entity\Location $locations
     */
    public function removeLocation(\Lollypop\GearBundle\Entity\Location $locations) {
        $this->locations->removeElement($locations);
    }

    /**
     * Get locations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLocations() {
        return $this->locations;
    }

    /**
     * Set current_location
     *
     * @param \Lollypop\GearBundle\Entity\Location $currentLocation
     *
     * @return User
     */
    public function setCurrentLocation(\Lollypop\GearBundle\Entity\Location $currentLocation = null) {
        $this->current_location = $currentLocation;

        return $this;
    }

    /**
     * Get current_location
     *
     * @return \Lollypop\GearBundle\Entity\Location 
     */
    public function getCurrentLocation() {
        return $this->current_location;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return User
     */
    public function setActive($active) {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive() {
        return $this->active;
    }

    /**
     * Set active_at
     *
     * @param \DateTime $activeAt
     *
     * @return User
     */
    public function setActiveAt($activeAt) {
        $this->active_at = $activeAt;

        return $this;
    }

    /**
     * Get active_at
     *
     * @return \DateTime 
     */
    public function getActiveAt() {
        return $this->active_at;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return User
     */
    public function setCreatedAt($createdAt) {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime 
     */
    public function getCreatedAt() {
        return $this->created_at;
    }

    /**
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return User
     */
    public function setUpdatedAt($updatedAt) {
        $this->updated_at = $updatedAt;

        return $this;
    }

    /**
     * Get updated_at
     *
     * @return \DateTime 
     */
    public function getUpdatedAt() {
        return $this->updated_at;
    }

    public function setIsPasswordSet($isSet) {
        $this->is_password_set = (Boolean) $isSet;

        return $this;
    }

    public function getIsPasswordSet() {
        return $this->is_password_set;
    }

    public function isEqualTo(UserInterface $user) {

        if (!$user instanceof Lollypop\GearBundle\Entity\User) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->salt !== $user->getSalt()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }

}
