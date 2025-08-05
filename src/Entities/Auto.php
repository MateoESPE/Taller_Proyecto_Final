<?php declare(strict_types=1);

abstract class Auto
{
    private int $id;
    private string $marca;
    private string $modelo;
    private string $color;
    private int $anio;

    public function __construct(int $idC, 
                                string $marcaC, 
                                string $modeloC, 
                                string $colorC, 
                                int $anioC)
    {
        $this->id = $idC;
        $this->marca = $marcaC;
        $this->modelo = $modeloC;
        $this->color = $colorC;
        $this->anio = $anioC;
    }

    public function getId(): int { return $this->id; }
    public function getMarca(): string { return $this->marca; }
    public function getModelo(): string { return $this->modelo; }
    public function getColor(): string { return $this->color; }
    public function getAnio(): int { return $this->anio; }

    public function setId(int $idIn): void { $this->id = $id; }

    public function setMarca(string $marcaIn): void 
    {
        $marcaTemp = trim($marcaIn); 
        if (trim($marcaIn) === '') {
            throw new \InvalidArgumentException("La marca no debe estar vacía");
        }
        $this->marca = $marcaTemp; 
    }

    public function setModelo(string $modeloIn): void 
    {
        $modeloTemp = trim($modeloIn);
        if (trim($modeloIn) === '') {
            throw new \InvalidArgumentException("El modelo no debe estar vacío");
        }
        $this->modelo = $modeloTemp;
    }

    public function setColor(string $colorIn): void 
    {
        $colorTemp = trim($colorIn);
        if (trim($colorIn) === '') {
            throw new \InvalidArgumentException("El color no debe ser nulo");
        }
        $this->color = $colorTemp;
    }

    public function setAnio(int $anioIn): void
    {
        if($anioIn <1900 || $anioIn > (int)date("Y")+1) {
            throw new \InvalidArgumentException("Anio de fabriacación INVALIDO");
        }
        $this->anio = $anioIn;
    }

    abstract public function getInfo(): string;
}