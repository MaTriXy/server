<?php

namespace Lollypop\GearBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Board
 *
 * @author seshachalam
 */

/**
 * @ORM\Entity(repositoryClass="Lollypop\GearBundle\Entity\Repository\BoardRepository")
 * @ORM\Table(name="board")
 */
class Board implements \JsonSerializable {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Location")
     * @ORM\JoinColumn(name="location", referencedColumnName="id", onDelete="CASCADE")
     * */
    private $location;

    /**
     * @ORM\Column(type="string", length=4, nullable=false)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="Message", mappedBy="board")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $messages;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\OneToMany(targetEntity="GCMRegID", mappedBy="board")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $gcm_reg_ids;

    public function __construct() {
        $this->created_at = new \DateTime();
        $this->messages = new \Doctrine\Common\Collections\ArrayCollection();
        $this->gcm_reg_ids = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set location
     *
     * @param \Lollypop\GearBundle\Entity\Location $location
     * @return Board
     */
    public function setLocation(\Lollypop\GearBundle\Entity\Location $location = null) {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return \Lollypop\GearBundle\Entity\Location 
     */
    public function getLocation() {
        return $this->location;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Board
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
     * @return Board
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

    /**
     * Add messages
     *
     * @param \Lollypop\GearBundle\Entity\Message $messages
     * @return Board
     */
    public function addMessage(\Lollypop\GearBundle\Entity\Message $messages) {
        $this->messages[] = $messages;

        return $this;
    }

    /**
     * Remove messages
     *
     * @param \Lollypop\GearBundle\Entity\Message $messages
     */
    public function removeMessage(\Lollypop\GearBundle\Entity\Message $messages) {
        $this->messages->removeElement($messages);
    }

    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMessages() {
        return $this->messages;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Board
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Add gcm_reg_ids
     *
     * @param \Lollypop\GearBundle\Entity\GCMRegID $gcmRegIds
     * @return Board
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

    public function jsonSerialize() {
        return empty($this->name) ? array('id' => $this->id) : array('id' => $this->id, 'name' => $this->name);
    }

}
