<?php

namespace Push\EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Client
 *
 * @ORM\Table(name="client")
 * @ORM\Entity(repositoryClass="Push\EntityBundle\Repository\ClientRepository")
 */
class Client
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
     *
     * @ORM\Column(name="token", type="text")
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(name="logo", type="string", length=255, nullable=true)
     */
    private $logo;

    /**
     * @var string
     *
     * @ORM\Column(name="vapid_public_key", type="string", length=255)
     */
    private $vapidPublicKey;

    /**
     * @var string
     *
     * @ORM\Column(name="vapid_private_key", type="string", length=255)
     */
    private $vapidPrivateKey;

    /**
     * @var string
     *
     * @ORM\Column(name="contact_email", type="string", length=255)
     */
    private $contactEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="primary_color", type="string", length=255, nullable=true)
     */
    private $primaryColor;

    /**
     * @var string
     *
     * @ORM\Column(name="secondary_color", type="string", length=255, nullable=true)
     */
    private $secondaryColor;


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
     * @return Client
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
     * Set token
     *
     * @param string $token
     *
     * @return Client
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set logo
     *
     * @param string $logo
     *
     * @return Client
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo
     *
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set vapidPublicKey
     *
     * @param string $vapidPublicKey
     *
     * @return Client
     */
    public function setVapidPublicKey($vapidPublicKey)
    {
        $this->vapidPublicKey = $vapidPublicKey;

        return $this;
    }

    /**
     * Get vapidPublicKey
     *
     * @return string
     */
    public function getVapidPublicKey()
    {
        return $this->vapidPublicKey;
    }

    /**
     * Set vapidPrivateKey
     *
     * @param string $vapidPrivateKey
     *
     * @return Client
     */
    public function setVapidPrivateKey($vapidPrivateKey)
    {
        $this->vapidPrivateKey = $vapidPrivateKey;

        return $this;
    }

    /**
     * Get vapidPrivateKey
     *
     * @return string
     */
    public function getVapidPrivateKey()
    {
        return $this->vapidPrivateKey;
    }

    /**
     * Set contactEmail
     *
     * @param string $contactEmail
     *
     * @return Client
     */
    public function setContactEmail($contactEmail)
    {
        $this->contactEmail = $contactEmail;

        return $this;
    }

    /**
     * Get contactEmail
     *
     * @return string
     */
    public function getContactEmail()
    {
        return $this->contactEmail;
    }

    /**
     * Set primaryColor
     *
     * @param string $primaryColor
     *
     * @return Client
     */
    public function setPrimaryColor($primaryColor)
    {
        $this->primaryColor = $primaryColor;

        return $this;
    }

    /**
     * Get primaryColor
     *
     * @return string
     */
    public function getPrimaryColor()
    {
        return $this->primaryColor;
    }

    /**
     * Set secondaryColor
     *
     * @param string $secondaryColor
     *
     * @return Client
     */
    public function setSecondaryColor($secondaryColor)
    {
        $this->secondaryColor = $secondaryColor;

        return $this;
    }

    /**
     * Get secondaryColor
     *
     * @return string
     */
    public function getSecondaryColor()
    {
        return $this->secondaryColor;
    }
}

