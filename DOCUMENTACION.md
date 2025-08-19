# Documentación del Proyecto: Help Desk Arpesod

## 1. Introducción

El proyecto **Help Desk Arpesod** es un sistema de mesa de ayuda diseñado para centralizar, gestionar y dar seguimiento a las solicitudes de soporte (tickets) dentro de una organización. Su objetivo principal es optimizar la comunicación entre los usuarios que reportan incidencias y el equipo de soporte técnico que las resuelve, asegurando un proceso ordenado y medible.

El sistema está diseñado para ser utilizado por tres roles principales:

*   **Usuarios Finales:** Cualquier empleado o cliente de la organización. Pueden crear nuevos tickets para reportar problemas o solicitar servicios, adjuntar archivos, y dar seguimiento al estado de sus solicitudes.

*   **Personal de Soporte (Agentes):** El equipo técnico encargado de resolver las solicitudes. Reciben tickets asignados, gestionan su ciclo de vida (investigación, reasignación, cierre), se comunican con el usuario a través de la plataforma y siguen flujos de trabajo predefinidos.

*   **Administradores:** Usuarios con permisos elevados. Son responsables de configurar y mantener el sistema. Esto incluye la gestión de usuarios, roles, categorías, prioridades, y la configuración de los flujos de trabajo automatizados. También tienen acceso a reportes y KPIs para medir el rendimiento del equipo de soporte.

## 2. Características Principales

El sistema cuenta con un conjunto completo de funcionalidades para una gestión de soporte técnico eficiente.

*   **Gestión de Tickets**
    *   **Creación de Tickets:** Los usuarios pueden crear tickets detallados, especificando categoría, subcategoría, prioridad y una descripción completa con formato enriquecido (gracias a la integración de Summernote).
    *   **Adjuntar Archivos:** Posibilidad de adjuntar múltiples documentos o imágenes tanto en la creación de tickets como en los comentarios de seguimiento.
    *   **Seguimiento y Comentarios:** Se mantiene un historial completo de cada ticket, visible en una línea de tiempo, que incluye comentarios, cambios de estado y reasignaciones.
    *   **Asignación y Reasignación:** Los tickets pueden ser asignados manual o automáticamente a los agentes de soporte, dependiendo de los flujos de trabajo configurados.
    *   **Cierre y Reapertura:** Funcionalidad para cerrar tickets resueltos y la opción de reabrirlos si el problema persiste.

*   **Automatización y Flujos de Trabajo**
    *   **Flujos de Trabajo Configurables:** Permite crear flujos de trabajo paso a paso que se asocian a subcategorías específicas de tickets, definiendo la ruta que debe seguir una solicitud.
    *   **Mapeo de Flujos:** Sistema de reglas para determinar qué cargos de usuario pueden crear ciertos tipos de tickets y a quién deben ser asignados inicialmente.
    *   **Reglas de Aprobación:** Capacidad para configurar reglas que requieren la aprobación de un jefe antes de que un flujo de trabajo se inicie formalmente.

*   **Notificaciones en Tiempo Real**
    *   Gracias a la implementación de **WebSockets** con la librería Ratchet, el sistema notifica a los usuarios y agentes instantáneamente sobre eventos importantes (nuevos tickets, comentarios, asignaciones) sin necesidad de recargar la página.

*   **Dashboard y Reportes**
    *   Un panel de control (dashboard) dinámico que presenta métricas clave (KPIs) y gráficos sobre el rendimiento del sistema y del equipo de soporte.
    *   **Visualización de Datos:** Gráficos de tendencias de tickets, carga de trabajo por agente, tickets por categoría, etc., generados con Chart.js.
    *   **Filtros Avanzados:** Posibilidad de filtrar los datos del dashboard por departamento, subcategoría o un ticket específico para un análisis más granular.

*   **Administración del Sistema (Mantenimiento)**
    *   Módulos de gestión completos (CRUD: Crear, Leer, Actualizar, Eliminar) para las entidades fundamentales del sistema:
        *   Usuarios, Roles y Permisos.
        *   Empresas, Departamentos y Regionales.
        *   Cargos.
        *   Categorías y Subcategorías.
        *   Prioridades.
        *   Respuestas Rápidas para agilizar la atención de tickets.

