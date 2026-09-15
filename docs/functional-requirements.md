# Requisitos Funcionales — Folium

## 1. Módulo de Catalogación (WEMI)

| ID | Requisito | Criterio de Aceptación | Contrato Relacionado | Prioridad |
| --- | --- | --- | --- | --- |
| RF1.1 | CRUD de entidades jerárquicas Work / Expression / Manifestation / Item | Cada entidad expone endpoints REST protegidos por rol | `WorkRepositoryInterface` | Alta |
| RF1.2 | Relación N:M entre Works y Authors / Subjects | Un Work admite ≥1 autor y ≥1 materia persistidos en pivote | `WorkRepositoryInterface` | Alta |
| RF1.3 | Generación automática de código de barras por Item | Código único, no reutilizable, validado a nivel de BD | Dominio Core | Alta |
| RF1.4 | Asignación obligatoria a una Sede (`branch_id`) | No se permite crear un Item sin sede física asociada | Dominio Core | Alta |

---

## 2. Módulo de Circulación (Préstamos y Devoluciones)

| ID | Requisito | Criterio de Aceptación | Contrato Relacionado | Prioridad |
| --- | --- | --- | --- | --- |
| RF2.1 | Registro de Préstamo (`Loan`) | Valida disponibilidad e inmutabilidad del ejemplar | `CirculationRepositoryInterface` | Alta |
| RF2.2 | Validación de elegibilidad del usuario | Rechaza con error de dominio si el usuario está suspendido | `CirculationService` | Alta |
| RF2.3 | Renovación de préstamo condicional | Renueva solo si no existen reservas en cola para la obra | `CirculationService` | Media |
| RF2.4 | Asignación automática de reserva (FIFO) | Listener procesa la reserva más antigua tras devolución | `NotifierInterface` | Alta |
| RF2.5 | Devolución Intersede Cruzada y Repatriación Automática | Si `current_branch_id != home_branch_id` al devolver, el préstamo se cierra (`returned_at`), pero el ítem transiciona automáticamente a `in_transit` con orden de repatriación hacia su sede de origen (*home branch*), sin quedar `available` en la sede receptora. | `CirculationService` / `TransferStrategyInterface` | Alta |

---

## 3. Módulo de Identidad, Permisos y Autorización

| ID | Requisito | Criterio de Aceptación | Contrato Relacionado | Prioridad |
| --- | --- | --- | --- | --- |
| RF3.1 | Autenticación basada en Tokens | Tokens emitidos Sanctum/JWT revocables | Infrastructure / Security | Alta |
| RF3.2 | Roles y Permisos Granulares | Roles: Admin, Catalogador, Bibliotecario, Lector, Bibliotecario Red | `RoleRepositoryInterface` | Alta |
| RF3.3 | Modelo Relacional de Usuarios y Roles | Tablas `roles` y `users` ligadas por `role_id` y `branch_id` | Database / Core | Alta |

---

## 4. OPAC y Motor de Búsqueda Desacoplado

| ID | Requisito | Criterio de Aceptación | Contrato Relacionado | Prioridad |
| --- | --- | --- | --- | --- |
| RF4.1 | Búsqueda full-text y tolerante a erratas | Búsqueda abstraída vía contrato con soporte Meilisearch / DB | `SearcherInterface` | Alta |
| RF4.2 | Filtros facetados por idioma, sede y formato | Facetas calculadas asincrónicamente | `SearcherInterface` | Media |
| RF4.3 | Reindexación transparente | Actualizaciones en segundo plano sin bloquear peticiones HTTP | `SearcherInterface` | Media |

---

## 5. Red Multi-Sede y Transferencias Interbibliotecarias

| ID | Requisito | Criterio de Aceptación | Contrato Relacionado | Prioridad |
| --- | --- | --- | --- | --- |
| RF5.1 | Disponibilidad desglosada por sede | API responde con inventario agrupado por `branch_id` | `WorkRepositoryInterface` | Alta |
| RF5.2 | Transferencia Interbibliotecaria | Asigna estrategia `InterBranchTransfer` y cambia estado a `in_transit` | `TransferStrategyInterface` | Alta |
| RF5.3 | Recepción de transferencia en sede destino | Confirma arribo, actualiza sede y estante a `available` | `TransferService` | Alta |
| RF5.4 | Auditoría de movimientos | Registro inmutable de origen, destino y tiempos de tránsito | `TransferRepositoryInterface` | Media |
| RF5.5 | REST API CRUD de Sedes (`/api/v1/branches`) | Soporta operaciones GET, POST, PUT, DELETE para gestión de red | `BranchRepositoryInterface` | Alta |
| RF5.6 | Resolución de Incidencias y Extravíos en Tránsito (ILL Failure Handling) | Si un traslado excede el plazo logístico o se declara extravío físico, el Bibliotecario de Red marca el ítem como `lost`. El sistema actualiza inmutablemente la bitácora `TRANSFER_REQUESTS` a `failed`, desengancha las reservas asociadas notificando al usuario afectado y reasignando la cola FIFO al siguiente ejemplar disponible. | `TransferService` / `NotifierInterface` | Alta |

---

## 6. Recomendaciones y Notificaciones Asíncronas

| ID | Requisito | Criterio de Aceptación | Contrato Relacionado | Prioridad |
| --- | --- | --- | --- | --- |
| RF6.1 | Sugerencias personalizadas | `RecommendationService` propone obras sin acoplarse al frontend | `RecommendationService` | Media |
| RF6.2 | Push en tiempo real por WebSockets | Notifica disponibilidad de reservas vía canal privado | `NotifierInterface` | Alta |
| RF6.3 | Correo electrónico de respaldo | Notificador secundario por Mailable en cola | `NotifierInterface` | Media |

---

## 7. Experiencia de Usuario, Validación y Sistema de Diseño

| ID | Requisito | Criterio de Aceptación | Contrato Relacionado | Prioridad |
| --- | --- | --- | --- | --- |
| RF7.1 | Validación Reactiva de Formularios | `<form novalidate>`, estado `reactive` touched y `computed` error feedback | UI / Vue 3 | Alta |
| RF7.2 | Terminología Editorial Humana | Eliminación de jerga de BD (WEMI/FRBR) en favor de términos de biblioteca | UX / Editorial | Alta |
| RF7.3 | Sistema de Tokens Design System | Paleta `folium.*` en Tailwind CSS con acento Terracota Cuero (`#9E4E36`) | Design System | Alta |
| RF7.4 | Tratamiento Editorial de Disponibilidad Nula Consolidada | Cuando la disponibilidad consolidada en red sea 0, el backend y componentes de UI suprimen las grillas de sedes con ceros planos (`[Sede: 0]`) y presentan un estado vacío unificado ("Sin ejemplares físicos disponibles actualmente en red") con opciones de reserva anticipada o suscripción a alertas de disponibilidad. | `WorkRepositoryInterface` / OPAC UI | Alta |
