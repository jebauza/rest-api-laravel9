# REST API LARAVEL9



## Comenzando 💪🚀

Estas instrucciones te permitirán obtener una copia del proyecto en funcionamiento en tu máquina local para propósitos de desarrollo y pruebas.

## Table of contents
* [Pre-requisitos 📋](#pre-requisitos)
* [Instalación 🔧](#instalación)
* [Construido con 🛠️](#construido-con)

### Pre-requisitos

_Que cosas necesitas para poner en marcha el proyecto y como instalarlos_

* GIT [Link](https://git-scm.com/downloads)
* Entorno de servidor local, Ej: [Laragon](https://laragon.org/download/), [XAMPP](https://www.apachefriends.org/es/index.html) o [LAMPP](https://bitnami.com/stack/lamp/installer).
* PHP Version ^8.1 [Link](https://www.php.net/downloads.php).
* Manejador de dependencias de PHP [Composer](https://getcomposer.org/download/).
* MariaDB: ^10.3.32 o Mysql: ^8.0.23 [link](https://mariadb.com/kb/en/mariadb-10332-release-notes/).

### Instalación

Paso a paso de lo que debes ejecutar para tener el proyecto ejecutandose

 1. Clona el repositorio dentro de la carpeta de tu servidor con el siguiente comando:
    ```
    git clone https://github.com/jebauza/rest-api-laravel9.git
    ```
 2. Ingresa a la carpeta del repositorio
    ```
    cd rest-api-laravel9
    ```
 3. Instala las dependencias del proyecto
    ```
    composer install
    ```
 4. Crea el archivo ".env" copiando la información del archivo "[.env.example](https://github.com/jebauza/rest-api-laravel9/-/blob/master/.env.example)" y cambiar valores de su Base de datos.
 5. Genera la clave de la aplicación en laravel
    ```
    php artisan key:generate
    ```
 6. Ejecute las migraciones
    ```
    php artisan migrate
    ```
 7. Ejecute lógica de despliegue
    ```
    php artisan launch:deploy
    ```
 8. Ejecute despliegue para el entorno local
    ```
    sh startup.sh
    ```
 9. Listo, ya podrá visualizar e interactuar con el proyecto en local  😁


  

### Construido con
* Framework de PHP [Laravel](https://laravel.com/docs/9.x).
