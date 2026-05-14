# 📦 E-commerce Dashboard — Motor de Procesamiento y Dashboard de Pedidos

Panel interno de logística con autenticación OAuth y procesamiento automatizado de pedidos.

## Tecnologías
- Laravel 12
- MySQL 8.4
- Docker (Laravel Sail)
- Blade + Tailwind CSS
- Laravel Socialite (OAuth GitHub y Google)

## Requisitos
- Docker Desktop o Docker Engine
- Composer

## Instalación

### 1. Clonar el repositorio
```bash
mkdir -p ~/Projects
git clone https://github.com/jorgeuriel89-rgb/ecommerce-dashboard.git ~/Projects/ecommerce-dashboard
cd ~/Projects/ecommerce-dashboard
```

### 2. Configurar el entorno
```bash
cp .env.example .env
```

Edita el `.env` y agrega tus credenciales de OAuth:

**GitHub:** Crea una OAuth App en https://github.com/settings/developers
- Homepage URL: `http://localhost:8080`
- Callback URL: `http://localhost:8080/auth/github/callback`

**Google:** Crea credenciales en https://console.cloud.google.com
- URI de redirección: `http://localhost:8080/auth/google/callback`

Luego llena estas variables en el `.env`:
```env
GITHUB_CLIENT_ID=tu_client_id
GITHUB_CLIENT_SECRET=tu_client_secret
GOOGLE_CLIENT_ID=tu_client_id
GOOGLE_CLIENT_SECRET=tu_client_secret
```

### 3. Instalar dependencias
```bash
composer install
```

### 4. Levantar los contenedores
```bash
./vendor/bin/sail up -d
```

### 5. Generar clave de aplicación
```bash
./vendor/bin/sail artisan key:generate
```

### 6. Correr migraciones y seeders
```bash
./vendor/bin/sail artisan migrate --seed
```

### 7. Compilar assets
```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

### 8. Acceder al sistema
Abre [http://localhost:8080](http://localhost:8080) e inicia sesión con GitHub o Google.

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