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

---

## 3. Módulo de Identidad y Autorización

| ID | Requisito | Criterio de Aceptación | Contrato Relacionado | Prioridad |
| --- | --- | --- | --- | --- |
| RF3.1 | Autenticación basada en Tokens | Tokens emitiendo Sanctum/JWT revocables | Infrastructure / Security | Alta |
| RF3.2 | Roles y Permisos Granulares | Roles: Admin, Catalogador, Bibliotecario, Lector, Bibliotecario Red | Infrastructure / Auth | Alta |

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

---

## 6. Recomendaciones y Notificaciones Asíncronas

| ID | Requisito | Criterio de Aceptación | Contrato Relacionado | Prioridad |
| --- | --- | --- | --- | --- |
| RF6.1 | Sugerencias personalizadas | `RecommendationService` propone obras sin acoplarse al frontend | `RecommendationService` | Media |
| RF6.2 | Push en tiempo real por WebSockets | Notifica disponibilidad de reservas vía canal privado | `NotifierInterface` | Alta |
| RF6.3 | Correo electrónico de respaldo | Notificador secundario por Mailable en cola | `NotifierInterface` | Media |
