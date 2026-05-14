Perfecto. Ahora vamos a escribir el **AI_JOURNEY.md**. 

Abre el archivo `AI_JOURNEY.md` en VS Code y pega esto como base — luego lo personalizamos con tus propias palabras:

```markdown
# AI_JOURNEY.md

## Mi experiencia como Tech Lead de la IA

### Contexto
Este proyecto fue desarrollado con apoyo de Claude (Anthropic) como asistente de IA.
Es mi primer proyecto en Laravel y Docker, por lo que usé la IA como guía técnica
mientras tomaba decisiones sobre la arquitectura y calidad del código.

---

## 1. Prompts Clave

### Setup inicial
Le pedí a la IA que me guiara paso a paso para instalar Laravel con Docker usando Sail,
sin experiencia previa en ninguna de las dos tecnologías.

### Migraciones y modelos
Le pedí que generara las 4 migraciones con sus relaciones correctas, especialmente
la tabla pivote `pedido_producto` para la relación muchos a muchos.

### Dashboard
Le pedí que creara el controlador y las vistas Blade con Tailwind, con las 4 categorías
de pedidos y paginación real de base de datos.

### Comando Artisan
Le pedí que implementara el motor de cargos exprés aplicando las 3 condiciones
exactas del negocio usando Eloquent.

---

## 2. Correcciones y Decisiones Técnicas

### Problema de DNS en Docker
La IA inicialmente intentó resolver el problema de DNS modificando el Dockerfile,
pero el problema real eran reglas de red residuales de ZeroTier. La solución
fue reiniciar Ubuntu para limpiar las reglas.

### Seeder — evitando el problema de rendimiento
La IA inicialmente sugirió crear los pedidos con un loop usando `save()` individual.
Lo refactoricé para usar `factory(1000)->create()` con `each()` para asignar
productos, reduciendo la complejidad del código.

### N+1 en el Dashboard
En el DashboardController usé `with(['cliente', 'productos'])` para cargar
las relaciones en una sola consulta, evitando el problema N+1 que ocurriría
si cargáramos cliente y productos por separado en la vista.

### Local Scopes
En lugar de escribir los filtros directamente en el controlador, los encapsulé
en el modelo `Pedido` como Local Scopes, siguiendo las notas técnicas del proyecto.

### Comando Artisan — filtro eficiente
Para el comando de cargos exprés, usé `whereHas` para verificar la existencia
del producto id=5 directamente en SQL, sin traer todos los productos a memoria.
```

Guarda y avísame. Luego lo revisamos y ajustamos con tus propias palabras.