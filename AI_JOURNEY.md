# AI_JOURNEY.md

## Mi experiencia como Tech Lead de la IA

### Contexto
Este proyecto fue desarrollado con apoyo de Claude (Anthropic) como asistente de IA.
Usé la IA como guía técnica y acelerador de desarrollo, tomando decisiones sobre 
arquitectura, orden de construcción y cómo resolver los problemas que surgieron en el camino.

---

## 1. Prompts Clave

### Arranque del proyecto
Antes de escribir una sola línea de código, le pedí a la IA que me ayudara a:
- Estimar el tiempo de desarrollo realista dado que era mi primer proyecto en Laravel y Docker
- Definir todos los requisitos de instalación en orden
- Establecer puntos de avance diarios para llegar a la fecha de entrega
- Analizar los requerimientos del proyecto para entender qué había que construir antes de empezar

Esto me permitió tener una visión clara del proyecto completo antes de tocar la terminal.

### Migraciones y modelos
Le pedí que generara las 4 migraciones con sus relaciones correctas, especialmente
la tabla pivote `pedido_producto` para la relación muchos a muchos entre pedidos y productos.

### Dashboard
Le pedí que implementara el controlador y las vistas Blade con Tailwind, con las
4 categorías exactas del negocio y paginación real de base de datos.

### Comando Artisan
Le pedí que implementara el motor de cargos exprés con las 3 condiciones exactas
usando Eloquent, y que el filtro se hiciera directamente en SQL sin traer datos a memoria.

---

## 2. Correcciones y Decisiones Técnicas

### El problema de Docker sin internet
El obstáculo más grande fue que Docker no tenía salida a internet para instalar
los paquetes dentro del contenedor. El problema era que tenía ZeroTier (una VPN)
instalado en Linux, y sus reglas de red creaban un conflicto con Docker al usar
un segmento de red diferente. Los contenedores no podían resolver DNS y por lo tanto
no podían descargar nada.

La solución fue reiniciar Ubuntu para limpiar las reglas de red residuales de ZeroTier.
Una vez limpio, Docker tuvo acceso a internet sin problema.

### Comando Artisan — optimización del update
La primera versión del comando usaba un loop con `save()` individual por cada pedido:

```php
// Versión inicial — ineficiente
foreach ($pedidos as $pedido) {
    $pedido->total = round($pedido->total * 1.10, 2);
    $pedido->save();
}
```

Lo refactoricé a un update masivo con `DB::raw` que ejecuta una sola query en la BD:

```php
// Versión optimizada — un solo UPDATE en SQL
Pedido::whereIn('id', $ids)->update([
    'total' => DB::raw('total * 1.10')
]);
```

Esto es crítico a escala — si hay 10,000 pedidos, la primera versión hace 10,000
queries y la segunda hace 1.

### Login con OAuth
Configuré Socialite para que el único punto de entrada al sistema sea GitHub,
sin formulario de registro manual. Las rutas del dashboard están protegidas con
el middleware `auth` para que nadie pueda acceder sin autenticarse.

### N+1 en el Dashboard
Usé `with(['cliente', 'productos'])` en el DashboardController para cargar
las relaciones en una sola consulta, evitando el problema N+1.

### Local Scopes
Encapsulé los 4 filtros del dashboard como Local Scopes en el modelo `Pedido`,
siguiendo las notas técnicas del proyecto. Esto permite llamarlos así:

```php
Pedido::porEnviar()->with(['cliente', 'productos'])->paginate(15);
Pedido::retrasados()->with(['cliente', 'productos'])->paginate(15);
```

En lugar de escribir el filtro completo cada vez en el controlador.