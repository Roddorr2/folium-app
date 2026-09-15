# Arquitectura del Sistema — Folium

## 1. Patrón Arquitectónico Global (Clean Architecture / Hexagonal)

Folium utiliza una arquitectura **Clean / Hexagonal (Puertos y Adaptadores)** desacoplada. La lógica del negocio (Dominio) es completamente independiente de frameworks, bases de datos y bibliotecas externas.

```mermaid
graph TD
    subgraph Frontend
        UI[Vue.js 3 SPA<br/>Pinia + Vue Router]
    end

    subgraph Infrastructure Layer
        Controller[REST Controllers<br/>Sanctum Auth]
        MeiliAdapter[MeilisearchSearcher]
        DBAdapter[Eloquent Repositories]
        WSAdapter[WebSocketNotifier]
        MailAdapter[EmailNotifier]
    end

    subgraph Domain Layer Contracts & Core
        SearcherContract[SearcherInterface]
        NotifierContract[NotifierInterface]
        CirculationRepo[CirculationRepositoryInterface]
        TransferStrategy[TransferStrategyInterface]

        CirculationSvc[CirculationService]
        TransferSvc[TransferService]
        RecommendSvc[RecommendationService]
    end

    UI -- REST JSON --> Controller
    Controller --> CirculationSvc
    Controller --> TransferSvc
    Controller --> RecommendSvc

    CirculationSvc --> CirculationRepo
    TransferSvc --> TransferStrategy
    RecommendSvc --> SearcherContract

    DBAdapter ..|> CirculationRepo
    MeiliAdapter ..|> SearcherContract
    WSAdapter ..|> NotifierContract
    MailAdapter ..|> NotifierContract
```

---

## 2. Abstrucción de Contratos (Domain Interfaces)

Para garantizar la extensibilidad y cumplir con el principio de inversión de dependencias:

| Contrato (Interface) | Implementaciones (Adapters) | Propósito |
| --- | --- | --- |
| `SearcherInterface` | `MeilisearchSearcher`, `DatabaseSearcher` | Motor de búsqueda full-text y facetado |
| `NotifierInterface` | `WebSocketNotifier`, `EmailNotifier`, `SMSNotifier` | Notificación asíncrona y en tiempo real |
| `TransferStrategyInterface` | `SameBranchTransfer`, `InterBranchTransfer` | Reglas de negocio para movimiento de ítems |
| `CirculationRepositoryInterface` | `EloquentCirculationRepository` | Abstracción de persistencia de préstamos |
| `WorkRepositoryInterface` | `EloquentWorkRepository` | Abstracción de persistencia de catálogo WEMI |
| `BranchRepositoryInterface` | `EloquentBranchRepository` | Gestión CRUD de sedes bibliotecarias de la red |
| `RoleRepositoryInterface` | `EloquentRoleRepository` | Gestión de roles y permisos de personal |
| `LanguageRepositoryInterface` | `EloquentLanguageRepository` | Gestión de idiomas ISO 639-1 del catálogo |

---

## 3. Modelo de Datos Relacional (MySQL) — Diagrama Entidad-Relación (ERD)

```mermaid
erDiagram
    ROLES ||--o{ USERS : "asigna rol a"
    BRANCHES ||--o{ USERS : "asigna sede a"
    BRANCHES ||--o{ ITEMS : "aloja"
    LANGUAGES ||--o{ EXPRESSIONS : "traduce a"
    WORKS ||--o{ EXPRESSIONS : "compone"
    EXPRESSIONS ||--o{ MANIFESTATIONS : "publica en"
    MANIFESTATIONS ||--o{ ITEMS : "materializa en"
    WORKS ||--o{ AUTHOR_WORK : "posee"
    AUTHORS ||--o{ AUTHOR_WORK : "es autor de"
    WORKS ||--o{ SUBJECT_WORK : "pertenece a"
    SUBJECTS ||--o{ SUBJECT_WORK : "clasifica a"
    USERS ||--o{ LOANS : "solicita"
    ITEMS ||--o{ LOANS : "es prestado en"
    USERS ||--o{ RESERVATIONS : "reserva"
    WORKS ||--o{ RESERVATIONS : "es reservada"
    ITEMS ||--o{ TRANSFER_REQUESTS : "es transferido"
    BRANCHES ||--o{ TRANSFER_REQUESTS : "origen/destino"

    ROLES {
        bigint id PK
        string name UK
        string display_name
        text description
        timestamp created_at
    }

    USERS {
        bigint id PK
        string name
        string email UK
        string password
        bigint role_id FK
        bigint branch_id FK
        string dni
        timestamp created_at
    }

    LANGUAGES {
        bigint id PK
        string code UK
        string name
        string native_name
        string script
        boolean is_active
    }

    BRANCHES {
        bigint id PK
        string name
        string city
        string address
        string phone
        timestamp created_at
    }

    WORKS {
        bigint id PK
        string title
        text abstract
        string original_language
        timestamp created_at
    }

    AUTHORS {
        bigint id PK
        string name
        timestamp created_at
    }

    SUBJECTS {
        bigint id PK
        string name
        timestamp created_at
    }

    AUTHOR_WORK {
        bigint work_id PK_FK
        bigint author_id PK_FK
    }

    SUBJECT_WORK {
        bigint work_id PK_FK
        bigint subject_id PK_FK
    }

    EXPRESSIONS {
        bigint id PK
        bigint work_id FK
        bigint language_id FK
        date translation_date
        timestamp created_at
    }

    MANIFESTATIONS {
        bigint id PK
        bigint expression_id FK
        string isbn
        string publisher
        int publication_year
        string format
        timestamp created_at
    }

    ITEMS {
        bigint id PK
        bigint manifestation_id FK
        bigint branch_id FK
        string barcode UK
        string shelf_location
        enum status
        timestamp created_at
    }

    LOANS {
        bigint id PK
        bigint item_id FK
        bigint user_id FK
        date due_date
        timestamp returned_at
        timestamp created_at
    }

    RESERVATIONS {
        bigint id PK
        bigint work_id FK
        bigint user_id FK
        int queue_position
        timestamp created_at
    }

    TRANSFER_REQUESTS {
        bigint id PK
        bigint item_id FK
        bigint origin_branch_id FK
        bigint destination_branch_id FK
        enum status
        timestamp created_at
    }
```

