<?php

namespace Push\EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WebNotification
 *
 * @ORM\Table(name="web_notification")
 * @ORM\Entity(repositoryClass="Push\EntityBundle\Repository\WebNotificationRepository")
 */
class WebNotification
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
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user", referencedColumnName="id")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="endpoint", type="text")
     */
    private $endpoint;

    /**
     * @var string
     *
     * @ORM\Column(name="auth", type="text")
     */
    private $auth;

    /**
     * @var string
     *
     * @ORM\Column(name="public_key", type="text")
     */
    private $publicKey;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="Client")
     * @ORM\JoinColumn(name="client", referencedColumnName="id")
     */
    private $client;

    /**
     * @var string
     *
     * @ORM\Column(name="reference", type="text")
     */
    private $reference;


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
     * Set user
     *
     * @param integer $user
     *
     * @return IaWebNotification
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return int
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set endpoint
     *
     * @param string $endpoint
     *
     * @return IaWebNotification
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    /**
     * Get endpoint
     *
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * Set auth
     *
     * @param string $auth
     *
     * @return IaWebNotification
     */
    public function setAuth($auth)
    {
        $this->auth = $auth;

        return $this;
    }

    /**
     * Get auth
     *
     * @return string
     */
    public function getAuth()
    {
        return $this->auth;
    }

    /**
     * Set publicKey
     *
     * @param string $publicKey
     *
     * @return IaWebNotification
     */
    public function setPublicKey($publicKey)
    {
        $this->publicKey = $publicKey;

        return $this;
    }

    /**
     * Get publicKey
     *
     * @return string
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }

    /**
     * Set client
     *
     * @param integer $client
     *
     * @return IaWebNotification
     */
    public function setClient($client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return int
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set reference
     *
     * @param integer $reference
     *
     * @return IaWebNotification
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference
     *
     * @return int
     */
    public function getReference()
    {
        return $this->reference;
    }
}

