<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\TodoRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"read"}},
 *     denormalizationContext={"groups"={"write"}}
 * )
 * @ApiFilter(SearchFilter::class, properties={"title": "partial", "status": "partial", "user.id": "exact"})
 * @ApiFilter(DateFilter::class, properties={"dueOn"})
 * @ORM\Entity(repositoryClass=TodoRepository::class)
 */
class Todo implements HasUser
{
    use UserGetterSetterTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"read"})
     * @ApiProperty{identifier=true}
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Groups({"read", "write"})
     */
    private $title;

    /**
     * @var \DateTimeInterface|null
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"read", "write"})
     */
    private $dueOn;

    /**
     * @ORM\Column(type="string", length=10)
     * @Assert\NotBlank
     * @Assert\Choice({"pending", "completed"}, message="The value can be 'pending' or 'completed'.")
     * @Groups({"read", "write"})
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, cascade={"persist"}) // todo remove cascade={"persist"}
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"read"})
     */
    private $user;

    // todo add created at
    // todo add updated at

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDueOn()
    {
        return $this->dueOn;
    }

    /**
     * @return $this
     */
    public function setDueOn(?\DateTimeInterface $dueOn): self
    {
        $this->dueOn = $dueOn;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'title' => $this->getTitle(),
            'dueOn' => $this->getDueOn(),
            'status' => $this->getStatus(),
            'user' => $this->getUser() ? $this->getUser()->getEmail() : null,
        ];
    }
}
