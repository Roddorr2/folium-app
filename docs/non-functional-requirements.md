# Requisitos No Funcionales — Folium

## 1. Rendimiento y Concurrencia

| ID | Categoría | Métrica / Objetivo | Descripción |
| --- | --- | --- | --- |
| RNF1.1 | Latencia de búsqueda | < 300 ms (p95) | Abstraída tras `SearcherInterface` ejecutada en Meilisearch |
| RNF1.2 | Latencia de catálogo relacional | < 1.5 s | Consultas WEMI optimizadas vía Repositorios con Eager Loading |
| RNF1.3 | Concurrencia de usuarios | ≥ 500 simultáneos | Caché de objetos de dominio e invalidación granular |
| RNF1.4 | Throughput de notificaciones | ≥ 100 eventos/seg | Procesamiento no bloqueante usando `NotifierInterface` asíncrono |

---

## 2. Arquitectura, Desacoplamiento y Seguridad

| ID | Categoría | Métrica / Objetivo | Descripción |
| --- | --- | --- | --- |
| RNF2.1 | Technology-Agnostic Core | 100% independencia de framework | Dominio puro PHP sin importaciones directas de Eloquent o Laravel |
| RNF2.2 | Inversión de Dependencias | 0 instanciaciones directas | Todo servicio inyecta sus dependencias mediante interfaces en constructor |
| RNF2.3 | Protección de Datos & Auth | 0 vulnerabilidades de inyección | Controladores filtran mediante Form Requests y Sanctum middleware |
| RNF2.4 | Aislamiento entre sedes | Aislamiento estricto por rol | Operaciones restringidas por la sede asociada al token del bibliotecario |

---

## 3. Escalabilidad e Integración

| ID | Categoría | Métrica / Objetivo | Descripción |
| --- | --- | --- | --- |
| RNF3.1 | Asincronía desacoplada | Event-driven notifications | Los eventos de dominio disparan Listeners sin bloquear las respuestas HTTP |
| RNF3.2 | Arquitectura API-First | JSON REST + WebSockets | Formato estandarizado utilizable por la SPA Vue.js o una futura App Móvil |
| RNF3.3 | Tolerancia a fallos en transferencias | Transacciones atómicas | Toda transferencia finaliza o revierte atómicamente el estado del ejemplar |

---

## 4. Usabilidad y Disponibilidad

| ID | Categoría | Métrica / Objetivo | Descripción |
| --- | --- | --- | --- |
| RNF4.1 | UI Responsiva & Móvil | 100% Usable desde 360px | Frontend Vue 3 + Tailwind CSS adaptable a cualquier pantalla |
| RNF4.2 | Disponibilidad de Servicio | ≥ 99.5% uptime mensual | Servicios desacoplados listos para despliegue en contenedores Docker |
| RNF4.3 | Degradación controlada | Fallback de adaptadores | Si el adaptador de Meilisearch no responde, se conmuta al `DatabaseSearcher` |
