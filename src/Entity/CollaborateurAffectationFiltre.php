<?php

namespace App\Entity;

use DateTime;

class CollaborateurAffectationFiltre
{
    public ?DateTime $debut = null;
    public ?Fonction $fonction = null;

    public function getDebut(): ?DateTime
    {
        return $this->debut;
    }

    public function setDebut(?DateTime $debut): self
    {
        $this->debut = $debut;
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
