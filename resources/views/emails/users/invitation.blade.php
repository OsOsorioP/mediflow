<style>
        /* Estilos inline para compatibilidad con clientes de email */
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .email-header {
            background-color: #0ea5e9;
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
        }
        .email-body {
            padding: 30px;
        }
        .credentials-box {
            background-color: #f8f9fa;
            border-left: 4px solid #0ea5e9;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .credentials-box p {
            margin: 10px 0;
        }
        .credentials-box strong {
            color: #0ea5e9;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #0ea5e9;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .button:hover {
            background-color: #0284c7;
        }
        .warning-box {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
        }
    </style>

<div class="email-container">
        <!-- HEADER -->
        <div class="email-header">
            <h1>¡Bienvenido a {{ $clinicName }}!</h1>
        </div>

        <!-- BODY -->
        <div class="email-body">
            <p>Hola <strong>{{ $userName }}</strong>,</p>

            <p>
                Has sido invitado a unirte a <strong>{{ $clinicName }}</strong> en MediFlow 
                como <strong>{{ $roleName }}</strong>.
            </p>

            <p>
                Para comenzar a usar el sistema, utiliza las siguientes credenciales:
            </p>

            <!-- CREDENCIALES -->
            <div class="credentials-box">
                <p><strong>Email:</strong> {{ $userEmail }}</p>
                <p><strong>Contraseña temporal:</strong> <code>{{ $temporaryPassword }}</code></p>
            </div>

            <!-- ADVERTENCIA DE SEGURIDAD -->
            <div class="warning-box">
                <p style="margin: 0;">
                    <strong>⚠️ Importante:</strong> Por tu seguridad, te recomendamos cambiar 
                    esta contraseña temporal después de iniciar sesión por primera vez.
                </p>
            </div>

            <!-- BOTÓN DE ACCIÓN -->
            <div style="text-align: center;">
                <a href="{{ $loginUrl }}" class="button">
                    Iniciar Sesión Ahora
                </a>
            </div>

            <p style="margin-top: 30px;">
                Si tienes alguna pregunta o necesitas ayuda, no dudes en contactar al 
                administrador de tu clínica.
            </p>

            <p>
                ¡Bienvenido al equipo!<br>
                <strong>El equipo de MediFlow</strong>
            </p>
        </div>

        <!-- FOOTER -->
        <div class="email-footer">
            <p>
                Este es un mensaje automático de MediFlow. Por favor, no respondas a este email.
            </p>
            <p>
                © {{ date('Y') }} MediFlow. Todos los derechos reservados.
            </p>
        </div>
    </div>