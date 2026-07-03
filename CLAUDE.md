# CLAUDE.md — Yofi E-commerce

Archivo de referencia para Claude Code. Leer completo antes de cualquier acción en este repositorio.

---

## IDIOMA

Siempre responder en español (Argentina). Sin excepciones.

## 1. Contexto del proyecto

Yofi es una tienda de ropa infantil construida en PHP puro + MySQL, sin frameworks. Es un proyecto en crecimiento activo con una sola desarrolladora (Gonzalo). El objetivo es una base sólida, mantenible y escalable a largo plazo.

- **Dev:** Gonzalo (único)
- **Cliente/Admin:** Ayelen (Aye)
- **Entorno local:** Laragon (Windows)
- **Producción:** DonWeb (FTP puerto 21, DB vía phpMyAdmin)
- **Repo:** github.com/gonzalofmattia/yofi (público)

---

## 2. Stack tecnológico

| Capa | Tecnología |
|---|---|
| Backend | PHP puro (sin frameworks) |
| Base de datos | MySQL |
| Admin panel | Bootstrap 5 |
| Frontend público | Tailwind CSS + Nunito |
| JavaScript | Vanilla JS (sin frameworks) |
| Pagos | Mercado Pago Checkout Pro |
| Envíos | Zipnova API v2 |
| Tests | PHPUnit |

---

## 3. Estructura de archivos clave

```
/
├── admin/              # Panel de administración (Aye)
│   └── include/        # funciones.php, includes.php, sidebar.php, config.php
├── checkout/
│   └── process.php     # ⚠️ ZONA FRÁGIL — no tocar sin tests
├── webhooks/
│   ├── mp-notification.php       # ⚠️ ZONA FRÁGIL — endpoint HTTP del webhook MP
│   └── zipnova-notification.php  # webhook de Zipnova (tracking de envíos)
├── src/php/             # Lógica de dominio del front público
│   ├── mp_sync.php       # ⚠️ ZONA FRÁGIL — lógica real del webhook MP (mp_mercadopago_sync_payment)
│   ├── order_emails.php  # Generadores de HTML de mails de pedidos
│   ├── email.php          # Motor de envío (PHPMailer + fallback nativo)
│   └── stock_reservation.php # Reservar/confirmar/liberar stock
├── config/             # Archivos de configuración
│   ├── *.local.php     # 🔒 NUNCA commitear — en .gitignore
│   └── *.production.php # 🔒 NUNCA commitear — en .gitignore
├── deploy/
│   └── deploy.php      # 🚫 NUNCA ejecutar sin confirmación explícita
└── tests/              # PHPUnit — actualmente solo tests/Unit/ (ver sección 7.1)
```

> Nota: no existe una carpeta `includes/` en la raíz. Las funciones globales del
> admin viven en `admin/include/funciones.php` e `admin/include/includes.php`
> (con "include" en singular). La lógica del front público está repartida en
> `src/php/*.php`, no en un único archivo de funciones.

---

## ANTES DE CADA TAREA — obligatorio

Antes de tocar cualquier archivo, siempre ejecutar en orden:
1. `git checkout main`
2. `git pull origin main`
3. `git checkout -b [tipo]/[nombre-descriptivo]`
4. Confirmar en qué branch estás antes de continuar

## 4. Workflow Git — SIEMPRE seguir este orden

### 4.1 Regla fundamental
**Nunca hacer push directo a `main`.** Main es siempre estable y deployable.

### 4.2 Convención de nombres de branches

| Tipo | Prefijo | Ejemplo |
|---|---|---|
| Nueva funcionalidad | `feature/` | `feature/filtro-por-talla` |
| Corrección de bug | `fix/` | `fix/stock-doble-decremento` |
| Urgente en producción | `hotfix/` | `hotfix/webhook-mp-caido` |
| Limpieza/refactor/config | `chore/` | `chore/actualizar-claude-md` |

### 4.3 Flujo completo por tarea

