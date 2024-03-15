# Real Time Strategies

En este repositorio se muestran las diversas estrategias para interactuar con una aplicación en tiempo real; fue desarrollada en un PC con Windows 11.

Las diferencias principales con [este demo](https://github.com/benbjurstrom/livewire-mercure-demo) son:

- La interfaz de la aplicación está traducida al español (con soporte para multilenguaje - solo falta implementar el selector de lenguaje), además tiene soporte para modo oscuro.
- No uso **Docker** sino todos los servicios se inician desde Windows (Uso [Laragon](https://laragon.org/download/index.html)).
- Los mensajes son persistidos en la base de datos.
- El [demo](https://github.com/benbjurstrom/livewire-mercure-demo) en mención solo muestra la estrategia **Eventos del servidor** usando [Mercure](https://mercure.rocks/).

Es un script sencillo que consta de un comando de consola que simula enviar mensajes desde el servidor (públicos y privados) y se reciben a través de un componente de Livewire y las diversas configuraciones para cada una de las estrategias se encuentran en ramas distintas.

## Estrategias usadas para la comunicación en tiempo real
Una descripción en español de estas estrategias se encuentra en el artículo de [Codigofacilito](https://codigofacilito.com/articles/266), que recomiendo leer.
1. [No real time](https://github.com/luisprmat/real-time-strategies/tree/no-real-time): No hay emisión de los mensajes en tiempo real, el comando los envía pero cada usuario debe refrescar el navegador para que se haga la consulta a la Base de Datos y así poder ver los nuevos mensajes sean públicos o privados.
2. [Polling](https://github.com/luisprmat/real-time-strategies/tree/polling): Usa la directiva `wire:poll` de [**Livewire**](https://livewire.laravel.com/docs/wire-poll) para hacer peticiones al servidor cada *2.5s* e ir hidratando la información en el componente si hay nuevos mensajes.
3. [Websockets (Pusher)](https://github.com/luisprmat/real-time-strategies/tree/pusher): Configuramos websockets usando [Pusher](https://pusher.com/), un servicio de terceros especializado en *realtime* pero tiene limitaciones ya que es un servicio de paga si se superan ciertos límites en el número de eventos o usuarios suscritos o concurrentes.
4. [Websockets (Soketi)](https://github.com/luisprmat/real-time-strategies/tree/soketi): Usa el servidor de websockets [Soketi](https://docs.soketi.app) basado en Node.js y [`uWebSockets.js`](https://github.com/uNetworking/uWebSockets.js), muy rápido y usa el mismo driver de **pusher** por lo que no se require instalar paquetes adicionales, solo instalar el servidor de manera global usando `npm install -g @soketi/soketi` y correrlo en un nuevo terminal (recomiendo [PowerShell](https://learn.microsoft.com/es-es/powershell/scripting/install/installing-powershell-on-windows?view=powershell-7.4) ya que pemite configurar variables de entorno con la sintaxis `$env:MY_VARIABLE='valor';`) con `soketi start`.

    *Ventajas:*
    - Es de código abierto, por lo que sólo se paga por la infraestructura del servidor pero no tiene límites teóricos en cuanto a número de mensajes.

    *Desventajas*
    - Se ha detenido el mantenimiento de este proyecto desde mediados de 2023 y en este momento (*Marzo 6 de 2024*) no es compatible con las últimas versiones de [Node.js](https://nodejs.org) (`Node ^20 - npm ^10.4`), para que funcione necesitamos `Node <= 18` y `npm < 10`.
5. [Websockets (Laravel Reverb)](https://github.com/luisprmat/real-time-strategies/tree/reverb): Nada mejor que los paquetes oficiales y la facilidad de Laravel, en este caso [Laravel Reverb](https://reverb.laravel.com). Para que funcione en *Laravel 10* basta con que usemos **PHP 8.2** o superior y podemos:
    - Instalar el paquete `composer require laravel/reverb:@beta`, sí, aún está en beta.
    - Instalar la configuración del lado del servidor con `php artisan reverb:install`. Este comando modifica el archivo `config/broadcasting.php` agregando el driver  de `reverb`, agrega el archivo de configuración `config/reverb.php`, copia las variables de entorno requeridas por **reverb** directamentente en el `.env` generando claves aleatorias y cambia el driver de conexión en el `.env` a `BROADCAST_DRIVER=reverb`.
    - Modificar la configuración del lado del cliente modificando el archivo `resources/js/bootstrap.js` por las variables de reverb
    ```js
    import Echo from 'laravel-echo';
 
    import Pusher from 'pusher-js';
    window.Pusher = Pusher;
    
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST,
        wsPort: import.meta.env.VITE_REVERB_PORT,
        wssPort: import.meta.env.VITE_REVERB_PORT,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
    });
    ```
    - Ejecutar `npm run build` para compilar el nuevo javascript
    - Encender el servidor con `php artisan reverb:start` y ¡listos!
6. [Eventos del servidor (Mercure)](https://github.com/luisprmat/real-time-strategies/tree/mercure): Usa el hub de [Mercure](https://mercure.rocks/) (un sustituto moderno para websockets) basado en el servidor **Caddy**, escrito en [**Go**](https://go.dev/) que se caracteriza por su rapidez.

    *Para correr Mercure server en windows:*
    - Instale el [servidor de Mercure](https://github.com/dunglas/mercure/releases) adecuado a su sistema operativo, en  mi caso uso [mercure_Windows_x86_64.zip](https://github.com/dunglas/mercure/releases/download/v0.15.9/mercure_Windows_x86_64.zip).
    - Descomprímalo y abra el archivo `Caddyfile.dev`.
    - Busque la línea `cors_origins *` y cámbiela por `cors_origins http://localhost:8080`. (Esto para no tener problemas de acceso por CORS, y también debe lanzar su aplicación desde `php artisan serve --port=8080`)
    - Abra un terminal de **Powershell** y ubíquese en la carpeta donde descomprimió el `Caddyfile.dev` y `mercure.exe` (`cd <ruta_carpeta>`)
    - Defina las variables de entorno: `$env:MERCURE_PUBLISHER_JWT_KEY='!ChangeThisMercureHubJWTSecretKey!';`
    - `$env:MERCURE_SUBSCRIBER_JWT_KEY='!ChangeThisMercureHubJWTSecretKey!';`
    - `$env:SERVER_NAME=':8888';`
    - Inicie el servidor: `.\mercure.exe run --config Caddyfile.dev`

## Guía rápida de instalación
1. Clone el repositorio: `git clone https://github.com/luisprmat/real-time-strategies.git`
2. Entre al directorio: `cd real-time-strategies`
3. Instale las dependencias de composer (que debe estar instalado): `composer install`
4. Cree una copia de las variables de entorno: `cp .env.example .env`
5. Asigne la clave de la aplicación: `php artisan key:generate`
6. Instale las dependencias de Node: `npm install && npm run build`
7. Configure la base de datos: Depende el motor que quiera usar
    - *Sqlite* (Por defecto en Laravel 11): El más fácil `touch database/database.sqlite` y asegúrese que `DB_CONNECTION=sqlite`
    - *Mysql*: Mantenga la configuración por defecto (Laravel <= 10) y al correr las migraciones díga que **SI** cree la base de datos
8. Corra las migraciones y los seeders: `php artisan migrate --seed`
9. Acceda a la aplicación desde el navegador (dos formas):
    - Con Laragon se crea un *virtualhost*: `http://real-time-strategies.test`
    - Use el servidor de Laravel: `php artisan serve --port=8080` y acceda con `http://localhost:8080`
        - *Nota: lo del cambio de puerto es por si quiere tener disponibles las dos formas sin entrar en conflictos por puertos ocupados, ya que laragon usa el puerto 80 por defecto.*
10. Haga *login* con el usuario `usuario1@example.com` contraseña `password`
11. En un navegador distinto (o en modo incógnito) haga *login* con el usuario `usuario2@example.com` contraseña `password`
12. Ubíquese en la rama de la estrategia que quiere observar y configure todo de forma correcta: `git checkout <rama>`
13. Use el comando artisan `php artisan message:send` para enviar los mensajes y observar su comportamiento según las ditintas estrategias.

## Detalles técnicos
Este repositorio fue creado desde una instalación fresca de Laravel 10 con el paquete Laravel Breeze (Livewire Volt Class API) para tener Livewire y la autenticación. Los invito a leer la documentación de cada servicio para obtener los detalles precisos de la implementación y configuración según la estrategia a observar.

[*Luis Parrado*](https://github.com/luisprmat): Programador web