*   **Cargas Masivas de Datos**
    *   El sistema incluye herramientas para la importación de datos desde archivos Excel, facilitando la configuración inicial y la carga masiva de: Cargos, Categorías, Subcategorías, Flujos y sus mapeos.

## 3. Arquitectura y Tecnologías

El sistema está construido sobre una pila tecnológica tradicional de PHP, con una clara separación de responsabilidades siguiendo el patrón arquitectónico **Modelo-Vista-Controlador (MVC)**.

### Arquitectura

*   **Modelo (Model):** Ubicado en el directorio `/models`. Contiene las clases PHP que representan los objetos de negocio (ej. `Ticket.php`, `Usuario.php`) y encapsulan toda la lógica de interacción con la base de datos. Utiliza **PDO (PHP Data Objects)** para un acceso seguro y estandarizado a los datos.

*   **Vista (View):** Ubicado en el directorio `/view`. Compuesto por archivos PHP que generan el HTML, junto con los archivos JavaScript (`.js`) y CSS que definen la interfaz de usuario y la interactividad del lado del cliente. Se apoya fuertemente en **jQuery** y **Bootstrap**.

*   **Controlador (Controller):** Ubicado en el directorio `/controller`. Son scripts PHP que actúan como intermediarios. Reciben las peticiones del usuario (a través de parámetros `op` en la URL), invocan los métodos necesarios de los Modelos para procesar los datos y finalmente cargan las Vistas o devuelven respuestas en formato **JSON** para las peticiones AJAX.

### Tecnologías Backend

*   **Lenguaje:** PHP
*   **Gestor de Dependencias:** Composer
*   **Librerías PHP Clave:**
    *   `vlucas/phpdotenv`: Para la gestión de variables de entorno desde un archivo `.env`.
    *   `phpmailer/phpmailer`: Para toda la funcionalidad de envío de correos electrónicos transaccionales.
    *   `phpoffice/phpspreadsheet`: Para la lectura de archivos Excel (`.xlsx`, `.xls`) en los módulos de cargas masivas.
    *   `ratchet/ratchet`: Para la implementación del servidor de **WebSockets** que da soporte a las notificaciones en tiempo real.
*   **Acceso a Base de Datos:** PDO (PHP Data Objects)

### Tecnologías Frontend

*   **Librería Principal:** jQuery
*   **Framework CSS:** Bootstrap
*   **Plugins JavaScript Notables:**
    *   **DataTables:** Para crear tablas interactivas con paginación, búsqueda y exportación.
    *   **SweetAlert:** Para mostrar alertas y modales de confirmación amigables.
    *   **Summernote:** Como editor de texto enriquecido (WYSIWYG) en las descripciones de los tickets.
    *   **FullCalendar:** Para la visualización de eventos en la sección de calendario.
    *   **Chart.js:** Para la generación de todos los gráficos en el dashboard de reportes.
    *   **Select2:** Para mejorar los elementos `<select>` con búsqueda y selección múltiple.

### Base de Datos

*   **Motor:** MySQL o MariaDB (inferido por la sintaxis en los archivos `.sql` de respaldo).

### Servidor Web

*   **Servidor:** Apache HTTP Server (inferido por el uso de un archivo `.htaccess` para la reescritura de URLs).

## 4. Estructura del Proyecto

El proyecto está organizado en una estructura de carpetas que sigue las mejores prácticas y el patrón MVC, facilitando la ubicación del código y la separación de responsabilidades.

*   **/config**: Contiene los archivos de configuración. El más importante es `conexion.php`, que gestiona la conexión a la base de datos y la carga de las variables de entorno (desde el archivo `.env`).

*   **/controller**: Almacena los controladores de la aplicación. Estos archivos contienen la lógica de negocio, procesan las entradas del usuario y actúan como puente entre los Modelos y las Vistas.

*   **/models**: Contiene las clases que representan las entidades de la base de datos (ej. `Ticket`, `Usuario`). Se encargan de todas las operaciones de datos (consultas, inserciones, etc.).

*   **/view**: Es la capa de presentación. Contiene subdirectorios para cada página o módulo de la aplicación, incluyendo los archivos `.php` que renderizan el HTML y los `.js` con la lógica del lado del cliente.

