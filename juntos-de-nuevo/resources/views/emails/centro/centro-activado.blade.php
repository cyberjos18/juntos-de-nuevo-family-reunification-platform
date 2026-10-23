<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centro activado - Juntos de nuevo</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .header { background: #2563eb; color: white; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .header p { margin: 8px 0 0; opacity: 0.9; font-size: 14px; }
        .content { padding: 30px; }
        .content h2 { color: #1f2937; margin-top: 0; }
        .content p { color: #4b5563; line-height: 1.6; }
        .info-box { background: #f0f9ff; border-left: 4px solid #2563eb; padding: 16px; margin: 20px 0; border-radius: 0 4px 4px 0; }
        .info-box strong { color: #1e40af; }
        .success-box { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 16px; margin: 20px 0; }
        .success-box p { color: #166534; margin: 0; }
        .btn { display: inline-block; background: #2563eb; color: #ffffff; padding: 14px 32px; border-radius: 6px; text-decoration: none; font-weight: 600; margin: 20px 0; }
        .btn:hover { background: #1d4ed8; }
        .btn-container { text-align: center; margin: 30px 0; }
        .url-text { background: #f3f4f6; padding: 12px; border-radius: 4px; word-break: break-all; font-size: 13px; color: #6b7280; margin: 10px 0; }
        .footer { background: #f9fafb; padding: 20px; text-align: center; color: #9ca3af; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏠 Juntos de nuevo</h1>
            <p>Plataforma de Reunificación Familiar</p>
        </div>

        <div class="content">
            <h2>¡Tu centro ha sido activado, {{ $nombreUsuario }}!</h2>

            <div class="success-box">
                <p>✅ El centro <strong>"{{ $nombreCentro }}"</strong> ha sido aprobado y ya está activo en la plataforma Juntos de nuevo.</p>
            </div>

            <p>Ahora puedes:</p>
            <ul style="color: #4b5563; line-height: 1.8;">
                <li>Registrar niños encontrados</li>
                <li>Gestionar casos de reunificación familiar</li>
                <li>Coordinar con otros centros de la red</li>
                <li>Acceder a todas las herramientas de la plataforma</li>
            </ul>

            <p>Inicia sesión con tu cuenta para comenzar a usar la plataforma:</p>

            <div class="btn-container">
                <a href="{{ $urlLogin }}" style="display:inline-block; background:#2563eb; color:#ffffff; padding:14px 32px; border-radius:6px; text-decoration:none; font-weight:600; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">Iniciar sesión</a>
            </div>

            <div class="info-box">
                <p><strong>¿El botón no funciona?</strong> Copia y pega este enlace en tu navegador:</p>
                <div class="url-text">{{ $urlLogin }}</div>
            </div>

            <p>Gracias por formar parte de la red de centros que ayudan a reunir familias.</p>
        </div>

        <div class="footer">
            <p>Juntos de nuevo — Plataforma humanitaria para reunificación familiar</p>
            <p>Venezuela, {{ date('Y') }}</p>
        </div>
    </div>
</body>
</html>
