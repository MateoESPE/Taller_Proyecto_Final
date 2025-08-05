<?php declare(strict_types=1);

namespace App\Entities;

use Auto;

class Camioneta extends Auto{
    private string $cabina;
    private float $capacidadCarga;

    public function __construct(int $idC, 
                                string $marcaC, 
                                string $modeloC, 
                                string $colorC, 
                                int $anioC,
                                string $cabinaC, 
                                float $capacidadCargaC)
    {
        parent::__construct($idC, $marcaC, $modeloC, $colorC, $anioC);
        $this->cabina = $cabinaC;
        $this->capacidadCarga = $capacidadCargaC;
    }

    public function getCabina(): string { return $this->cabina; }
    public function getCapacidadCarga(): float { return $this->capacidadCarga; }

    public function setCabina(string $cabinaIn): void { $this -> cabina = $cabinaIn; }
    public function setCapacidadCarga(float $capacidadCargaIn): void { $this->capacidadCarga = $capacidadCargaIn;}

    public function getInfo(): string{
        return "Auto: ".$this->getMarca()
             . "Modelo: ".$this->getModelo()
             . "Anio: ".$this->getAnio();
    }
}