*   **/public**: El directorio raíz web (DocumentRoot). Contiene todos los activos de acceso público como hojas de estilo (`css`), imágenes (`img`), fuentes (`fonts`) y librerías JavaScript de terceros. También almacena los documentos subidos por los usuarios.

*   **/cargues**: Scripts especializados para realizar cargas masivas de datos desde archivos Excel. Cada archivo corresponde a un módulo de gestión específico (cargos, categorías, etc.).

*   **/Event**: Contiene la lógica para el manejo de eventos en tiempo real. `NotificationServer.php` es la implementación del servidor de WebSockets.

*   **/vendor**: Directorio estándar de Composer donde se instalan todas las dependencias de PHP de terceros.

*   **/docs**: Carpeta destinada a la documentación del proyecto, principalmente los respaldos de la base de datos (`.sql`).

*   **Archivos Raíz Importantes:**
    *   `index.php`: Punto de entrada principal de la aplicación web.
    *   `server.php`: Script ejecutable para iniciar el servidor de notificaciones WebSocket.
    *   `composer.json`: Define las dependencias de PHP del proyecto.
    *   `.env.example`: Plantilla para el archivo de configuración de entorno `.env`.
    *   `.cpanel.yml`: Archivo de configuración para el despliegue en cPanel.

## 5. Instalación y Configuración

Sigue estos pasos para configurar el proyecto en un entorno de desarrollo local.

### Requisitos Previos

*   PHP 8.0 o superior
*   Composer 2.x
*   Servidor web Apache 2.4 (con `mod_rewrite` habilitado)
*   Base de datos MySQL 5.7+ o MariaDB 10.2+
*   Git

### Pasos de Instalación

1.  **Clonar el repositorio:**
    ```bash
    git clone <URL_DEL_REPOSITORIO>
    cd help-desk-arpesod
    ```

2.  **Instalar dependencias de PHP:**
    Ejecuta el siguiente comando en la raíz del proyecto para instalar las librerías requeridas.
    ```bash
    composer install
    ```

3.  **Configurar el entorno:**
    Copia el archivo de ejemplo `.env.example` a un nuevo archivo llamado `.env`.
    ```bash
    cp .env.example .env
    ```
    Luego, abre el archivo `.env` y configura las siguientes variables con los datos de tu entorno local:
    *   `APP_URL`: La URL base de tu proyecto (ej. `http://helpdesk.test`).
    *   `DB_HOST`: El host de tu base de datos (ej. `127.0.0.1`).
    *   `DB_NAME`: El nombre de tu base de datos.
    *   `DB_USER`: El usuario de la base de datos.
    *   `DB_PASS`: La contraseña del usuario.

4.  **Crear e importar la Base de Datos:**
    *   Crea una nueva base de datos en MySQL/MariaDB con el nombre que especificaste en la variable `DB_NAME`.
    *   Importa la estructura y los datos iniciales desde uno de los archivos de respaldo ubicados en la carpeta `/docs`. Se recomienda usar la versión más reciente.
    ```bash
    mysql -u TU_USUARIO -p TU_BASE_DE_DATOS < docs/backup-v-9.sql
    ```

5.  **Configurar el Servidor Web (Apache):**
    *   Configura un Virtual Host en Apache para que apunte al directorio `/public` del proyecto. Este será el `DocumentRoot`.
    *   Asegúrate de que el módulo `mod_rewrite` esté habilitado para que las URLs amigables funcionen correctamente.
    *   Ejemplo de configuración de Virtual Host:
        ```apache
        <VirtualHost *:80>
            ServerName helpdesk.test
            DocumentRoot "/ruta/absoluta/a/help-desk-arpesod/public"
            <Directory "/ruta/absoluta/a/help-desk-arpesod/public">
                AllowOverride All
                Require all granted
            </Directory>
        </VirtualHost>
        ```
    *   Recuerda reiniciar Apache después de crear o modificar la configuración.

6.  **Iniciar el Servidor de Notificaciones:**
    Para que las notificaciones en tiempo real funcionen, debes iniciar el servidor de WebSockets. Abre una nueva terminal en la raíz del proyecto y ejecuta:
    ```bash
    php server.php
    ```
    Este proceso debe mantenerse en ejecución mientras se utiliza la aplicación.