```bash
# 1. Siempre partir de main actualizado
git checkout main
git pull origin main

# 2. Crear branch con convención correcta
git checkout -b feature/nombre-descriptivo

# 3. Trabajar, hacer commits atómicos con mensajes claros
git commit -m "feat: agregar filtro de productos por talla"

# 4. Antes del merge: correr tests (ver sección 7)
# Si los tests pasan → continuar
# Si los tests fallan → corregir antes de mergear

# 5. Push de la branch
git push origin feature/nombre-descriptivo

# 6. Abrir PR en GitHub con:
#    - Título descriptivo
#    - Descripción breve de qué cambia y por qué
#    - Checklist de tests corridos

# 7. Merge a main (squash merge recomendado para historia limpia)
git checkout main
git merge --squash feature/nombre-descriptivo
git commit -m "feat: filtro de productos por talla (#PR)"
git push origin main
```

### 4.4 Mensajes de commit — convención

Usar prefijos semánticos:
- `feat:` nueva funcionalidad
- `fix:` corrección de bug
- `chore:` mantenimiento, dependencias, config
- `refactor:` reorganización sin cambio de comportamiento
- `test:` agregar o modificar tests
- `docs:` documentación

### 4.5 Branches — no borrar después del merge

Conservar todas las branches después de mergear. No ejecutar `git branch -d` ni 
`git push origin --delete` nunca. Las branches quedan como historial de trabajo.

---

## 5. Convenciones de código PHP

### 5.1 Naming del admin panel

Seguir el patrón de Casa de Insecticidas (proyecto de referencia):

| Prefijo | Uso |
|---|---|
| `a_` | Alta (crear registro) |
| `b_` | Baja (eliminar registro) |
| `e_` | Edición (modificar registro) |
| `xt_` | Helper/auxiliar AJAX |

Ejemplo: `a_producto.php`, `e_producto.php`, `xt_subir_imagen.php`

### 5.2 Estructura de archivos admin

Cada entidad del admin debe tener:
- `listado_[entidad].php` — tabla con registros
- `a_[entidad].php` — formulario de alta
- `e_[entidad].php` — formulario de edición
- `b_[entidad].php` — lógica de baja (POST, no GET)

### 5.3 Includes obligatorios

Todo archivo PHP del admin debe arrancar con (ruta real: `admin/include/`, no `includes/`):
```php
require_once '../include/funciones.php';
require_once '../include/includes.php';
```

### 5.4 Seguridad — reglas no negociables

```php
// CSRF: siempre validar con hash_equals, nunca con ==
if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    exit('Token inválido');
}

// Siempre exit() en fallos de validación, nunca solo return
// Siempre prepared statements, nunca concatenar SQL
// Nunca exponer errores de PHP en producción
```

---

## 6. Base de datos — reglas

### 6.1 Naming de tablas

Prefijo `tbl_` para todas las tablas. Ejemplos:
- `tbl_productos`, `tbl_skus`, `tbl_ordenes`, `tbl_usuarios`
- `tbl_wishlist`, `tbl_slider`, `tbl_config_empresa`

> La tabla de pedidos se llama `tbl_ordenes` (no `tbl_pedidos`). Estado del
> pedido: columna `estado` (VARCHAR, no ENUM), 5 valores válidos desde la
> refactorización de 2026-07: `pendiente`, `confirmado`, `enviado`,
> `entregado`, `cancelado`. Historial de cambios en `tbl_ordenes_historial`.

### 6.2 Modelo de productos (Carter's/Cheeky)

- Un producto padre en `tbl_productos`
- Variantes color+talle en `tbl_skus`
- El frontend muestra una card por producto con swatches de color
- Imagen principal por color, no por SKU

### 6.3 Permisos SQL en sesión local (Laragon)

Claude Code **puede** correr comandos SQL contra la base local de Laragon para:
- Validar migraciones
- Verificar resultados de operaciones
- Inspeccionar estado de tablas

⚠️ Siempre mostrar el resultado de la query antes de cualquier acción destructiva (DELETE, UPDATE masivo).

### 6.4 Tablas con manejo especial

| Tabla | Regla |
|---|---|
| `tbl_ordenes` | No modificar registros existentes sin confirmación |
| `tbl_skus` | No modificar `stock` directamente — usar funciones de `src/php/stock_reservation.php` |
| `tbl_sessions` | Excluida de dumps de deploy |

