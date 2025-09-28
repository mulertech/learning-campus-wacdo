<?php

namespace App\Entity;

class CollaborateurFiltre
{
    public ?string $nom = null;
    public ?Fonction $fonction = null;

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getFonction(): ?Fonction
    {
        return $this->fonction;
    }

    public function setFonction(?Fonction $fonction): self
    {
        $this->fonction = $fonction;
        return $this;
    }
}
