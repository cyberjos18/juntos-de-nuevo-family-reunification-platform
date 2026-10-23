<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo centro pendiente - Juntos de nuevo</title>
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
        .info-row { display: flex; padding: 8px 0; border-bottom: 1px solid #e5e7eb; }
        .info-row:last-child { border-bottom: none; }
        .info-label { font-weight: 600; color: #374151; min-width: 140px; }
        .info-value { color: #4b5563; }
        .btn { display: inline-block; background: #2563eb; color: #ffffff; padding: 14px 32px; border-radius: 6px; text-decoration: none; font-weight: 600; margin: 20px 0; }
        .btn:hover { background: #1d4ed8; }
        .btn-container { text-align: center; margin: 30px 0; }
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
            <h2>Nuevo centro pendiente de aprobación</h2>

            <p>Hola <strong>{{ $nombreAdmin }}</strong>,</p>

            <p>Se ha registrado un nuevo centro en la plataforma que requiere tu revisión y aprobación:</p>

            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">Centro:</span>
                    <span class="info-value"><strong>{{ $nombreCentro }}</strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tipo:</span>
                    <span class="info-value">{{ $tipoCentro }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email responsable:</span>
                    <span class="info-value">{{ $emailResponsable }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Teléfono:</span>
                    <span class="info-value">{{ $telefonoCentro }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Dirección:</span>
                    <span class="info-value">{{ $direccionCentro }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Fecha de registro:</span>
                    <span class="info-value">{{ $fechaRegistro }}</span>
                </div>
            </div>

            <div class="btn-container">
                <a href="{{ $urlPendientes }}" style="display:inline-block; background:#2563eb; color:#ffffff; padding:14px 32px; border-radius:6px; text-decoration:none; font-weight:600; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">Ver centros pendientes</a>
            </div>

            <p>Recuerda que debes validar la información del centro antes de activarlo para garantizar la seguridad de la plataforma.</p>
        </div>

        <div class="footer">
            <p>Juntos de nuevo — Plataforma humanitaria para reunificación familiar</p>
            <p>Venezuela, {{ date('Y') }}</p>
        </div>
    </div>
</body>
</html>
