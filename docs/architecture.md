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

---

## 3. Modelo de Datos Relacional (MySQL) — Diagrama Entidad-Relación

```mermaid
erDiagram
    WORKS ||--o{ EXPRESSIONS : "tiene"
    EXPRESSIONS ||--o{ MANIFESTATIONS : "tiene"
    MANIFESTATIONS ||--o{ ITEMS : "tiene"
    WORKS }o--o{ AUTHORS : "author_work"
    WORKS }o--o{ SUBJECTS : "work_subject"
    BRANCHES ||--o{ ITEMS : "aloja"
    USERS ||--o{ LOANS : "solicita"
    ITEMS ||--o{ LOANS : "es prestado en"
    USERS ||--o{ RESERVATIONS : "reserva"
    WORKS ||--o{ RESERVATIONS : "es reservada"
    ITEMS ||--o{ TRANSFER_REQUESTS : "es transferido"
    BRANCHES ||--o{ TRANSFER_REQUESTS : "origen/destino"

    WORKS {
        bigint id PK
        string title
        text abstract
        string original_language
    }
    EXPRESSIONS {
        bigint id PK
        bigint work_id FK
        string language
        date translation_date
    }
    MANIFESTATIONS {
        bigint id PK
        bigint expression_id FK
        string isbn
        string publisher
        int publication_year
        string format
    }
    ITEMS {
        bigint id PK
        bigint manifestation_id FK
        bigint branch_id FK
        string barcode
        string shelf_location
        enum status
    }
    BRANCHES {
        bigint id PK
        string name
        string address
    }
    LOANS {
        bigint id PK
        bigint item_id FK
        bigint user_id FK
        date due_date
        date returned_at
    }
    RESERVATIONS {
        bigint id PK
        bigint work_id FK
        bigint user_id FK
        int queue_position
    }
    TRANSFER_REQUESTS {
        bigint id PK
        bigint item_id FK
        bigint origin_branch_id FK
        bigint destination_branch_id FK
        enum status
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