---

## 7. Testing con PHPUnit

### 7.1 Estructura de tests

Estado real (no hay carpeta `tests/Integration/` todavía — es aspiracional,
crearla si se agregan tests de ese tipo):

```
tests/
├── Unit/
│   ├── AdminAuthRedirectTest.php
│   ├── CheckEmailAccountStatusTest.php
│   ├── DbConnectionTest.php
│   ├── LoginOtpTest.php
│   ├── OrderEmailsTest.php
│   ├── SessionCookiePathTest.php
│   └── SessionCookieSecureTest.php
└── bootstrap.php   # requiere config.php + src/php/db.php — conecta a la DB local real
```

No existen todavía `StockTest.php`, `MercadoPagoWebhookTest.php` ni
`CheckoutFlowTest.php` — son la cobertura de alta prioridad pendiente
(ver 7.2), no código ya escrito.

### 7.2 Prioridad de cobertura

**Alta prioridad (nunca mergear sin tests verdes):**
1. Lógica de stock: reserva al crear pedido, decremento solo en webhook aprobado, liberación en rechazo o expiración 30min (`src/php/stock_reservation.php`)
2. Webhook de Mercado Pago: idempotencia, transición de estados (`src/php/mp_sync.php`) — todavía sin validación de firma `x-signature`, ver 8.3
3. Cotización Zipnova: request/response, manejo de errores
4. Flujo de checkout: validaciones, creación de pedido, redirección MP

**Media prioridad:**
- CRUD de admin (productos, SKUs, categorías)
- Sistema de usuarios (registro, login, recuperación)
- Wishlist (localStorage → server en login)

### 7.3 Correr tests antes de cada merge

```bash
# Correr toda la suite
./vendor/bin/phpunit tests/

# Solo tests unitarios
./vendor/bin/phpunit tests/Unit/

# Solo tests de integración
./vendor/bin/phpunit tests/Integration/

# Un archivo específico
./vendor/bin/phpunit tests/Unit/StockTest.php
```

**Regla:** Si `phpunit` retorna errores o failures → no mergear a main.

---

## 8. Zonas frágiles — protocolo especial

### 8.1 Deploy script

**`deploy/deploy.php` — NUNCA ejecutar sin confirmación explícita de Gonzalo.**

Antes de cualquier acción relacionada con deploy:
1. Pausar y mostrar qué se va a hacer
2. Esperar confirmación explícita ("sí, ejecutá" o similar)
3. Ejecutar y reportar resultado completo

### 8.2 Lógica de stock

El patrón correcto es **reservar → confirmar → liberar**. Nunca decrementar en dos lugares.

```
Creación de pedido → reservar stock (status: pendiente)
Webhook MP aprobado → decrementar stock definitivo
Webhook MP rechazado → liberar reserva
Cron 30min → liberar reservas expiradas
```

Si se modifica cualquier parte de este flujo, correr `StockTest.php` antes de continuar.

### 8.3 Webhook de Mercado Pago

Archivos: `webhooks/mp-notification.php` (endpoint HTTP, solo parsea y extrae
`payment_id`) + `src/php/mp_sync.php` (lógica real, función
`mp_mercadopago_sync_payment()`).

- No valida el header `x-signature` que MP envía (pendiente de implementar).
  La mitigación real hoy es que **siempre vuelve a consultar** `GET
  /v1/payments/{id}` contra la API de MP con el access token propio y solo
  actúa según esa respuesta — nunca confía en el `status` del body del
  webhook.
- Lógica idempotente: si el pedido ya está en el estado destino, no hace nada
  (`mp_sync_transition_allowed()` + comparación de estado anterior/nuevo).
- Nunca decrementar stock aquí Y en `process.php` — solo en el webhook
  (`stock_confirm_order_reservation()` en `src/php/stock_reservation.php`).
- Para probar el endpoint localmente sin credenciales reales de MP: `php
  scripts/test_webhook_mp.php <id_orden>`.

### 8.4 Archivos de configuración

