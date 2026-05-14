# 📦 E-commerce Dashboard — Motor de Procesamiento y Dashboard de Pedidos

Panel interno de logística con autenticación OAuth y procesamiento automatizado de pedidos.

## Tecnologías
- Laravel 12
- MySQL 8.4
- Docker (Laravel Sail)
- Blade + Tailwind CSS
- Laravel Socialite (OAuth GitHub)

## Requisitos
- Docker Desktop o Docker Engine
- Composer

## Instalación

### 1. Clonar el repositorio
```bash
git clone https://github.com/jorgeuriel89-rgb/ecommerce-dashboard.git
cd ecommerce-dashboard
```

### 2. Configurar el entorno
```bash
cp .env.example .env
```

Edita el `.env` y agrega tus credenciales de GitHub OAuth:
```env
GITHUB_CLIENT_ID=tu_client_id
GITHUB_CLIENT_SECRET=tu_client_secret
GITHUB_REDIRECT_URI=http://localhost:8080/auth/github/callback
APP_PORT=8080
FORWARD_DB_PORT=3307
DB_HOST=mysql
DB_DATABASE=pedidos_db
DB_USERNAME=sail
DB_PASSWORD=password
```

### 3. Instalar dependencias
```bash
composer install
./vendor/bin/sail up -d
```

### 4. Generar clave de aplicación
```bash
./vendor/bin/sail artisan key:generate
```

### 5. Correr migraciones y seeders
```bash
./vendor/bin/sail artisan migrate --seed
```

### 6. Compilar assets
```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

### 7. Acceder al sistema
Abre [http://localhost:8080](http://localhost:8080) e inicia sesión con GitHub.

---

## Funcionalidades

### Dashboard de Logística
Panel con 4 categorías de pedidos:
- **Por Enviar** — pendientes con entrega en los próximos 3 días
- **Retrasados** — pendientes con fecha de entrega vencida
- **Entregados** — pedidos completados
- **Cancelados** — pedidos cancelados

### Motor de Cargos Exprés
Comando Artisan que aplica un recargo del 10% a pedidos pendientes con entrega
al día siguiente que incluyan el producto de Manejo Especial (id=5).

```bash
./vendor/bin/sail artisan pedidos:cargo-expres
```

Programado para ejecutarse automáticamente a medianoche.

---

## Notas Técnicas
- **N+1 resuelto** con eager loading `with(['cliente', 'productos'])`
- **Local Scopes** en el modelo `Pedido` para filtros reutilizables
- **Update masivo** con `DB::raw` en el comando Artisan
- **whereHas** para filtrar por relaciones directamente en SQL