# Help Desk ARPESOD

Help Desk ARPESOD es un sistema de mesa de ayuda y gestión de tickets basado en la web, diseñado para facilitar la comunicación entre usuarios y equipos de soporte. La plataforma permite la creación, asignación y seguimiento de tickets a través de flujos de trabajo definidos, con un sistema de notificaciones en tiempo real y un dashboard de reportes para el análisis de métricas clave.

## Características Principales

-   **Gestión de Tickets:** Creación, asignación, actualización, cierre y reapertura de tickets.
-   **Flujos de Trabajo (Workflows):** Sistema de flujos de trabajo basado en pasos, con asignaciones por cargo y aprobaciones de jefes.
-   **Roles de Usuario:** Múltiples roles (Usuario, Soporte, Administrador) con permisos diferenciados para el acceso a las funcionalidades.
-   **Notificaciones en Tiempo Real:** Utiliza WebSockets (Ratchet) para notificar a los usuarios sobre eventos importantes (nuevos tickets, asignaciones, etc.).
-   **Dashboard de Reportes:** Visualización de KPIs, gráficos y tablas sobre la carga de trabajo de los agentes, rendimiento, categorías de tickets y más.
-   **Gestión de Entidades:** Módulos para administrar (CRUD) Usuarios, Cargos, Departamentos, Empresas, Categorías, Prioridades, etc.
-   **Carga Masiva de Datos:** Funcionalidad para importar datos masivamente desde archivos Excel.
-   **Comunicación por Email:** Envío de notificaciones por correo electrónico (PHPMailer) en diferentes etapas del ciclo de vida de un ticket.

## Tecnologías Utilizadas

### Backend
-   **PHP 8.1+**
-   **MySQL:** Base de datos relacional.
-   **Composer:** Gestor de dependencias.
-   **Ratchet:** Biblioteca para WebSockets en PHP.
-   **PHPMailer:** Biblioteca para el envío de correos electrónicos.
-   **PhpSpreadsheet:** Para la lectura de archivos Excel.
-   **vlucas/phpdotenv:** Para la gestión de variables de entorno.

### Frontend
-   **HTML5 & CSS3**
-   **JavaScript (ES6+)**
-   **jQuery:** Manipulación del DOM y AJAX.
-   **Bootstrap:** Framework para el diseño de la interfaz de usuario.
-   **DataTables:** Para la creación de tablas interactivas.
-   **SweetAlert:** Para alertas y notificaciones personalizadas.
-   **Summernote:** Editor de texto WYSIWYG.
-   **FullCalendar:** Para la visualización de eventos en un calendario.
-   **Chart.js:** Para la creación de gráficos en el dashboard.

## Estructura del Proyecto

El proyecto sigue una arquitectura similar al patrón Modelo-Vista-Controlador (MVC):

-   `/models`: Contiene las clases que interactúan con la base de datos y encapsulan la lógica de negocio.
-   `/view`: Contiene los archivos de presentación (HTML, CSS, JS del lado del cliente) que componen la interfaz de usuario.
-   `/controller`: Actúa como intermediario entre los modelos y las vistas, manejando las peticiones del usuario.
-   `/config`: Almacena los archivos de configuración, como la conexión a la base de datos (`conexion.php`).
-   `/public`: Directorio para los assets públicos (CSS, JS, imágenes, documentos).
-   `/vendor`: Contiene las dependencias gestionadas por Composer.
-   `/cargues`: Scripts especializados para la carga masiva de datos.
-   `/docs`: Documentación y backups de la base de datos.
