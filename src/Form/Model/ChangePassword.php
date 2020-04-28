<?php

namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

class ChangePassword
{
    /**
     * @SecurityAssert\UserPassword(
     *     message = "Ancien mot de passe incorrect"
     * )
     */
    protected $oldPassword;

    /**
     *@Assert\Length(min="8", minMessage="Votre mot de passe doit faire minimum 8 caractères.")
     */
    private $newPassword;
 
    /**
     * @Assert\EqualTo(propertyPath="newPassword", message="Vous n'avez pas tapé le même mot de passe")
     */
    public $confirm_password;

    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }
 
    public function setOldPassword(string $oldPassword): self
    {
        $this->oldPassword = $oldPassword;
 
        return $this;
    }
 
    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }
 
    public function setNewPassword(string $newPassword): self
    {
        $this->newPassword = $newPassword;
 
        return $this;
    }

}