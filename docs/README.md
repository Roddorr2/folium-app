# 📚 Documentación del Sistema — Folium

Bienvenido al centro de documentación de **Folium**, Sistema Integrado de Gestión Bibliotecaria Red Multi-Sede basado en Clean Architecture y el Modelo IFLA LRM / FRBR (WEMI).

---

## 🗺️ Índice de Especificaciones y Requisitos

| Documento | Descripción |
| --- | --- |
| 🏗️ [Arquitectura del Sistema](file:///d:/folium-app/docs/architecture.md) | Patrón Clean/Hexagonal, diagrama ER, contratos de dominio, patrones de diseño y diagramas de secuencia. |
| 📋 [Requisitos Funcionales](file:///d:/folium-app/docs/functional-requirements.md) | Catálogo WEMI, Circulación, Autenticación, Búsqueda, Red Multi-Sede y Recomendaciones. |
| ⚡ [Requisitos No Funcionales](file:///d:/folium-app/docs/non-functional-requirements.md) | Métricas de latencia, seguridad, desacoplamiento de dominio, throughput y usabilidad. |
| 📖 [Historias de Usuario](file:///d:/folium-app/docs/user-stories.md) | Historias redactadas en sintaxis Gherkin (Given-When-Then) organizadas en 5 épicas. |

---

## 🏛️ Resumen de la Arquitectura de Dominio

```
backend/app/
├── Domain/                      <-- Core agnóstico del framework
│   ├── Contracts/               <-- Interfaces puras (Searcher, Notifier, TransferStrategy, Repositories)
│   ├── Models/                  <-- Entidades de dominio y Value Objects
│   ├── Services/                <-- Lógica de negocio (Circulation, Transfer, Recommendation)
│   └── Strategies/              <-- Estrategias concretas de trasferencia
├── Infrastructure/              <-- Adaptadores de tecnologías externas
│   ├── Search/                  <-- MeilisearchSearcher, DatabaseSearcher
│   ├── Notifications/           <-- WebSocketNotifier, EmailNotifier
│   └── Persistence/             <-- Eloquent Repositories
└── Http/                        <-- Controladores REST (puertos de entrada HTTP)
```
