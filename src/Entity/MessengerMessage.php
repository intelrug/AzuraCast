<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Internal table used for Symfony Messenger handling.
 *
 * @ORM\Table(name="messenger_messages", indexes={
 *     @ORM\Index(columns={"queue_name"}),
 *     @ORM\Index(columns={"available_at"}),
 *     @ORM\Index(columns={"delivered_at"}),
 * })
 * @ORM\Entity(readOnly=true)
 *
 * @internal
 */
class MessengerMessage
{
    /**
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(name="body", type="text")
     * @var string
     */
    protected $body;

    /**
     * @ORM\Column(name="headers", type="text")
     * @var string
     */
    protected $headers;

    /**
     * @ORM\Column(name="queue_name", type="string", length=190)
     * @var string
     */
    protected $queueName;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @ORM\Column(name="available_at", type="datetime")
     * @var \DateTime
     */
    protected $availableAt;

    /**
     * @ORM\Column(name="delivered_at", type="datetime", nullable=true)
     * @var \DateTime
     */
    protected $deliveredAt;
}