7.  **Acceder a la aplicación:**
    Una vez completados todos los pasos, puedes acceder a la aplicación a través de la URL que configuraste en tu Virtual Host (ej. `http://helpdesk.test`).

## 6. Componentes Clave (Versión Detallada)

A continuación, se desglosan los archivos más importantes del sistema. Su comprensión es fundamental para entender el flujo de datos y la lógica de negocio de la aplicación.

### `config/conexion.php`

*   **Responsabilidad Principal:** Actúa como la piedra angular para toda la interacción con la base de datos y la configuración del entorno de la aplicación.

*   **Funcionamiento Detallado:**
    1.  **Carga de Entorno:** Utiliza la librería `vlucas/phpdotenv` para leer el archivo `.env` ubicado en la raíz del proyecto. Esto permite mantener las credenciales y configuraciones sensibles (como contraseñas de BD, URLs) fuera del control de versiones.
    2.  **Clase `Conectar`:** Define una clase `Conectar` que es heredada por todas las clases del directorio `/models`. Esto significa que cada modelo tiene acceso a los métodos de conexión.
    3.  **Método `Conexion()`:** Este es el método central. Establece la conexión a la base de datos utilizando **PDO (PHP Data Objects)**. Lee las variables `DB_HOST`, `DB_NAME`, `DB_USER`, y `DB_PASS` del entorno para construir el DSN (Data Source Name). Incluye un bloque `try-catch` para manejar cualquier error de conexión de forma segura, deteniendo la ejecución si la base de datos no es accesible.
    4.  **Método `set_names()`:** Ejecuta la consulta `SET NAMES 'utf8'` en la conexión, asegurando que todos los datos intercambiados con la base de datos manejen correctamente los caracteres especiales y acentos.
    5.  **Método `ruta()`:** Devuelve el valor de la variable de entorno `APP_URL`. Es utilizado en toda la aplicación para construir URLs absolutas, lo que previene errores en redirecciones y al enlazar recursos.

### `models/Ticket.php`

*   **Responsabilidad Principal:** Es la clase más extensa y crítica del sistema. Modela la entidad "Ticket" y encapsula toda la lógica de negocio y las operaciones de base de datos relacionadas con el ciclo de vida de un ticket.

*   **Interacciones con Tablas:**
    *   `tm_ticket` (tabla principal de tickets)
    *   `td_ticketdetalle` (comentarios e historial de texto)
    *   `th_ticket_asignacion` (historial de asignaciones y pasos de flujo)
    *   `tm_notificacion` (creación de notificaciones)

*   **Métodos Clave:**
    *   `insert_ticket()`: Recibe todos los datos del formulario de nuevo ticket. No solo inserta en `tm_ticket`, sino que también crea el primer registro en `th_ticket_asignacion` (el estado inicial) y un registro en `tm_notificacion` para el agente asignado.
    *   `listar_ticket_x_usuario()` / `listar_ticket_x_agente()`: Realizan complejas consultas `JOIN` para unir `tm_ticket` con `tm_usuario`, `tm_categoria`, `tm_subcategoria`, etc., y así poder mostrar información legible (nombres en lugar de IDs) en las tablas de la vista.
    *   `listar_ticketdetalle_x_ticket()`: Obtiene el historial de comentarios de un ticket para mostrarlo en la vista de detalle.
    *   `insert_ticket_detalle()`: Inserta un nuevo comentario en `td_ticketdetalle`. Su lógica es crucial: determina si quien comenta es el usuario o un agente para crear la notificación correspondiente para la otra parte.
    *   `update_ticket()` / `reabrir_ticket()`: Cambian el campo `tick_est` y actualizan las fechas (`fech_cierre`) para gestionar el estado del ticket.
    *   `update_asignacion_y_paso()`: Orquesta el avance en un flujo de trabajo. Actualiza el `usu_asig` y `paso_actual_id` en la tabla `tm_ticket` y, muy importante, inserta un nuevo registro en `th_ticket_asignacion` para dejar constancia del cambio.

### `models/Usuario.php`

*   **Responsabilidad Principal:** Gestiona todo lo relacionado con los usuarios: autenticación, información de perfil, permisos y operaciones CRUD.