```
config/*.local.php      → Solo entorno local. Nunca commitear.
config/*.production.php → Solo producción. Nunca commitear.
```

Estos archivos están en `.gitignore`. Si Claude Code necesita ver su contenido, pedirle a Gonzalo que los muestre manualmente.

---

## 9. Integraciones externas

### 9.1 Mercado Pago

- Modo: Checkout Pro
- Credenciales en: `config/mercadopago.local.php` / `config/mercadopago.production.php`
- Nunca loguear `access_token` ni `client_secret`
- Sandbox activo en dev, producción requiere credenciales de Aye
- ngrok está instalado localmente (Gonzalo) para exponer el webhook en pruebas
  de sandbox — MP no puede pegarle a `localhost`, hace falta la URL pública de
  ngrok para que `mp-notification.php` reciba las notificaciones

### 9.2 Zipnova

- Endpoint: `POST https://api.zipnova.com.ar/v2/shipments/quote`
- Auth: Basic `key:secret`
- Unidades: gramos y milímetros
- El campo `destination` requiere: `zipcode`, `city`, `state`
- `origin_id` debe ser un warehouse ID configurado en la cuenta
- `classification_id: 1`
- Resultados en `$data['all_results']` filtrados por `selectable: true`
- Credenciales en: `config/zipnova.local.php` / `config/zipnova.production.php`

---

## 10. Frontend — reglas

### 10.1 Brand tokens CSS

```css
--yofi-peach: #FAAF7D;
--yofi-dusty: #96AFC8;
--yofi-terracotta: #E1644B;
--yofi-olive: #7D7D64;
--yofi-cream: #FAE1C8;
```

Font principal: **Nunito** (Google Fonts)

### 10.2 Admin vs Frontend público

- **Admin:** Bootstrap 5 exclusivamente
- **Frontend público:** Tailwind CSS + tokens de marca

### 10.3 Regla de no tocar frontend

No modificar el frontend público hasta que los cambios en admin estén validados en Laragon y funcionen correctamente.

---

## 11. Deploy — proceso

El deploy a DonWeb es manual y tiene pasos específicos. Detalle completo del
mecanismo (detección incremental por `git diff`, flags disponibles, casos
especiales) documentado en `deploy/README.md`.

1. Correr tests → deben pasar todos
2. Mergear a main
3. Ejecutar `deploy/deploy.php` (requiere confirmación explícita)
   - Valida 6 archivos de config
   - Sube por FTP **solo los archivos modificados desde el último deploy**
     (git diff contra el commit del último deploy — ver `deploy/README.md`)
   - Exporta DB con `mysqldump` (excluye `tbl_sessions`) → `deploy/dumps/*.sql.gz`
4. Importar DB en DonWeb manualmente vía phpMyAdmin (no automatizable)

---

## 12. Lo que Claude Code NO debe hacer sin preguntar

| Acción | Motivo |
|---|---|
| Ejecutar `deploy/deploy.php` | Afecta producción directamente |
| Hacer DELETE masivo en cualquier tabla | Irreversible |
| Modificar `.gitignore` | Puede exponer configs sensibles |
| Cambiar estructura de `tbl_ordenes` o `tbl_skus` | Requiere migración coordinada |
| Instalar dependencias nuevas (composer, npm) | Gonzalo debe evaluar antes |

---

## 13. Checklist pre-merge a main

Antes de abrir el PR y mergear, verificar:

- [ ] Tests corren sin errores (`./vendor/bin/phpunit tests/`)
- [ ] No hay archivos `.local.php` o `.production.php` en el commit
- [ ] No hay `var_dump`, `print_r` o `console.log` de debug olvidados
- [ ] Nuevas tablas o columnas tienen su migration SQL documentada
- [ ] Si se tocó lógica de stock → `StockTest.php` pasa
- [ ] Si se tocó el webhook → `MercadoPagoWebhookTest.php` pasa
- [ ] Si se tocó el deploy script → confirmación manual de Gonzalo
- [ ] Mensaje del commit sigue la convención semántica
- [ ] PR tiene descripción breve de qué cambia y por qué

