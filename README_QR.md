QR generation setup (Fudo)
==========================

Este README resume cómo instalar y usar la generación de códigos QR en el proyecto Fudo, y además documenta las nuevas mejoras UX en la carta (persistencia de carrito, toasts y accesibilidad del scroller).

Requisitos rápidos
- PHP (XAMPP) instalado y Apache corriendo.
- Carpeta del proyecto: `C:\xampp\htdocs\fudo`.
- Opcional pero recomendado: Composer y la extensión GD en PHP si quieres PNG raster.

1) Instalar con Composer (recomendado)

- Desde PowerShell en la carpeta del proyecto:

```powershell
cd C:\xampp\htdocs\fudo
& 'C:\xampp\php\php.exe' 'C:\xampp\php\composer.phar' require endroid/qr-code
```

- Esto añadirá `vendor/autoload.php`. El wrapper `application/libraries/Ciqrcode.php` detecta automáticamente `vendor/autoload.php` y usará `endroid/qr-code` si está presente. Si no quieres usar Composer, el proyecto incluye un fallback con `phpqrcode` en `application/third_party/phpqrcode/`.

2) Habilitar GD para PNG raster (opcional pero recomendado)

- En XAMPP edita `C:\xampp\php\php.ini` y habilita la extensión GD (quita el `;`):

```ini
extension=gd
```

- Reinicia Apache desde el panel de XAMPP.

3) Probar la generación de QR (controlador de prueba)

- Visita en el navegador:

```
http://localhost/fudo/index.php/dev_qr/generar
```

- Deberías ver un mensaje indicando el archivo generado (por ejemplo `assets/qr/mesa_test.png` o `mesa_test.svg` si GD no está activo). Comprueba la carpeta `assets/qr/`.

4) Generar QR para mesas (API)

- Hay un endpoint en `application/controllers/Mesas.php` (método `generar_qr($id)`) que genera el QR para la mesa, guarda el archivo en `assets/qr/` y actualiza el campo `mesas.codigo_qr` en la base de datos. Desde la UI de admin -> Mesas hay un botón que invoca esta acción por AJAX y actualiza la fila.

5) Problemas comunes y soluciones rápidas

- Composer no en PATH: usa la ruta a `composer.phar` con `php` (ejemplo arriba).
- Permisos: si PHP no puede escribir en `assets/qr`, crea la carpeta y asegúrate que el usuario de Apache (en Windows suele ser el mismo usuario que ejecuta Apache) tiene permisos de escritura.
- Hooks pos-install en Windows: si Composer falló por scripts que usan comandos Unix (`sed`, `mv`), aplica los fixes con PowerShell o ejecuta `composer install` en un entorno WSL o Git Bash. Durante la instalación guiada en este proyecto se resolvió un caso con un reemplazo manual usando PowerShell.

Nuevas UX en la carta
----------------------

- Sticky categories: la vista de carta ahora muestra una fila de "pills" categorizadas, con flechas laterales para scrollear. Las pills son accesibles por teclado (Enter/Space) y tienen atributos ARIA.
- Iconos: las categorias usan SVG inline para consistencia (en vez de emojis).
- Tarjetas de producto: muestran nombre en mayúsculas, descripción, precio con 2 decimales y una línea con el precio "Sin impuestos nacionales" calculada como `precio/1.21`.
- Persistencia de carrito: el carrito se guarda en `localStorage` (clave `fudo_cart_v1`) para sobrevivir recargas de página.
- Toasts: al agregar un producto se muestra un toast Bootstrap confirmando la acción; al crear el pedido también se puede mostrar feedback (implementación básica incluida).

Pruebas y verificación
-----------------------

1. Abre en el navegador: `http://localhost/fudo/mesa/1` (o la ruta que uses). Si usas pretty URLs y tienes `mod_rewrite`, la ruta funcionará sin `index.php`. Si no, usa `http://localhost/fudo/index.php/mesa/1`.
2. Revisa que las categorías aparezcan con flechas y puedas navegar con mouse/teclado.
3. Agrega productos al carrito: deberían persistir tras recarga y mostrar toasts.
4. Envía un pedido y comprueba que la API `pedidos/crear` responde OK y/o muestra mensaje. (El backend ya implementa la creación del pedido; si quieres que muestre un toast al finalizar, lo podemos añadir).

Notas para desarrolladores
-------------------------

- Wrapper QR: `application/libraries/Ciqrcode.php` decide automáticamente entre `endroid/qr-code` y el fallback `phpqrcode`.
- Vista principal carta: `application/views/carta/index.php` contiene la mayoría de los cambios visuales y JS. Revisa ahí si quieres ajustar colores o reemplazar los SVG por iconos reales.

Si quieres que deje los iconos en un sprite SVG, o que use FontAwesome, lo implemento y reemplazo los SVG inline.

¿Siguiente paso?
- Puedo pulir colores y tipografías para que queden exactos a la captura que me enviaste.
- Puedo implementar toasts al confirmar envío de pedido y limpiar el carrito tras éxito.
- Puedo añadir persistencia del carrito en el servidor si prefieres (requiere endpoints y autenticación / identificación de mesa adicional).

Dime cuál de estas opciones quieres que haga ahora y lo implemento.
