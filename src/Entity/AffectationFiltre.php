<?php

namespace App\Entity;

use DateTime;

class AffectationFiltre
{
    public ?string $ville = null;
    public ?DateTime $debut = null;
    public ?DateTime $fin = null;
    public ?Fonction $fonction = null;

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): self
    {
        $this->ville = $ville;
        return $this;
    }

    public function getDebut(): ?DateTime
    {
        return $this->debut;
    }

    public function setDebut(?DateTime $debut): self
    {
        $this->debut = $debut;
        return $this;
    }

    public function getFin(): ?DateTime
    {
        return $this->fin;
    }

    public function setFin(?DateTime $fin): self
    {
        $this->fin = $fin;
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