*   **Funcionamiento Detallado:**
    *   **Método `login()`:** Es más que una simple consulta.
        1.  Recibe `usu_correo`, `usu_pass` y `rol_id` del formulario.
        2.  Busca al usuario por `usu_correo`.
        3.  Si lo encuentra, verifica que el `rol_id` solicitado en el login coincida con el `rol_id` del usuario en la base de datos. Esto previene que un usuario normal intente entrar por el login de soporte.
        4.  Utiliza `password_verify()` para comparar de forma segura la contraseña proporcionada con el hash almacenado en la base de datos.
        5.  Si todo es correcto, establece las variables de sesión (`$_SESSION`) como `usu_id`, `usu_nom`, `rol_id`, etc., que serán usadas en toda la aplicación para controlar el acceso y personalizar las vistas.
    *   **Métodos `insert_usuario()` y `update_usuario()`:** Utilizan `password_hash()` con el algoritmo `PASSWORD_DEFAULT` antes de insertar o actualizar una contraseña, garantizando que nunca se almacenen contraseñas en texto plano.
    *   **Métodos `get_usuario_x_rol()` y `get_usuarios_por_cargo()`:** Son ejemplos de métodos de consulta que se utilizan para rellenar dinámicamente los elementos `<select>` en la interfaz, por ejemplo, al asignar un ticket o configurar una regla.

### `controller/ticket.php`

*   **Responsabilidad Principal:** Es el cerebro que conecta las acciones del usuario en la vista con la lógica de los modelos. No contiene lógica de base de datos, solo orquesta las llamadas.

*   **Funcionamiento Detallado:**
    *   Su lógica se basa en una gran estructura `switch ($_GET["op"])`. Cada `case` corresponde a una acción específica.
    *   **`case "insert"`:** Recibe los datos de `$_POST` del formulario de nuevo ticket. Llama a los modelos `Flujo` y `FlujoMapeo` para determinar la asignación inicial. Luego, invoca `Ticket::insert_ticket()`. Si se adjuntan archivos (`$_FILES`), itera sobre ellos, los mueve al directorio `/public/document` y llama a `Documento::insert_documento()` para registrar cada uno en la base de datos.
    *   **`case "listar_x_usu"`:** Es invocado por una petición AJAX de DataTables. Llama a `Ticket::listar_ticket_x_usuario()`, procesa el array resultante para añadir botones de acción (`editar`, `eliminar`) y finalmente lo codifica a JSON con el formato que espera DataTables para renderizar la tabla.
    *   **`case "insertdetalle"`:** Recibe el comentario del ticket. Llama a `Ticket::insert_ticket_detalle()`. Si el usuario marcó "avanzar flujo", este `case` también contiene la lógica para llamar a `Ticket::update_asignacion_y_paso()`, actualizando el ticket y notificando al nuevo agente.

### `Event/NotificationServer.php`

*   **Responsabilidad Principal:** Implementa la lógica del servidor de WebSockets, permitiendo la comunicación en tiempo real desde el servidor hacia los clientes (navegadores).

*   **Funcionamiento Detallado:**
    1.  **Inicio:** El script `php server.php` inicia un bucle de eventos y adjunta una instancia de esta clase al servidor Ratchet en el puerto 8080.
    2.  **Conexión (`onOpen`)**: Cuando un usuario carga una página, el script `notificacion.js` establece una conexión WebSocket (ej: `ws://servidor:8080?userId=123`). El método `onOpen` captura esta conexión, extrae el `userId` de la URL y almacena la conexión en un array asociativo (`$this->userConnections`) con el `userId` como clave. Este mapeo es **esencial** para saber a quién enviar cada notificación.
    3.  **Bucle Periódico (`checkNewNotifications`)**: El `EventLoop` de Ratchet está configurado para llamar a este método cada 2 segundos. El método ejecuta una consulta a la base de datos (`Notificacion::get_nuevas_notificaciones_para_enviar()`) buscando notificaciones con estado "pendiente de envío" (`est=2`).
    4.  **Envío**: Por cada notificación encontrada, busca la conexión del destinatario en el array `$this->userConnections`. Si el usuario está conectado, le envía un mensaje JSON con los detalles de la notificación. Inmediatamente después, llama a `Notificacion::update_notificacion_estado()` para marcarla como "enviada" (`est=1`) y no volver a enviarla.
    5.  **Desconexión (`onClose`)**: Si el usuario cierra la pestaña, el servidor detecta la desconexión y este método se encarga de eliminar la conexión del array `$this->userConnections` para no intentar enviar mensajes a conexiones inexistentes.

