<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## 📋 Descripción

MediFlow es una aplicación web SaaS desarrollada con Laravel 12 y PHP 8.3+ que permite a profesionales de la salud independientes gestionar sus consultorios de forma eficiente y segura.

## Features

### Gestión de Pacientes
- CRUD completo de pacientes
- Búsqueda avanzada
- Historial clínico digital
- Datos encriptados para privacidad

### Expedientes Clínicos
- Registros médicos con encriptación automática
- Múltiples tipos de registros (consulta, diagnóstico, procedimiento)
- Signos vitales y métricas
- Generación de recetas en PDF

### Sistema de Citas
- Agenda inteligente con prevención de conflictos
- Validación de horarios de atención
- Estados de citas (pendiente, confirmada, completada, cancelada)
- Notificaciones automáticas por email

### Multi-tenancy
- Aislamiento completo de datos por clínica
- Filtrado automático a nivel de base de datos
- Seguridad reforzada con Policies

### Seguridad y Auditoría
- Encriptación de datos médicos sensibles
- Registro de auditoría de todas las acciones
- Control de acceso basado en roles (Admin/Asistente)
- Prevención de race conditions con locks pesimistas

### Notificaciones
- Confirmación de citas por email
- Recordatorios automáticos 24h antes
- Sistema de colas para procesamiento asíncrono

## 🛠️ Stack Tecnológico

- **Backend**: PHP 8.3+, Laravel 12
- **Frontend**: Laravel Livewire 3, Tailwind CSS
- **Base de Datos**: PostgreSQL
- **Queue**: Database driver
- **PDF**: DomPDF
- **Testing**: Pest PHP
- **Quality**: PHPStan (nivel 8), Laravel Pint

## 📦 Instalación

### Requisitos Previos

- Docker y Docker Compose
- Git

### Pasos de Instalación

```bash
# Clonar el repositorio
git clone https://github.com/OsOsorioP/mediflow.git
cd mediflow

# Copiar el archivo de configuración
cp .env.example .env

# Instalar dependencias con Sail
./vendor/bin/sail up -d
./vendor/bin/sail composer install
./vendor/bin/sail npm install

# Generar key de aplicación
./vendor/bin/sail artisan key:generate

# Ejecutar migraciones y seeders
./vendor/bin/sail artisan migrate --seed

# Compilar assets
./vendor/bin/sail npm run dev
```

### Iniciar Queue Worker

En una terminal separada:

```bash
./vendor/bin/sail artisan queue:work
```

### Iniciar Scheduler (opcional, para recordatorios)

```bash
./vendor/bin/sail artisan schedule:work
```

## 👤 Acceso al Sistema

Después de ejecutar los seeders, puedes acceder con:

- **Email**: admin@drperez.com
- **Password**: password
- **Rol**: Administrador

O:

- **Email**: maria@drperez.com
- **Password**: password
- **Rol**: Asistente

## 🧪 Testing

```bash
# Ejecutar todos los tests
./vendor/bin/sail pest

# Con coverage
./vendor/bin/sail pest --coverage

# Tests específicos
./vendor/bin/sail pest tests/Feature/AppointmentTest.php
```

## 📊 Análisis Estático

```bash
# PHPStan
./vendor/bin/sail vendor/bin/phpstan analyse

# Laravel Pint (formateo)
./vendor/bin/sail vendor/bin/pint
```

## 📧 Testing de Emails

Los emails se pueden ver en Mailpit:
- URL: http://localhost:8025

Enviar email de prueba:

```bash
./vendor/bin/sail artisan email:test confirmation
./vendor/bin/sail artisan email:test reminder
```

## 🏗️ Arquitectura

### Patrones Implementados

- **Actions**: Lógica de negocio encapsulada
- **DTOs**: Transferencia de datos tipados
- **Policies**: Autorización granular
- **Events/Listeners**: Desacoplamiento de lógica
- **Jobs**: Procesamiento asíncrono
- **Traits**: Reutilización de comportamiento (MultiTenant, Auditable)

### Capas de la Aplicación

```
┌─────────────────────────────────────┐
│         Presentation Layer          │
│    (Livewire Components + Views)    │
├─────────────────────────────────────┤
│         Application Layer           │
│    (Controllers, Actions, DTOs)     │
├─────────────────────────────────────┤
│          Domain Layer               │
│  (Models, Enums, Business Logic)    │
├─────────────────────────────────────┤
│      Infrastructure Layer           │
│ (Database, Queue, Mail, Storage)    │
└─────────────────────────────────────┘
```

## 🔐 Seguridad

### Datos Encriptados

Los siguientes campos se encriptan automáticamente:
- Síntomas
- Diagnósticos
- Plan de tratamiento
- Prescripciones
- Notas clínicas

### Auditoría

Todas las acciones se registran en `audit_logs`:
- Creación, actualización, eliminación
- Visualizaciones de expedientes médicos
- IP, user agent, timestamp

## 🚀 Deployment (Producción)

### Checklist Pre-Deploy

- [ ] Configurar `APP_ENV=production`
- [ ] Configurar `APP_DEBUG=false`
- [ ] Generar nueva `APP_KEY`
- [ ] Configurar base de datos de producción
- [ ] Configurar email (SES, Mailgun, etc.)
- [ ] Configurar queue worker (Supervisor)
- [ ] Configurar scheduler (cron)
- [ ] Configurar SSL/HTTPS
- [ ] Optimizar autoload: `composer install --optimize-autoloader --no-dev`
- [ ] Cachear configuración: `php artisan config:cache`
- [ ] Cachear rutas: `php artisan route:cache`
- [ ] Cachear vistas: `php artisan view:cache`

### Supervisor (Queue Worker)

```ini
[program:mediflow-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/mediflow/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/mediflow/storage/logs/worker.log
stopwaitsecs=3600
```

### Cron (Scheduler)

```bash
* * * * * cd /path/to/mediflow && php artisan schedule:run >> /dev/null 2>&1
```

## 📝 Licencia

Este proyecto es de código abierto bajo licencia MIT.

## 👨‍💻 Autor

Desarrollado como proyecto educativo para demostrar buenas prácticas en Laravel.

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Por favor, abre un issue primero para discutir cambios mayores.

## 📞 Soporte

Para reportar bugs o solicitar features, abre un issue en GitHub.

Solucion layout

php artisan livewire:publish --config

/*
|---------------------------------------------------------------------------
| Layout View
|---------------------------------------------------------------------------
|
| This property specifies the default layout view that will be used
| when rendering a full-page component.
|
*/

// Cambia esto:
// 'layout' => 'components.layouts.app',

// Por esto:
'layout' => 'layouts.app',

php artisan config:clear


Para ejecutar scheduler en desarrollo:

```bash
./vendor/bin/sail artisan schedule:work
```

Para iniciar queue worker:

```bash
./vendor/bin/sail artisan queue:work
```

o para desarrollo:

```bash
./vendor/bin/sail artisan queue:listen
```