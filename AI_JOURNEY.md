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
- Estimar el tiempo de desarrollo
- Definir todos los requisitos de instalación en orden
- Establecer puntos de avance diarios para llegar a la fecha de entrega
- Analizar los requerimientos del proyecto para entender qué había que construir antes de empezar

Esto me permitió entender a detalle los requerimientos para poder cumplirlos adecuadamente

### Migraciones y modelos
Le pedí que generara las 4 migraciones con sus relaciones correctas, validando
la tabla pivote `pedido_producto` para la relación muchos a muchos entre pedidos y productos.

### Dashboard
Le pedí que implementara el controlador y las vistas Blade con Tailwind, con las
4 categorías y paginación de base de datos.

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

### Login con OAuth
Configuré Socialite para que el único punto de entrada al sistema sea GitHub y Google,
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


### Timezone incorrecto en el scope "Por Enviar"
El scope mostraba fechas incorrectas porque Laravel corría en UTC mientras el servidor
está en Ciudad de México. El rango de "próximos 3 días" se corría un día completo.
Se resolvió configurando el timezone en `config/app.php`:
`'timezone' => 'America/Mexico_City'`

### Redirección de rutas protegidas
Al validar que usuarios sin sesión no pudieran acceder al dashboard, descubrimos que
Laravel busca por defecto una ruta llamada `login` para redirigir. Como no usamos
el sistema de autenticación tradicional sino OAuth, se resolvió nombrando la ruta
principal como `login` en `routes/web.php`.

### Login con Google — plus adicional
Como mejora adicional al requerimiento de OAuth, se implementó login con Google
además de GitHub. Al tener Socialite ya instalado y configurado, el proceso fue
directo — misma arquitectura, diferente driver.

### Seeder con emails duplicados
Durante la validación del proyecto clonando desde cero, el seeder fallaba con un error
de constraint único en el email de clientes. El problema era que el `PedidoFactory`
creaba un cliente nuevo por cada pedido, y Faker eventualmente generaba emails repetidos
a escala de 1,000 registros.

Se refactorizó el `DatabaseSeeder` para crear primero 100 clientes y luego asignarlos
aleatoriamente a los pedidos usando `$clientes->random()->id`, eliminando completamente
la posibilidad de duplicados.

### Docker fuera del directorio home
Al intentar clonar el repositorio directamente en `~` y correr los comandos de Sail,
Docker lanzaba un error de namespace: "current working directory is outside of container
mount namespace root". El contenedor no podía acceder al directorio.

Se identificó que Docker requiere que el proyecto esté en una subcarpeta, no directamente
en el home. Se actualizó el README para indicar que se clone en `~/Projects` o cualquier
subdirectorio.

### Validación del orden de instalación
Al simular el proceso de instalación como lo haría el evaluador, se detectó que el README
tenía `key:generate` antes de `sail up -d`, lo cual falla porque Sail no está corriendo.
Se corrigió el orden y se agregó una nota para esperar 20 segundos antes de correr
`migrate --seed`, ya que MySQL necesita tiempo para inicializarse completamente.

### Segunda refactorización del Seeder — loop directo
Durante una segunda ronda de validación clonando desde cero, el seeder seguía fallando
con emails duplicados de forma intermitente. Se identificó que usar `factory()->make()`
dentro del loop no garantizaba unicidad completa.

Se refactorizó a un loop directo con `Pedido::create()` asignando los campos manualmente
y tomando clientes existentes con `$clientes->random()`. Esto elimina completamente
cualquier dependencia del factory dentro del loop y garantiza que nunca se crean
clientes nuevos durante la creación de pedidos.

### migrate:fresh --seed en lugar de migrate --seed
Durante la validación se detectó que si el evaluador corría `migrate --seed` dos veces,
los datos se duplicaban (2,000 pedidos, 200 clientes, 40 productos). Se cambió el comando
a `migrate:fresh --seed` que elimina y recrea todas las tablas antes de sembrar,
garantizando siempre datos limpios sin importar cuántas veces se ejecute.

### Sail artisan se cuelga en ciertas rutas
Se detectó que `./vendor/bin/sail artisan key:generate` se congela indefinidamente
cuando el proyecto está en ciertas rutas del sistema. Se agregó una alternativa en
el README usando `docker exec` directamente sobre el contenedor, que funciona
independientemente de la ruta:
`docker exec $(docker ps --filter "name=laravel.test" --format "{{.Names}}") php artisan key:generate`