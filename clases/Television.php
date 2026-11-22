<?php

declare(strict_types=1);

require_once __DIR__ . '/EquipoComputo.php';

/** Representa una televisión y agrega su tamaño de pantalla. */
class Television extends EquipoComputo
{
    private float $pulgadas;

    public function __construct(
        string $numeroSerie,
        string $marca,
        string $modelo,
        float $pulgadas,
        string $estado = 'Disponible'
    ) {
        // Los campos de computadora se dejan vacíos porque no aplican a una televisión.
        parent::__construct($numeroSerie, $marca, $modelo, '', 0, '', $estado);
        $this->pulgadas = $pulgadas;
    }

    /** Identifica el tipo de producto para el polimorfismo. */
    public function getTipo(): string
    {
        return 'Television';
    }

    public function getPulgadas(): float
    {
        return $this->pulgadas;
    }

    /** Extiende el resumen común para incluir el tamaño de la televisión. */
    public function getResumen(): string
    {
        return parent::getResumen() . sprintf(' | Pantalla: %.1f pulgadas', $this->pulgadas);
    }

    /** Agrega las pulgadas a los datos que se guardan en la sesión. */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'pulgadas' => $this->pulgadas,
        ]);
    }
}
