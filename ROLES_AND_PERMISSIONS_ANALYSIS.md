# Анализ системы ролей и прав

## Две отдельные подсистемы

### 1. Глобальные роли (Spatie Laravel Permission)

**Таблицы:** `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`

**Назначение:**
- Ответ на вопрос: «Имеет ли пользователь доступ в приложение вообще и куда?»
- **Роли:** `admin`, `manager`, `support`, `user`
- **Проверки:** `panel.access` (вход в админ-панель), `client.access` (вход в клиентскую часть), все права вида `panel.*` на маршрутах админки

**Где используется:**
- Middleware: `CheckPermission` (Spatie `hasAnyPermission`), `CheckRole`, `OnlyPanelAccess`, `OnlyClientAccess`
- Маршруты: `check.permission:panel.xxx`, `only.panel`, `only.client`
- В шаблонах: `@can('panel.access')`, `$user->can('client.access')`

**Роль `user`** имеет только `client.access` и `client.subscription.pay`. Конкретные действия в бизнесе (клиенты, записи и т.д.) **не** проверяются через Spatie.

---

### 2. Роли бизнеса (кастомная подсистема)

**Таблицы:** `business_roles`, `business_role_permissions`, в `business_user` — поле `role_id`

**Назначение:**
- Ответ на вопрос: «Что этот пользователь может делать **в выбранном бизнесе**?»
- **Роли:** системные (`owner`, `admin`, `master`) + кастомные (привязаны к `owner_id`)

**Проверки:** все права вида `client.*` (кроме `client.access`) — через `BusinessRolePermissionService` и middleware `CheckBusinessRolePermission`.

**Где используется:**
- Маршруты клиентской части: `check.business.permission:client.clients.view`, `client.appointments.create` и т.д.
- Контроллеры: `authorizeBusinessPermission()`, `$permissionService->hasPermission($role->id, 'client.xxx')`

---

## Связь между подсистемами

- В **Spatie** в таблице `permissions` лежат **все** имена прав: и `panel.*`, и `client.*` (включая описания).
- Для **проверки доступа** в клиентской части используются только записи в `business_role_permissions` (роль в бизнесе + право).
- Список «какие бывают client.* права» для UI (настройки ролей бизнеса) и для сидера дефолтных ролей берётся из **Spatie** (`Permission::where('name', 'like', 'client.%')`).

Итого: имена и описания client-прав хранятся в Spatie, факт «роль X в бизнесе имеет право Y» — в `business_role_permissions`. Дублирования логики проверки нет: в клиентской части везде проверяется только бизнес-роль.

---

## Избыточность: где есть, где нет

### Не избыточно

1. **Два уровня (глобальный и бизнес)**  
   Один пользователь может входить в несколько бизнесов с разными ролями. Глобальные роли (Spatie) решают «есть ли доступ в panel/client», роли бизнеса — «что можно делать в этом бизнесе». Разделение обосновано.

2. **Разные middleware для panel и client**  
   `check.permission` (Spatie) для panel, `check.business.permission` (бизнес-роль) для client — разная природа прав, объединять в один механизм не нужно.

3. **Правила вида `.own`**  
   `client.appointments.view.own`, `client.clients.view.own` и логика в `BusinessRolePermissionService` (wildcards, приоритет deny, проверка «только свои») — одна согласованная модель, не дублирует Spatie.

4. **Роль owner «всё может»**  
   Реализовано одним условием в `hasPermission()` (slug === 'owner'), без хранения всех прав в БД — ок.

### Потенциальная избыточность

1. **Хранение имён client.* в Spatie**  
   - Сейчас: все `client.*` (кроме `client.access`) лежат в `permissions` и используются только как справочник для сидера и для UI («все возможные client-права»).
   - Проверки в коде идут только по `business_role_permissions`.
   - Упрощение: вынести список имён и описаний client-прав в конфиг (или один метод/класс). Тогда в Spatie хранить только `panel.*` + `client.access` + `panel.access`. Сидер дефолтных бизнес-ролей и оба `BusinessRolePermissionsController` будут брать список из конфига. Плюс: один источник прав для клиентской части, меньше зависимости от Spatie для client.*.

2. **Два контроллера с одним именем**  
   - `App\Http\Controllers\Settings\BusinessRolePermissionsController` — настройка ролей **в своём бизнесе** (клиентская часть).
   - `App\Http\Controllers\Panel\BusinessRolePermissionsController` — настройка **системных** ролей бизнеса (админ-панель).
   - Логика и метод `getAllPermissions()` в них совпадают. Имеет смысл вынести общую часть (список client-прав + описания) в сервис или конфиг и переиспользовать в обоих контроллерах, чтобы не дублировать код.

3. **Количество прав**  
   - Много прав с суффиксами `.view`, `.create`, `.update`, `.delete` по сущностям (clients, appointments, services, …). Для гибкой настройки ролей это нормально; объединять в одно право «управление X» означало бы потерять детализацию. С точки зрения избыточности система не перегружена.

---

## Рекомендации

| Что | Действие |
|-----|----------|
| Два уровня (Spatie + бизнес-роли) | Оставить как есть. |
| Имена client.* в Spatie как справочник | По желанию: вынести в конфиг/сервис, в Spatie оставить только panel.* и client.access. |
| Два BusinessRolePermissionsController | Вынести общий код (getAllPermissions / список client-прав и описаний) в один сервис или конфиг. |
| Детализация прав (view/create/update/delete) | Оставить; не объединять в крупные права без потребности. |

**Итог:** система не избыточна по смыслу: разделение на глобальный доступ (Spatie) и права внутри бизнеса (business_roles) оправдано. Упростить можно хранение и получение **списка** client-прав (конфиг + общий код в двух контроллерах), не меняя самой модели проверок.
