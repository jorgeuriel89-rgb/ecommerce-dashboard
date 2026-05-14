# AI_JOURNEY.md
## Experiencia como Tech Lead de la IA

### Detalles
Este proyecto fue desarrollado con apoyo de Claude de Anthropic como asistente de IA.
Usé la IA como guía técnica y acelerador de desarrollo, para validar requerimientos y 
para resolver los problemas que surgieron en el desarrollo.

## 1. Prompts Clave

### Arranque del proyecto
Teniendo el documento de los requisitos a desarrollar, le pedí a la IA que me ayudara a:
- Estimar el tiempo de desarrollo
- Definir todos los requisitos de instalación en orden
- Establecer puntos de avance diarios para llegar a la fecha de entrega
- Analizar los requerimientos del proyecto para entender qué había que construir antes de empezar

Pude entender a detalle los requerimientos para poder cumplirlos adecuadamente

### Migraciones y modelos
Le pedí que generara las 4 migraciones con sus relaciones correctas, validando
la tabla pivote pedido_producto.

### Dashboard
Le pedí que implementara el controlador y las vistas Blade con Tailwind, con las
4 categorías y paginación de base de datos.

### Comando Artisan
Le pedí que implementara el motor de cargos exprés con las 3 condiciones mencionadas
usando Eloquent y que el filtro se hiciera directamente en SQL sin traer datos a memoria.


## 2. Correcciones y validaciones de requerimientos

### El problema de Docker sin internet
Inicialmente tuve un problema con que Docker no tenía salida a internet para instalar
los paquetes dentro del contenedor. El problema era que tenía ZeroTier 
instalado en Linux y sus reglas de red creaban un conflicto con Docker al usar
un segmento de red diferente. Los contenedores no podían resolver DNS y por lo tanto
no podían descargar nada.

Después de varios intentos para resolverlo la solución final fue reiniciar Ubuntu para limpiar las reglas de red de ZeroTier.
Vlidando despues de eso, Docker tuvo acceso a internet sin problema y pude continuar con el desarrollo.

### Comando Artisan — optimización del update
La primera versión del comando usaba un loop con save() individual por cada pedido.

Se  refactorizó a un update masivo que ejecuta una sola query en la BD

### Login con OAuth
Se configuró Socialite para que la entrada al sistema sea GitHub y Google,
sin formulario de registro. Las rutas del dashboard se protegieron con
el middleware auth para que nadie pueda acceder sin autenticarse.

### N+1 en el Dashboard
Usé with(['cliente', 'productos']) en el DashboardController para cargar
las relaciones en una sola consulta, evitando el problema N+1.

### Local Scopes
Se vaidaron los 4 filtros del dashboard como Local Scopes en el modelo Pedido,
siguiendo las notas técnicas del proyecto.


### Timezone incorrecto en el apartado "Por Enviar"
Se mostraba fechas incorrectas porque Laravel corría en UTC mientras el servidor en la zona horaria 
debería ser Ciudad de México. El rango de próximos 3 días se corría un día completo y daba un día demás.
Se resolvió configurando el timezone.

### Redirección de rutas protegidas
Al validar que usuarios sin sesión no pudieran acceder al dashboard, no se redirigía al login si no 
la vista de error.

### Login con Google
Como extra al requerimiento de OAuth, se implementó login con Google
además del primero que puse que fue el de GitHub.

### Seeder con emails duplicados
Durante la validación del proyecto clonando desde cero, el seeder fallaba con un error
de constraint en el email de clientes. El problema era que el PedidoFactory
creaba un cliente nuevo por cada pedido y Faker eventualmente generaba emails repetidos.

Se refactorizó el DatabaseSeeder, eliminando la posibilidad de duplicados.

### Docker fuera deen el directorio home
Al intentar clonar el repositorio directamente en el home y correr los comandos de Sail,
Docker lanzaba el error de namespace: "current working directory is outside of container
mount namespace root".

Se identificó que Docker requiere que el proyecto esté en una subcarpeta, no directamente
en el home. Se actualizó el README para indicar que se clone en cualquier subdirectorio.

### Validación del orden de instalación
Al simular el proceso de instalación, se detectó que el README tenía 
key:generate antes de sail up -d, lo cual falla porque Sail no está corriendo.
Se corrigió el orden y se agregó una nota para esperar 20 segundos.

### Segunda refactorización del Seeder
Durante una segunda ronda de validación clonando desde cero, el seeder seguía fallando
con emails duplicados de forma intermitente. 

Se refactorizó nuevamente asignando los campos manualmente
y tomando clientes existentes con $clientes->random().

### migrate:fresh --seed
Durante la validación se detectó que si se corría migrate --seed dos veces,
los datos se duplicaban (2,000 pedidos, 200 clientes, 40 productos). Se cambió el comando
a migrate:fresh --seed que elimina y recrea todas las tablas.

### Sail artisan se cuelga en ciertas rutas
Se detectó que ./vendor/bin/sail artisan key:generate se congela un rato en ocaciones.
Se agregó una alternativa en el README usando docker exec directamente sobre el contenedor.

Se valida requerimientos y ejecución del proyecto antes de mandarlo.