---

## 4. Patrones de Diseño Aplicados

1. **Repository Pattern:** abstrae consultas de persistencia fuera de los controladores y servicios.
2. **Strategy Pattern:** encapsula variaciones en transferencia de ejemplares (`SameBranchTransfer` vs `InterBranchTransfer`).
3. **Service Pattern:** servicios puros (`CirculationService`, `TransferService`, `RecommendationService`) enfocados con responsabilidad única.
4. **Observer / Event-Driven Pattern:** notificaciones desacopladas en Listeners reaccionando a eventos del sistema.
5. **Dependency Injection:** todas las dependencias son inyectadas como interfaces mediante constructores.

---

## 5. Flujos de Secuencia

### Flujo de Préstamo

```mermaid
sequenceDiagram
    actor Bib as Bibliotecario
    participant Controller as LoansController
    participant Svc as CirculationService
    participant Repo as CirculationRepositoryInterface

    Bib->>Controller: POST /api/loans {barcode, user_id}
    Controller->>Svc: createLoan(barcode, userId)
    Svc->>Repo: findAvailableItemByBarcode(barcode)
    Svc->>Repo: saveLoan(loan)
    Svc-->>Controller: Loan Result
    Controller-->>Bib: 201 Created Response
```

### Flujo de Transferencia Interbibliotecaria

```mermaid
sequenceDiagram
    actor Lector
    participant Controller as TransferController
    participant TSvc as TransferService
    participant Strategy as TransferStrategyInterface
    participant Event as ItemStatusChangedEvent

    Lector->>Controller: POST /api/transfers {item_id, destination_branch}
    Controller->>TSvc: initiateTransfer(itemId, targetBranchId)
    TSvc->>Strategy: execute(item, origin, destination)
    TSvc->>Event: dispatch(ItemInTransitEvent)
    Controller-->>Lector: 202 Accepted
```

---

## 6. Ciclo de Vida del Ejemplar (State Machine)

```mermaid
stateDiagram-v2
    [*] --> available: Alta en inventario
    available --> loaned: Préstamo registrado
    available --> reserved: Asignado a reserva FIFO
    available --> in_transit: Solicitud de transferencia
    loaned --> available: Devolución sin reservas en cola
    loaned --> reserved: Devolución con reserva en cola
    reserved --> loaned: Lector retira el ejemplar
    in_transit --> available: Recepción confirmada en sede destino
    available --> lost: Reportado extraviado
    loaned --> lost: No devuelto tras plazo extendido
    lost --> [*]
```

---

## 7. Sistema de Diseño & Design Tokens (Identidad Editorial Folium)

La interfaz frontend (Vue 3 + Tailwind CSS) implementa una paleta cerrada denominada **Folium Brand Identity** registrada bajo el espacio de nombres `folium.*` en `tailwind.config.js`:

| Categoría Token | Nombre Token | Hexadecimal | Uso y Propósito en Interfaz |
| --- | --- | --- | --- |
| **Fondo / Pergamino** | `folium-canvas` | `#F9F6F0` | Fondo general de la aplicación |
| **Pergamino Tostado** | `folium-parchment` | `#F3EFE6` | Lienzo de tarjetas (`paper-card`), contenedores |
| **Marfil Cálido** | `folium-ivory` | `#FDFBF7` | Contenedores de búsqueda e inputs destacados |
| **Tinta Bosque** | `folium-ink` | `#152219` | Titulares principales, números de métricas |
| **Musgo Cenizo** | `folium-moss` | `#3C4A40` | Texto secundario y párrafos explicativos |
| **Salvia Seco** | `folium-sage` | `#66756A` | Micro-atribuciones y subtítulos |
| **Verde Laurel (Primario)**| `folium-forest` | `#2D5A3F` | Botones de acción primarios, foco, badges de stock |
| **Terracota / Cuero** | `folium-terracotta` | `#9E4E36` | Acento cálido principal, botones secundarios |
| **Terracota Profundo** | `folium-terracotta-deep`| `#7D3B28` | Texto de badges de materias y clasificaciones |
| **Terracota Suave** | `folium-terracotta-subtle`| `#F2E4DE` | Fondos de chips de género y clasificaciones Dewey |
| **Ámbar Cobre** | `folium-amber` | `#B87333` | Indicadores de traslados intersede (ILL) |
| **Granate / Carmesí** | `folium-crimson` | `#8C433E` | Mensajes de error inline y sin stock |

### Arquitectura de Validación Reactiva de Formularios

- **Sin validaciones nativas de navegador:** Todos los formularios especifican `<form novalidate @submit.prevent="...">`.
- **Estado Reactivo de Campo (`touched` state):** Monitoreado por objeto `reactive` en eventos `@blur` y `@input`.
- **Compilaciones Compuestas (`computed` validation):** Evaluación en tiempo real de `errors` y deshabilitación dinámica del botón de envío si el formulario es inválido.
- **Feedback Inline Editorial:** Bordes y textos de alerta en tono granate (`#8C433E`) o terracota (`#9E4E36`) integrados en el lienzo pergamino.