## 7. Flujos de Trabajo Importantes

Esta sección describe, paso a paso, cómo los diferentes componentes del sistema (Modelos, Vistas, Controladores, etc.) colaboran para realizar tareas clave.

### Flujo 1: Creación de un Nuevo Ticket

Este flujo describe el proceso completo desde que un usuario final crea un ticket hasta que se almacena en el sistema y se notifica al agente correspondiente.

1.  **Vista (Formulario):** El usuario navega a la sección "Nuevo Ticket" (`/view/NuevoTicket/`). El script `nuevoticket.js` se carga y realiza peticiones AJAX a los controladores (`categoria.php`, `prioridad.php`, etc.) para rellenar los campos `<select>` del formulario (Categorías, Prioridades, etc.).

2.  **Interacción del Usuario:** El usuario completa los campos del formulario (título, descripción, etc.) y adjunta archivos si es necesario.

3.  **Vista (JavaScript):** Al hacer clic en "Guardar", el evento es interceptado por `nuevoticket.js`. Este script:
    *   Realiza una validación de los campos en el lado del cliente.
    *   Recopila todos los datos del formulario, incluyendo la descripción del editor Summernote y los archivos adjuntos, en un objeto `FormData`.
    *   Envía una petición `AJAX` de tipo `POST` al controlador `controller/ticket.php?op=insert`.

4.  **Controlador (`ticket.php`):**
    *   El `case "insert"` se activa.
    *   Recibe los datos de `$_POST` y los archivos de `$_FILES`.
    *   Invoca al modelo `FlujoMapeo.php` para determinar si la subcategoría seleccionada tiene una regla de asignación automática.
    *   Llama al método `Ticket::insert_ticket()` pasándole los datos. Este método devuelve el ID del ticket recién creado (`$tick_id`).

5.  **Modelo (`Ticket.php`):**
    *   El método `insert_ticket()` ejecuta varias consultas SQL dentro de una transacción:
        *   Inserta el registro principal en la tabla `tm_ticket`.
        *   Inserta el primer registro en la tabla de historial `th_ticket_asignacion` con el estado "Abierto".
        *   Inserta un registro en la tabla `tm_notificacion` con `est=2` (pendiente de envío) para el agente que fue asignado.

6.  **Controlador (continuación):**
    *   Si el modelo confirma la creación, el controlador procesa los archivos adjuntos.
    *   Para cada archivo en `$_FILES`, lo mueve del directorio temporal al directorio final `/public/document/ticket/`, renombrándolo con el `$tick_id` para evitar colisiones.
    *   Llama al método `Documento::insert_documento()` para guardar la referencia del archivo en la base de datos.
    *   Finalmente, devuelve una respuesta `JSON` al cliente indicando que la operación fue exitosa.

7.  **Vista (Respuesta):** El script `nuevoticket.js` recibe la respuesta exitosa del AJAX, muestra una alerta de confirmación al usuario usando `SweetAlert`, y limpia el formulario.

### Flujo 2: Notificación en Tiempo Real (WebSocket)

Este flujo comienza justo después de que el Flujo 1 termina, y describe cómo se entrega la notificación al agente asignado.

1.  **Prerrequisito (Agente Conectado):** Un agente de soporte tiene la aplicación abierta en su navegador. Al cargar la página, el script `view/notificacion.js` ha establecido una conexión WebSocket con el servidor (`server.php`), enviando su ID de usuario (ej: `ws://ayuda.arpesod.com:8080?userId=123`).

2.  **Servidor (`NotificationServer.php` - `onOpen`):** El servidor Ratchet recibe la conexión, y el método `onOpen` la almacena en un array, asociando el ID del agente (`123`) con su objeto de conexión específico. El agente ahora está "escuchando".

3.  **Disparador (Notificación Pendiente):** Como resultado del Flujo 1, ahora existe una fila en la tabla `tm_notificacion` con `est=2` y `usu_id=123`.

