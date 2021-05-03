<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PhoneRepository;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Annotations as OA;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass=PhoneRepository::class)
 * @OA\Schema()
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "show_phone",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups = "detail")
 * )
 * 
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "show_phone",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups = "list")
 * )
 * @Hateoas\Relation(
 *     "create",
 *     href = @Hateoas\Route("add_phone",
 *     absolute = true
 *     ),
 *     exclusion = @Hateoas\Exclusion(groups = "detail")
 * )
 * @Hateoas\Relation(
 *     "update",
 *     href = @Hateoas\Route("update_phone",
 *     parameters = { "id" = "expr(object.getId())" },
 *     absolute = true
 *     ),
 *     exclusion = @Hateoas\Exclusion(groups = "detail")
 * )
 * @Hateoas\Relation(
 *     "delete",
 *     href = @Hateoas\Route("delete_phone",
 *     parameters = { "id" = "expr(object.getId())" },
 *     absolute = true
 *     ),
 *     exclusion = @Hateoas\Exclusion(groups = "detail")
 * )
 */
class Phone
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"detail", "list"})
     * @OA\Property(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le modèle du produit est obligatoire !")
     * @Assert\Length(min=4, max=255, minMessage="Le modèle du produit doit avoir plus de 4 caractères !")
     * @Serializer\Groups({"detail", "list"})
     * @OA\Property(type="string")
     * @var string
     */
    private $model;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="La couleur du produit est obligatoire !")
     * @Assert\Length(min=2, max=255, minMessage="La couleur du produit doit avoir plus de 2 caractères !")
     * @Serializer\Groups({"detail", "list"})
     * @OA\Property(type="string")
     * @var string
     */
    private $color;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="La description du produit est obligatoire !")
     * @Assert\Length(min=10, max=255, minMessage="La description du produit doit avoir plus de 10 caractères !")
     * @Serializer\Groups({"detail"})
     * @OA\Property(type="string")
     * @var string
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Le prix du produit est obligatoire !")
     * @Serializer\Groups({"detail"})
     * @OA\Property(type="integer")
     * @var int
     */
    private $price;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="phones")
     */
    private $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addPhone($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removePhone($this);
        }

        return $this;
    }
}
