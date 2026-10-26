# Microplagio

Tema WordPress de Microplagio: tecnología, ciencia y otras rarezas.

Diseño claro con estética de terminal, tipografía editorial y detalles frikis. Sin dark mode, sin newsletter y sin dependencias de JavaScript en producción.

## Stack

- PHP 8.4+
- WordPress 6.5+
- Vite 8 + Tailwind CSS 4 (solo desarrollo)
- Fuentes autohospedadas: Space Grotesk, Source Serif 4 y JetBrains Mono
- Resaltado de código en servidor con Tempest Highlight

`assets/build/` y `vendor/` se commitean. El servidor no necesita Node ni Composer.

## Instalación en el servidor

El tema vive en `wp-content/themes/microplagio`.

```bash
cd /ruta/a/wordpress/wp-content/themes
git clone git@github.com:abelini/microplagio.git
```

Activa el tema:

```bash
wp theme activate microplagio
```

O desde el panel: **Apariencia → Temas → Microplagio → Activar**.

### Enlaces permanentes

En **Ajustes → Enlaces permanentes**:

- Estructura personalizada: `/articulos/%postname%/`
- Base de categorías: `categorias`

Con WP-CLI:

```bash
wp option update permalink_structure '/articulos/%postname%/'
wp option update category_base 'categorias'
wp rewrite flush --hard
```

### Actualizaciones

```bash
cd wp-content/themes/microplagio
git pull --ff-only
```

No hay que compilar ni instalar dependencias en el servidor.

## Desarrollo

```bash
npm ci
composer install
npm run dev
```

Build de producción:

```bash
npm run build
```

El manifiesto de Vite se lee desde `assets/build/.vite/manifest.json`.

## Estructura

```
assets/build/       Build de Vite (commiteado)
mockups/            Referencia visual aprobada
resources/css/      CSS modular
resources/js/       JS del tema
src/                PHP OOP (PSR-4)
template-parts/     Partes de plantilla
vendor/             Dependencias PHP (commiteado)
```

## Despliegue con llave SSH

Si el servidor no tiene acceso al repo privado, añade su llave pública como **Deploy Key** en GitHub:

1. Genera la llave en el servidor: `ssh-keygen -t ed25519 -C "microplagio-deploy"`
2. Copia el contenido de `~/.ssh/id_ed25519.pub`.
3. En GitHub: **Settings → Deploy keys → Add deploy key** (solo lectura).
4. Prueba: `ssh -T git@github.com`

## Notas

- No se publican versiones exactas de PHP, WordPress ni Vite en el frontend.
- El tema elimina `wp_generator` y no expone el hash real del commit.
- La portada de referencia está en `mockups/portada.html`.
