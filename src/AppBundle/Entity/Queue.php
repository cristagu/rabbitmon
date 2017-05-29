<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Queue
 *
 * @ORM\Table(name="queue")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\QueueRepository")
 */
class Queue
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
     * @ORM\Column(name="vhost", type="string", length=32)
     */
    private $vhost;

    /**
     * @var Server
     *
     * @ORM\ManyToOne(targetEntity="Server", inversedBy="queues")
     * @ORM\JoinColumn(name="server_name", referencedColumnName="name")
     */
    private $server;

    /**
     * @var int
     */
    private $messages;

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
     * @return Queue
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
     * Set vhost
     *
     * @param string $vhost
     *
     * @return Queue
     */
    public function setVhost($vhost)
    {
        $this->vhost = $vhost;

        return $this;
    }

    /**
     * Get vhost
     *
     * @return string
     */
    public function getVhost()
    {
        return $this->vhost;
    }

    /**
     * Set server
     *
     * @param Server $server
     *
     * @return Queue
     */
    public function setServer($server)
    {
        $this->server = $server;

        return $this;
    }

    /**
     * Get server
     *
     * @return Server
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * @return int
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @param int $messages
     */
    public function setMessages($messages)
    {
        $this->messages = $messages;
    }
}

