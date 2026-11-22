<?php

declare(strict_types=1);

require_once __DIR__ . '/EquipoComputo.php';

/** Representa un teléfono dentro del inventario. Hereda los datos comunes. */
class Telefono extends EquipoComputo
{
    /** Los teléfonos no necesitan procesador, RAM ni almacenamiento en este modelo. */
    public function __construct(
        string $numeroSerie,
        string $marca,
        string $modelo,
        string $estado = 'Disponible'
    ) {
        parent::__construct($numeroSerie, $marca, $modelo, '', 0, '', $estado);
    }

    /** Identifica el tipo de producto para el polimorfismo. */
    public function getTipo(): string
    {
        return 'Telefono';
    }
}
