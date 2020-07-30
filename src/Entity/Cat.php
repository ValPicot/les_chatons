<?php

namespace App\Entity;

use App\Traits\TimetableTraits;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CatRepository")
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable()
 */
class Cat
{
    use TimetableTraits;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"list_cats", "get_cat"})
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(minMessage="Min 3 caractères", maxMessage="Max 255 caractères", min="3", max="255")
     * @ORM\Column(type="string", length=255)
     * @Groups({"list_cats", "get_cat"})
     */
    private $name;

    /**
     * @Assert\Regex(pattern="/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/", message="Le format pour la couleur n'est pas bon (ex: #FFFF00)")
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"list_cats", "get_cat"})
     */
    private $color;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Race", inversedBy="cats")
     * @Groups({"list_cats", "get_cat"})
     */
    private $race;

    /**
     * @Assert\Length(maxMessage="Max 255 caractères", max="255")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $filename;

    /**
     * @var File|null
     * @Assert\Image(
     *      mimeTypes="image/jpeg"
     * )
     * @Vich\UploadableField(mapping="cat_image", fileNameProperty="filename")
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="cats")
     * @Groups({"get_cat"})
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param mixed $filename
     *
     * @return Cat
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    public function getImage(): ?File
    {
        return $this->image;
    }

    /**
     * @throws \Exception
     */
    public function setImage(?File $image): Cat
    {
        $this->image = $image;
        if ($this->image instanceof UploadedFile) {
            $this->updatedAt = new \DateTime('now');
        }

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

    public function getRace(): ?Race
    {
        return $this->race;
    }

    public function setRace(?Race $race): self
    {
        $this->race = $race;

        return $this;
    }
}
