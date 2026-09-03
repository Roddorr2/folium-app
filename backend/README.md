# 🐘 Folium Backend API & Domain Layer

Backend REST API y Motor de Dominio para el Sistema de Gestión Bibliotecaria **Folium**, desarrollado con **PHP 8.x** y **Laravel 11** bajo arquitectura **Clean / Domain-Driven Design (DDD)**.

---

## 🏛️ Estructura de Capas (Clean DDD Architecture)

```
app/
├── Domain/                      # Core de Dominio 100% Agnóstico
│   ├── Contracts/               # Interfaces (Searcher, Notifier, TransferStrategy, Repositories)
│   ├── Services/                # Lógica pura (CirculationService, TransferService, RecommendationService)
│   └── Strategies/              # Estrategias concretas (SameBranchTransfer, InterBranchTransfer)
├── Infrastructure/              # Adaptadores Tecnológicos
│   ├── Search/                  # MeilisearchSearcher, DatabaseSearcher
│   └── Notifications/           # WebSocketNotifier, EmailNotifier
└── Http/                        # Controladores HTTP REST & Middleware
```

---

## 🛠️ Requisitos e Instalación

- PHP ≥ 8.2 con extensiones `mbstring`, `pdo_mysql`, `redis`
- Composer ≥ 2.x
- MySQL 8.0
- Redis

### Pasos de Configuración:

1. **Instalar dependencias:**
   ```bash
   composer install
   ```

2. **Configurar archivo de entorno:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Ejecutar migraciones y seeders:**
   ```bash
   php artisan migrate --seed
   ```

4. **Iniciar servidor local de desarrollo:**
   ```bash
   php artisan serve
   ```

---

## 🧪 Pruebas Automatizadas

```bash
php artisan test
```
