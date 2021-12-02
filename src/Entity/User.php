<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="Il y a déjà un compte avec cet email")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email(
     *     message = "L'email n'est pas valide"
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $city;

    /**
     * @ORM\OneToMany(targetEntity=Annonce::class, mappedBy="user", cascade={"remove"})
     */
    private $annonces;

    /**
     * @ORM\OneToMany(targetEntity=Article::class, mappedBy="author", cascade={"remove"})
     */
    private $articles;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $avatar;

    /**
     * @ORM\OneToMany(targetEntity=Animal::class, mappedBy="user", cascade={"remove"})
     */
    private $animal;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="writer", orphanRemoval=true, cascade={"remove"})
     */
    private $messages;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="sender", orphanRemoval=true, cascade={"remove"})
     */
    private $messages_send;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="receiver", orphanRemoval=true, cascade={"remove"})
     */
    private $messages_receive;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $phone;

    /**
     * @ORM\OneToMany(targetEntity=Contact::class, mappedBy="user", cascade={"remove"})
     */
    private $contacts;

    /**
     * @ORM\ManyToOne(targetEntity=Blacklist::class, inversedBy="users")
     */
    private $blacklist;

    public function __construct()
    {
        $this->annonces = new ArrayCollection();
        $this->articles = new ArrayCollection();
        $this->animal = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->messages_send = new ArrayCollection();
        $this->messages_receive = new ArrayCollection();
        $this->contacts = new ArrayCollection();
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

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

    /**
     * @return Collection|Annonce[]
     */
    public function getAnnonces(): Collection
    {
        return $this->annonces;
    }

    public function addAnnonce(Annonce $annonce): self
    {
        if (!$this->annonces->contains($annonce)) {
            $this->annonces[] = $annonce;
            $annonce->setUser($this);
        }

        return $this;
    }

    public function removeAnnonce(Annonce $annonce): self
    {
        if ($this->annonces->removeElement($annonce)) {
            // set the owning side to null (unless already changed)
            if ($annonce->getUser() === $this) {
                $annonce->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Article[]
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->setAuthor($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getAuthor() === $this) {
                $article->setAuthor(null);
            }
        }

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(null|string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return Collection|Animal[]
     */
    public function getAnimal(): Collection
    {
        return $this->animal;
    }

    public function addAnimal(Animal $animal): self
    {
        if (!$this->animal->contains($animal)) {
            $this->animal[] = $animal;
            $animal->setUser($this);
        }

        return $this;
    }

    public function removeAnimal(Animal $animal): self
    {
        if ($this->animal->removeElement($animal)) {
            // set the owning side to null (unless already changed)
            if ($animal->getUser() === $this) {
                $animal->setUser(null);
            }
        }

        return $this;
    }

    // /**
    //  * @return Collection|Message[]
    //  */
    // public function getMessages(): Collection
    // {
    //     return $this->messages;
    // }

    // public function addMessage(Message $message): self
    // {
    //     if (!$this->messages->contains($message)) {
    //         $this->messages[] = $message;
    //         $message->setWriter($this);
    //     }

    //     return $this;
    // }

    // public function removeMessage(Message $message): self
    // {
    //     if ($this->messages->removeElement($message)) {
    //         // set the owning side to null (unless already changed)
    //         if ($message->getWriter() === $this) {
    //             $message->setWriter(null);
    //         }
    //     }

    //     return $this;
    // }

    /**
     * @return Collection|Message[]
     */
    public function getMessagesSend(): Collection
    {
        return $this->messages_send;
    }

    public function addMessagesSend(Message $messagesSend): self
    {
        if (!$this->messages_send->contains($messagesSend)) {
            $this->messages_send[] = $messagesSend;
            $messagesSend->setSender($this);
        }

        return $this;
    }

    public function removeMessagesSend(Message $messagesSend): self
    {
        if ($this->messages_send->removeElement($messagesSend)) {
            // set the owning side to null (unless already changed)
            if ($messagesSend->getSender() === $this) {
                $messagesSend->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessagesReceive(): Collection
    {
        return $this->messages_receive;
    }

    public function addMessagesReceive(Message $messagesReceive): self
    {
        if (!$this->messages_receive->contains($messagesReceive)) {
            $this->messages_receive[] = $messagesReceive;
            $messagesReceive->setReceiver($this);
        }

        return $this;
    }

    public function removeMessagesReceive(Message $messagesReceive): self
    {
        if ($this->messages_receive->removeElement($messagesReceive)) {
            // set the owning side to null (unless already changed)
            if ($messagesReceive->getReceiver() === $this) {
                $messagesReceive->setReceiver(null);
            }
        }

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return Collection|Contact[]
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addContact(Contact $contact): self
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts[] = $contact;
            $contact->setUser($this);
        }

        return $this;
    }

    public function removeContact(Contact $contact): self
    {
        if ($this->contacts->removeElement($contact)) {
            // set the owning side to null (unless already changed)
            if ($contact->getUser() === $this) {
                $contact->setUser(null);
            }
        }

        return $this;
    }

    public function getBlacklist(): ?Blacklist
    {
        return $this->blacklist;
    }

    public function setBlacklist(?Blacklist $blacklist): self
    {
        $this->blacklist = $blacklist;

        return $this;
    }

    public function __toString()
    {
        return $this->firstname . ' ' . $this->lastname . ' : ' . $this->email;
    }
}
