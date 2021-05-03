<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use OpenApi\Annotations as OA;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity("email", message="email déjà utilisé !")
 * @OA\Schema()
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "show_user",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      ),
 *      embedded = @Hateoas\Embedded("expr(object.getPhones())"),
 *      exclusion = @Hateoas\Exclusion(groups = "detail")
 * )
 * 
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "show_user",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups = "list")
 * )
 * @Hateoas\Relation(
 *     "create",
 *     href = @Hateoas\Route("add_user",
 *     absolute = true
 *     ),
 *     exclusion = @Hateoas\Exclusion(groups = "detail")
 * )
 * @Hateoas\Relation(
 *     "delete",
 *     href = @Hateoas\Route("delete_user",
 *     parameters = { "id" = "expr(object.getId())" },
 *     absolute = true
 *     ),
 *     exclusion = @Hateoas\Exclusion(groups = "detail")
 * )
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"detail"})
     * @OA\Property(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180)
     * @Serializer\Groups({"detail"})
     * @Assert\NotBlank(message=" Merci d'entrer votre email !")
     * @Assert\Email(message="email non valide !")
     * @OA\Property(type="string")
     * @var string
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"detail", "list"})
     * @Assert\NotBlank(message=" Merci d'entrer votre prénom !")
     * @Assert\Length(min=4, max=255, minMessage="Votre prénom doit comporter plus de 4 caractères !")
     * @OA\Property(type="string")
     * @var string
     */
    private $first_name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"detail", "list"})
     * @Assert\NotBlank(message=" Merci d'entrer votre nom !")
     * @Assert\Length(min=4, max=255, minMessage="Votre nom doit comporter plus de 4 caractères !")
     * @OA\Property(type="string")
     * @var string
     */
    private $last_name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"detail"})
     * @Assert\NotBlank(message=" Merci d'entrer votre adresse !")
     * @Assert\Length(min=10, max=255, minMessage="Votre adresse doit comporter plus de 10 caractères !")
     * @OA\Property(type="string")
     * @var string
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=100)
     * @Serializer\Groups({"detail"})
     * @Assert\NotBlank(message=" Merci d'entrer votre code postal !")
     * @Assert\Length(min=4, max=255, minMessage="Votre code postal doit comporter plus de 4 caractères !")
     * @OA\Property(type="string")
     * @var string
     */
    private $postal_code;

    /**
     * @ORM\Column(type="string", length=100)
     * @Serializer\Groups({"detail", "list"})
     * @Assert\NotBlank(message=" Merci d'entrer le nom de votre ville !")
     * @Assert\Length(min=3, max=255, minMessage="Le nom de votre ville doit comporter plus de 3 caractères !")
     * @OA\Property(type="string")
     * @var string
     */
    private $city;

    /**
     * @ORM\Column(type="datetime")
     * @Serializer\Groups({"detail"})
     * @OA\Property(type="string", format="date-time")
     * @var DateTimeInterface
     */
    private $created_at;

    /**
     * @ORM\ManyToOne(targetEntity=Shop::class, inversedBy="users", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $shop;

    /**
     * @ORM\ManyToMany(targetEntity=Phone::class, inversedBy="users")
     */
    private $phones;

    public function __construct()
    {
        $this->phones = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): self
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postal_code;
    }

    public function setPostalCode(string $postal_code): self
    {
        $this->postal_code = $postal_code;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getShop(): ?Shop
    {
        return $this->shop;
    }

    public function setShop(?Shop $shop): self
    {
        $this->shop = $shop;

        return $this;
    }

    /**
     * @return Collection|Phone[]
     */
    public function getPhones(): Collection
    {
        return $this->phones;
    }

    public function addPhone(Phone $phone): self
    {
        if (!$this->phones->contains($phone)) {
            $this->phones[] = $phone;
        }

        return $this;
    }

    public function removePhone(Phone $phone): self
    {
        $this->phones->removeElement($phone);

        return $this;
    }
}
