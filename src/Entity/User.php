<?php

namespace App\Entity;

use App\Traits\TimetableTraits;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(
 *     fields={"email"},
 *     message="entity.user.unique"
 * )
 */
class User implements UserInterface, \Serializable
{
    const ROLE_ADMIN = 'ROLE_ADMIN';

    use TimetableTraits;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"get_cat"})
     */
    private $id;

    /**
     * @Assert\NotBlank(groups={"user_create"})
     * @Assert\Length(minMessage="Min 3 caractères", maxMessage="Max 255 caractères", min="3", max="255")
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="string")
     * @Groups({"get_cat"})
     */
    private $password;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Cat", mappedBy="user")
     */
    private $cats;

    /**
     * @ORM\Column(type="boolean", options={"default" : 1})
     */
    private $isActive;

    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     * @Assert\Length(maxMessage="Max 255 caractères", max="255")
     * @ORM\Column(type="string", length=255)
     * @Groups({"get_cat"})
     */
    private $email;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(minMessage="Min 2 caractères", maxMessage="Max 255 caractères", min="2", max="255")
     * @ORM\Column(type="string", length=255)
     * @Groups({"get_cat"})
     */
    private $name;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(minMessage="Min 2 caractères", maxMessage="Max 255 caractères", min="2", max="255")
     * @ORM\Column(type="string", length=255)
     * @Groups({"get_cat"})
     */
    private $lastname;

    /**
     * @Assert\Length(maxMessage="Max 255 caractères", max="255")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $resetToken;

    /**
     * @ORM\Column(type="string", unique=true, nullable=true)
     */
    private $apiToken;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $dark_mode;

    public function __construct()
    {
        $this->cats = new ArrayCollection();
        $this->isActive = 1;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return (string) $this->email;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return ['ROLE_USER'];
     *     }
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles()
    {
        if (empty($this->roles)) {
            return ['ROLE_OWNER_CAT'];
        }

        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function hasRoles(string $role)
    {
        return in_array($role, $this->getRoles());
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
    }

    /**
     * String representation of object.
     *
     * @see https://php.net/manual/en/serializable.serialize.php
     *
     * @return string the string representation of the object or null
     *
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->email,
            $this->password,
        ]);
    }

    /**
     * Constructs the object.
     *
     * @see https://php.net/manual/en/serializable.unserialize.php
     *
     * @param string $serialized <p>
     *                           The string representation of the object.
     *                           </p>
     *
     * @return void
     *
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->email,
            $this->password
            ) = unserialize($serialized, ['allowed_classes' => false]);
    }

    /**
     * @return Collection|Cat[]
     */
    public function getCats(): Collection
    {
        return $this->cats;
    }

    public function addCat(Cat $cat): self
    {
        if (!$this->cats->contains($cat)) {
            $this->cats[] = $cat;
            $cat->setUserId($this);
        }

        return $this;
    }

    public function removeCat(Cat $cat): self
    {
        if ($this->cats->contains($cat)) {
            $this->cats->removeElement($cat);
            // set the owning side to null (unless already changed)
            if ($cat->getUserId() === $this) {
                $cat->setUserId(null);
            }
        }

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    public function getApiToken()
    {
        return $this->apiToken;
    }

    public function setApiToken($apiToken)
    {
        $this->apiToken = $apiToken;

        return $this;
    }

    public function getDarkMode(): ?bool
    {
        return $this->dark_mode;
    }

    public function setDarkMode(?bool $dark_mode): self
    {
        $this->dark_mode = $dark_mode;

        return $this;
    }
}
