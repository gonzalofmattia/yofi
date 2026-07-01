# Deploy de Yofi a DonWeb

Deploy manual vía FTP, siempre desde `main` recién actualizado. Nunca se
ejecuta `deploy/deploy.php` sin que Gonzalo lo confirme explícitamente.

## Cómo funciona la detección de archivos modificados

`deploy/deploy.php` **no sube el proyecto entero en cada deploy**: sube solo
los archivos que cambiaron desde el último deploy exitoso.

Para eso guarda en `deploy/.deploy-manifest.json` (gitignored, no se
versiona) el commit de git del último deploy. En el siguiente deploy:

1. Calcula `git diff --name-only <último-commit-deployado> HEAD` para saber
   qué cambió entre deploys.
2. Suma cualquier cambio sin commitear (`git status --porcelain`), por si
   quedó algo sin subir a un branch.
3. De esa lista: los archivos que siguen existiendo en el filesystem se
   suben por FTP; los que ya no existen se borran del servidor remoto.
4. Si el deploy termina sin errores, guarda el commit actual como nuevo
   baseline.

Esto reemplazó un manifest anterior basado en `size + mtime` de cada
archivo, que en la práctica **nunca detectaba "sin cambios"**: cada
`git checkout` / `pull` / squash-merge actualiza el `mtime` de todos los
archivos aunque el contenido no cambie, así que el deploy terminaba subiendo
los ~250 archivos del proyecto por FTP cada vez (ver historial en
`deploy/deploy.log` antes de este cambio). Usar `git diff` en vez de mtime
elimina ese problema de raíz: el diff es por contenido de commit, no por
timestamps de archivo.

### Casos especiales

- **Primer deploy / sin baseline:** si no hay `last_commit` guardado (o no es
  un commit válido, por ejemplo tras un rebase que reescribió historia), se
  hace una subida completa y esa queda como nuevo baseline.
- **`--full`:** ignora el baseline y fuerza una subida completa. Útil si el
  servidor quedó desincronizado o hay dudas sobre el estado remoto.
- **Archivos `config/*.production.php`:** no están versionados (están en
  `.gitignore` porque tienen credenciales), así que `git diff` no los ve. Se
  comparan aparte por hash de contenido (sha1), guardado también en el
  manifest. Son solo 5 archivos, el costo de hashearlos es insignificante.
- **Base de datos:** el dump de MySQL nunca es incremental — se exporta
  completo en cada deploy que no use `--files-only` (igual que antes).

## Comandos

```bash
php deploy/deploy.php                  # Deploy completo (dump + archivos, incremental)
php deploy/deploy.php --files-only     # Solo archivos, sin exportar la BD
php deploy/deploy.php --config-only    # Solo subir config/*.production.php → *.local.php remoto
php deploy/deploy.php --full           # Ignora el baseline y sube todo el proyecto
php deploy/deploy.php --dry-run        # Muestra qué se subiría/borraría, sin tocar FTP ni exportar BD
php deploy/deploy.php --force-db       # Omite la advertencia si prod ya tiene datos
```

Combinables, ej. `php deploy/deploy.php --files-only --dry-run` para ver qué
archivos se subirían sin exportar la base ni tocar el FTP.

## Flujo recomendado

1. Mergear todas las branches que correspondan a `main`.
2. Pararse en `main` actualizado (`git checkout main && git pull`).
3. Correr `php deploy/deploy.php --dry-run` y revisar la lista de archivos.
4. Si está bien, correr `php deploy/deploy.php` (con confirmación explícita
   de Gonzalo, según CLAUDE.md).
5. Si el deploy incluyó cambios de base de datos, importar el `.sql.gz`
   generado en `deploy/dumps/` manualmente vía phpMyAdmin en DonWeb (paso
   manual, no automatizado — ver salida del script).

## Requisitos previos

1. `config/deploy.local.php` (copiar desde `config/deploy.local.php.example`)
2. `config/*.production.php` completados (ver `config/*.production.php.example`)
3. `mysqldump` disponible (Laragon lo incluye)
4. Extensión FTP de PHP habilitada
