# 🍃 Folium — Sistema Web de Gestión Bibliotecaria (Red Multi-Sede)

[![Clean Architecture](https://img.shields.io/badge/Architecture-Clean%20%2F%20Hexagonal-emerald.svg)](docs/architecture.md)
[![IFLA LRM](https://img.shields.io/badge/Model-IFLA%20LRM%20%2F%20FRBR-blue.svg)](docs/functional-requirements.md)
[![Vue 3](https://img.shields.io/badge/Frontend-Vue.js%203%20%2B%20Tailwind-4fc08d.svg)](frontend/)
[![Laravel 11](https://img.shields.io/badge/Backend-Laravel%2011%20(DDD)-ff2d20.svg)](backend/)

## 📖 Core del Negocio

**Folium** es un Sistema Integrado de Gestión Bibliotecaria (SIGB) para redes multi-sede. Administra colecciones físicas y digitales mediante el modelo bibliográfico **IFLA LRM / FRBR (WEMI)**, optimiza la circulación interbibliotecaria y ofrece un catálogo público (OPAC) inteligente con recomendaciones y búsquedas de alta velocidad.

---

## 🏛️ Principios Arquitectónicos

Este proyecto aplica rigurosamente **Clean Architecture / Arquitectura Hexagonal**:

1. **Registros planos:** si una biblioteca tiene *El Señor de los Anillos* en tapa dura, bolsillo, inglés y audiolibro, el sistema crea 4 registros aislados → duplicidad de metadatos y mala experiencia de búsqueda.
2. **Aislamiento entre sedes:** cada sucursal opera como una isla. Un lector en la Sede Norte no puede saber (ni reservar) un ejemplar disponible en la Sede Sur, y no existe un mecanismo seguro de préstamo interbibliotecario.

## 💡 La Solución Propuesta

### Núcleo bibliográfico: IFLA LRM / FRBR (WEMI)

| Nivel | Nombre | Descripción | Ejemplo |
| --- | --- | --- | --- |
| **W** | Work (Obra) | Creación intelectual abstracta | *El Quijote* |
| **E** | Expression (Expresión) | Realización de la obra | *Traducción al inglés de 1950* |
| **M** | Manifestation (Manifestación) | Formato físico/digital | *Edición de bolsillo Penguin 2001, ISBN X* |
| **I** | Item (Ejemplar) | Objeto físico real asociado a una Sede | *Código IT-1002, Sede Norte, Estante A-3* |

### 🧩 Reto adicional: Red Multi-Sede + Recomendaciones + Búsqueda Avanzada

Para elevar la complejidad del sistema a un escenario realista de una red bibliotecaria, se agregan cuatro capacidades:

1. **Multi-sede (`branches`):** cada `Item` pertenece a una sede física. El catálogo es único y federado; la disponibilidad se muestra desglosada por sede.
2. **Transferencia interbibliotecaria:** un `Item` puede viajar de una sede a otra mediante un flujo de estados (`available → in_transit → available`), habilitando préstamos interbibliotecarios (ILL).
3. **Motor de recomendaciones:** basado en historial de préstamos (filtrado colaborativo simple + reglas por materia/autor), sugiere obras al lector en el OPAC.
4. **Búsqueda avanzada y notificaciones en tiempo real:** se reemplaza el `LIKE` de MySQL por **Meilisearch** (tolerante a errores tipográficos, con facetas) y se notifica al lector vía **WebSockets (Laravel Reverb)** cuando un ítem reservado queda disponible.

## 🛠️ Stack Tecnológico

- **Backend:** PHP 8.x + Laravel 11 (Clean DDD Architecture) + Sanctum + Laravel Reverb (WebSockets) + Redis
- **Buscador:** Meilisearch (vía `SearcherInterface` / `MeilisearchSearcher`)
- **Frontend:** Vue.js 3 (Composition API) + Pinia + Vue Router + Tailwind CSS (Vite)
- **Base de Datos:** MySQL 8.0

---

## 📂 Estructura del Repositorio

```
folium-app/
├── docs/                        # Documentación central del sistema
│   ├── README.md                # Índice de documentación
│   ├── architecture.md          # Arquitectura Clean/Hexagonal & ERD
│   ├── functional-requirements.md # Requisitos Funcionales
│   ├── non-functional-requirements.md # Requisitos No Funcionales
│   └── user-stories.md          # Historias de Usuario en Gherkin
├── backend/                     # Backend API & Domain Core
│   ├── app/Domain/              # Contratos, Modelos y Servicios puros de Dominio
│   ├── app/Infrastructure/      # Adaptadores concretos (Meilisearch, WebSockets, DB)
│   └── app/Http/                # API Controllers & Middlewares
└── frontend/                    # Single Page Application (Vue 3 + Vite + Tailwind)
```

---

## 🚀 Guía de Instalación y Ejecución Local

### 1. Backend Setup

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

### 2. Frontend Setup

```bash
cd frontend
npm install
npm run dev
```
