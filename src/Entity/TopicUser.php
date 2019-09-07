<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TopicUserRepository")
 */
class TopicUser
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Topic")
     * @ORM\JoinColumn(nullable=false)
     */
    private $topic_id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function __construct()
    {
        $preAliasList = [
            "Super",
            "Ultra",
            "xXpro",
            "CatLover",
            "Destroyer",
            "Paul",
            "Ben",
            "Dracula",
            "Infinity",
            "Valkon",
            "Snow"
        ];

        $connectors = [
            '_',
            'x',
            'xXx',
            '-',
            '.',
            ' ',
            ''
        ];

        $postAliasList = [
            "LP",
            "GamerXx",
            "99",
            "Alpaka",
            "42",
            "Racer",
            "Dangerous",
            "Snicker",
            "Forest",
            "Ninja",
            "Dragon"
        ];

        $firstPart = $preAliasList[mt_rand(0, count($preAliasList) - 1)];
        $connector = $connectors[mt_rand(0, count($connectors) - 1)];
        $lastPart = $postAliasList[mt_rand(0, count($postAliasList) - 1)];

        $username = $firstPart . $connector . $lastPart;

        $this->setUsername($username);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getTopicId(): ?Topic
    {
        return $this->topic_id;
    }

    public function setTopicId(Topic $topic_id): self
    {
        $this->topic_id = $topic_id;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }


}
