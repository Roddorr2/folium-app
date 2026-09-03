# Historias de Usuario (Gherkin) — Folium

> Formato: `Feature` / `Scenario` / `Given-When-Then`

## Épica 1: Catálogo Público (OPAC) y Descubrimiento

```gherkin
Feature: Búsqueda unificada de obras
  Como Lector
  Quiero buscar un título y ver una única tarjeta de "Obra"
  Para no confundirme con ediciones repetidas

  Scenario: US1-01 - Resultado agrupado por Obra
    Given existen 4 Manifestaciones distintas de la Obra "El Señor de los Anillos"
    When el Lector busca "Señor de los Anillos" en el OPAC
    Then el sistema muestra exactamente 1 tarjeta de resultado para la Obra
    And la tarjeta indica "4 ediciones disponibles"

  Scenario: US1-02 - Exploración de ediciones e idiomas
    Given el Lector está en la página de la Obra "Cien Años de Soledad"
    When despliega la sección de ediciones
    Then ve listadas las Expresiones agrupadas por idioma
    And cada Expresión muestra sus Manifestaciones (ISBN, editorial, año, formato)

  Scenario: US1-03 - Disponibilidad de ejemplares en tiempo real
    Given una Manifestación tiene 3 Items registrados
    And 2 Items están en estado "loaned" y 1 en estado "available"
    When el Lector consulta la disponibilidad
    Then el sistema muestra "1 ejemplar disponible" y "2 prestados"
```

## Épica 2: Catalogación Avanzada (WEMI)

```gherkin
Feature: Catalogación jerárquica WEMI
  Como Catalogador
  Quiero crear y mantener la jerarquía Work-Expression-Manifestation-Item
  Para evitar duplicidad de metadatos

  Scenario: US2-01 - Creación de una nueva Obra
    Given el Catalogador ha iniciado sesión con rol "cataloger"
    When crea una Obra "Rayuela" y la vincula al autor "Julio Cortázar"
    And la vincula a las materias "Literatura Latinoamericana" y "Novela experimental"
    Then la Obra queda registrada con sus relaciones N:M persistidas

  Scenario: US2-02 - Alta masiva de ejemplares
    Given existe la Manifestación ISBN "978-84-376-0494-7"
    When el Catalogador registra un lote de compra de 10 unidades
    Then el sistema genera 10 códigos de barras únicos autoincrementales
    And crea 10 Items en estado "available" ligados a la Sede del Catalogador
```

## Épica 3: Circulación (Préstamos y Devoluciones)

```gherkin
Feature: Circulación de ejemplares
  Como Bibliotecario
  Quiero gestionar préstamos, reservas y devoluciones
  Para mantener el flujo de la colección bajo control

  Scenario: US3-01 - Préstamo rápido por escaneo
    Given el Bibliotecario escanea el carnet del Usuario "U-1023"
    And escanea el código de barras del Item "IT-4501" en estado "available"
    When confirma el préstamo
    Then el préstamo se registra en menos de 5 segundos
    And el Item cambia a estado "loaned"

  Scenario: US3-02 - Reserva con asignación automática
    Given todos los Items de la Obra "Sapiens" están en estado "loaned"
    When el Lector reserva la Obra "Sapiens"
    And uno de los Items es devuelto posteriormente
    Then el sistema asigna automáticamente ese Item a la reserva más antigua en cola
    And notifica al Lector (ver Épica 5)

  Scenario: US3-03 - Cálculo automático de suspensión
    Given un préstamo tenía como fecha límite "2026-08-20"
    And el Usuario devuelve el Item el "2026-08-25"
    When se procesa la devolución
    Then el sistema calcula 5 días de atraso
    And aplica una suspensión proporcional según la política configurada
```

## Épica 4: Red Multi-Sede y Transferencias

```gherkin
Feature: Red de bibliotecas multi-sede
  Como Bibliotecario de red
  Quiero transferir ejemplares entre sedes
  Para satisfacer demanda que una sola sucursal no puede cubrir

  Scenario: US4-01 - Disponibilidad desglosada por sede
    Given la Obra "1984" tiene Items en la Sede Norte y en la Sede Sur
    When el Lector consulta disponibilidad
    Then el sistema muestra el conteo de ejemplares disponibles por cada sede

  Scenario: US4-02 - Solicitud de transferencia interbibliotecaria
    Given el Lector está afiliado a la Sede Sur
    And el único Item disponible de "1984" está en la Sede Norte
    When el Lector solicita préstamo interbibliotecario
    Then el sistema crea una solicitud de transferencia
    And el Item cambia su estado a "in_transit"

  Scenario: US4-03 - Recepción de transferencia
    Given un Item está en estado "in_transit" hacia la Sede Sur
    When el Bibliotecario de la Sede Sur escanea el Item al recibirlo
    Then el Item cambia su ubicación (shelf_location) y branch_id a Sede Sur
    And queda "available" listo para ser prestado al solicitante
```

## Épica 5: Recomendaciones y Notificaciones en Tiempo Real

```gherkin
Feature: Recomendaciones personalizadas
  Como Lector
  Quiero recibir sugerencias relevantes
  Para descubrir obras afines a mis intereses

  Scenario: US5-01 - Recomendación por historial de préstamos
    Given el Lector ha tomado en préstamo 3 obras de "Ciencia Ficción"
    When visita su página de inicio en el OPAC
    Then el sistema sugiere obras de la misma materia que aún no ha leído
    And excluye obras ya prestadas o reservadas por él

  Scenario: US5-02 - Notificación en tiempo real de disponibilidad
    Given el Lector tiene una reserva activa sobre la Obra "Dune"
    When el sistema le asigna automáticamente un Item devuelto
    Then el Lector recibe una notificación push vía WebSocket en menos de 3 segundos
    And el correo de confirmación se encola de forma asíncrona
```