4.  **Servidor (Bucle de Eventos):** El servidor de WebSockets, que corre en un proceso continuo, tiene un temporizador que ejecuta el método `checkNewNotifications()` cada 2 segundos.

5.  **Detección y Envío:**
    *   En su siguiente ejecución, `checkNewNotifications()` consulta la base de datos y encuentra la notificación pendiente para el usuario `123`.
    *   Busca en su array de conexiones si el usuario `123` está conectado. Lo encuentra.
    *   Construye un mensaje `JSON` con los datos de la notificación.
    *   Envía el mensaje `JSON` a través de la conexión WebSocket específica de ese agente.
    *   Inmediatamente después, actualiza el estado de la notificación en la base de datos a `est=1` (enviada) para no volver a procesarla.

6.  **Cliente (`notificacion.js` - `onmessage`):**
    *   El navegador del agente recibe el mensaje `JSON` a través del WebSocket.
    *   El evento `onmessage` en `notificacion.js` se dispara.
    *   El script parsea el `JSON` y utiliza la librería `$.notify` para mostrar una alerta emergente en la pantalla del agente.
    *   Adicionalmente, llama a funciones como `actualizarContadorDeNotificaciones()` que hacen una petición AJAX para refrescar el contador de notificaciones en la barra de navegación.

## 8. Despliegue

El proyecto está preparado para ser desplegado en un entorno de hosting que utilice **cPanel** y su funcionalidad de "Git Version Control", aunque los principios se pueden aplicar a otros entornos.

### Proceso de Despliegue Automatizado (cPanel)

La presencia del archivo `.cpanel.yml` en la raíz del proyecto indica que se utiliza el sistema de despliegue de cPanel.

*   **Funcionamiento:** Este archivo contiene una serie de comandos que cPanel ejecuta automáticamente cada vez que se realiza un `push` a una rama específica del repositorio (generalmente `main` o `master`).
*   **Tareas Típicas:** Un archivo `.cpanel.yml` para este proyecto probablemente contenga tareas como:
    ```yaml
    ---
    deployment:
      tasks:
        - export DEPLOYPATH=/home/usuario_cpanel/public_html/
        - /bin/cp -R * $DEPLOYPATH
    ```
    Esto simplemente copia todos los archivos del repositorio al directorio público del servidor.

### Pasos y Consideraciones Manuales en Producción

Aunque la copia de archivos puede ser automática, hay pasos manuales cruciales que se deben realizar en el servidor, especialmente en la configuración inicial.

1.  **Configuración del `.env` de Producción:**
    *   En el servidor de producción, se debe crear un archivo `.env` manualmente en la raíz del proyecto. **Este archivo NUNCA debe subirse al repositorio Git**.
    *   Debe contener las credenciales de la base de datos de producción, la `APP_URL` del dominio real y cualquier otra configuración específica del entorno.

2.  **Dependencias de Composer:**
    *   Tras el primer despliegue (o cada vez que se actualice `composer.json`), es necesario ejecutar `composer install` en el servidor, usualmente a través de una terminal SSH.
    *   Para un entorno de producción, se recomienda usar flags de optimización:
        ```bash
        composer install --no-dev -o
        ```
        Esto omite las dependencias de desarrollo y optimiza el autoloader para un mejor rendimiento.

3.  **Permisos de Archivos:**
    *   Es fundamental asegurarse de que el servidor web (Apache) tenga permisos de escritura sobre los directorios donde los usuarios subirán archivos, por ejemplo:
        *   `/public/document/ticket`
        *   `/public/document/detalle`

4.  **Servidor de WebSockets en Producción:**
    *   El script `server.php` debe estar ejecutándose **constantemente** en el servidor para que las notificaciones en tiempo real funcionen.
    *   Ejecutar `php server.php` directamente en la terminal no es suficiente, ya que el proceso se detendrá al cerrar la sesión SSH.
    *   **Se recomienda encarecidamente** utilizar un gestor de procesos como **`supervisor`** o `systemd`. Estos gestores aseguran que el script se reinicie automáticamente si falla y que se inicie junto con el servidor.
    *   Como alternativa más simple (aunque menos robusta), se puede usar `nohup` para que el proceso continúe ejecutándose en segundo plano:
        ```bash
        nohup php server.php > /dev/null 2>&1 &
        ```
