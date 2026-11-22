# Inventario de equipos de cómputo

Aplicación web escolar desarrollada en PHP para administrar un inventario de equipos tecnológicos. El sistema utiliza programación orientada a objetos, herencia y polimorfismo para manejar distintos tipos de productos mediante una estructura común.

La aplicación permite registrar computadoras de escritorio, laptops, teléfonos y televisiones. Cada tipo comparte los datos generales del producto y puede agregar características específicas.

## Clases

```text
EquipoComputo (abstracta)
├── PCEscritorio
├── Laptop
├── Telefono
└── Television

InventarioComputo administra objetos EquipoComputo
```

- **`EquipoComputo`** es la clase abstracta que concentra los datos comunes, como número de serie, marca, modelo y estado. Define métodos que las clases derivadas utilizan o especializan.
- **`PCEscritorio`** hereda de `EquipoComputo` y agrega el tipo de gabinete y la potencia de la fuente de alimentación.
- **`Laptop`** hereda de `EquipoComputo` y agrega el tamaño de pantalla y la duración de la batería. También conserva los datos técnicos de cómputo, como procesador, memoria RAM y almacenamiento.
- **`Telefono`** hereda de `EquipoComputo` y utiliza los atributos marca, modelo y número de serie.
- **`Television`** hereda de `EquipoComputo` y agrega el tamaño de pantalla expresado en pulgadas, además de marca, modelo y número de serie.
- **Polimorfismo:** las clases derivadas comparten la estructura de la clase base. El inventario puede recibir y recorrer objetos de distintos tipos mediante una referencia `EquipoComputo`, mientras cada objeto responde de acuerdo con su clase concreta.
- **`InventarioComputo`** administra los objetos, valida que no se repita el número de serie, permite buscar, filtrar y eliminar productos, y convierte los objetos a arreglos para conservarlos en la sesión.
- **`index.php`** presenta el formulario y la tabla del inventario. Según el tipo seleccionado, valida los campos correspondientes y crea una instancia de la clase derivada adecuada.

## Funciones

- Registrar y consultar computadoras de escritorio, laptops, teléfonos y televisiones.
- Capturar los atributos generales de cada producto y sus características específicas.
- Buscar productos por número de serie, marca o modelo y filtrar por tipo.
- Evitar números de serie duplicados.
- Eliminar registros y consultar el total de productos.
- Mostrar un resumen de cada objeto utilizando métodos comunes y comportamientos especializados.
- Demostrar polimorfismo al administrar diferentes subclases mediante la clase base.

## Ejecutar con XAMPP

1. Abre XAMPP Control Panel e inicia Apache.
2. Copia la carpeta del proyecto a `htdocs`, o configura un `Alias` para su ubicación.
3. Verifica que la ruta de los archivos PHP coincida con los `require_once` utilizados en el proyecto.
4. Abre la dirección local del proyecto en el navegador, por ejemplo:

```text
http://localhost/inventario-computo-con-herencia/
```

## Estructura del proyecto

```text
inventario-computo-con-herencia/
├── assets/
│   └── style.css
├── clases/
│   ├── EquipoComputo.php
│   ├── InventarioComputo.php
│   ├── Laptop.php
│   ├── PCEscritorio.php
│   ├── Telefono.php
│   └── Television.php
├── index.php
├── presentacion/
└── README.md
```

## Comprobar la sintaxis

Desde la carpeta raíz del proyecto, puedes revisar la sintaxis de los archivos con PHP:

```bash
php -l index.php
php -l clases/EquipoComputo.php
php -l clases/InventarioComputo.php
php -l clases/PCEscritorio.php
php -l clases/Laptop.php
php -l clases/Telefono.php
php -l clases/Television.php
```

## Concepto principal: polimorfismo

El polimorfismo permite utilizar objetos de diferentes clases derivadas mediante un tipo común. En este proyecto, `InventarioComputo` trabaja con objetos que heredan de `EquipoComputo`. De esta manera, puede agregar, consultar y recorrer distintos productos sin necesitar una administración completamente separada para cada uno.

Cada clase derivada representa un tipo de producto y aporta sus propios atributos y comportamiento. Por ejemplo, una televisión incluye las pulgadas de su pantalla, mientras que una laptop cuenta con información de batería y pantalla. El inventario puede trabajar con todos estos objetos usando la misma estructura general.

## Datos

Los registros del inventario se conservan en la sesión del navegador mediante `$_SESSION`; esta implementación no utiliza una base de datos. Al recuperar los datos de la sesión, `InventarioComputo` reconstruye los objetos de acuerdo con el tipo de producto guardado.
