<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ActivarCentroMail;
use App\Persistencia\CentroRepository;
use App\Persistencia\ConfiguracionRepository;
use App\Persistencia\UserRepository;
use App\Services\MailConfigService;
use App\Validaciones\RegisterCentroValidacion;
use App\Validaciones\LoginValidacion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    private UserRepository $userRepo;
    private CentroRepository $centroRepo;
    private MailConfigService $mailConfig;
    private ConfiguracionRepository $configRepo;

    public function __construct(
        UserRepository $userRepo,
        CentroRepository $centroRepo,
        MailConfigService $mailConfig,
        ConfiguracionRepository $configRepo,
    ) {
        $this->userRepo = $userRepo;
        $this->centroRepo = $centroRepo;
        $this->mailConfig = $mailConfig;
        $this->configRepo = $configRepo;
    }

    /**
     * POST /api/auth/register
     */
    public function register(RegisterCentroValidacion $request): JsonResponse
    {
        // Crear centro
        $centro = $this->centroRepo->crear([
            'nombre' => $request->nombre_centro,
            'tipo' => $request->tipo_centro,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
        ]);

        // Crear usuario gestor
        $user = $this->userRepo->crearGestor(
            centroId: $centro->id,
            nombre: $request->nombre,
            email: $request->email,
            password: $request->password,
            cargo: $request->cargo,
        );

        // Enviar email de activación usando configuración dinámica de Mailjet
        try {
            $mailer = $this->configRepo->obtenerValor('MAIL_MAILER', 'log');

            if ($mailer === 'brevo') {
                // Usar API REST de Brevo directamente
                $brevoService = app(\App\Services\BrevoMailService::class);
                $urlActivacion = url('/activar/' . $user->token_activacion);
                $brevoService->enviarConVista(
                    $user->email,
                    '[Juntos de Nuevo] Activa tu cuenta - ' . $centro->nombre,
                    'emails.activar-centro',
                    [
                        'nombreCentro' => $centro->nombre,
                        'nombreUsuario' => $user->nombre,
                        'tokenActivacion' => $user->token_activacion,
                        'urlActivacion' => $urlActivacion,
                    ]
                );
            } else {
                $this->mailConfig->aplicarConfiguracion();
                Mail::to($user->email)->send(
                    new ActivarCentroMail($centro->nombre, $user->nombre, $user->token_activacion)
                );
            }
        } catch (\Exception $e) {
            // Log del error pero no bloquear el registro
            \Log::error('Error enviando email de activación: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Centro registrado exitosamente. Por favor confirma tu email para activar la cuenta.',
            'data' => [
                'centro_id' => $centro->id,
                'user_id' => $user->id,
                // En producción NO devolver el token, se envía por email
                'token_activacion' => $user->token_activacion,
            ],
        ], 201);
    }

    /**
     * POST /api/auth/activate/{token}
     */
    public function activate(string $token): JsonResponse
    {
        $activado = $this->userRepo->activarPorToken($token);

        if (!$activado) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'INVALID_TOKEN',
                    'message' => 'Token de activación inválido',
                ],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Cuenta activada exitosamente. Ya puedes iniciar sesión.',
        ]);
    }

    /**
     * POST /api/auth/login
     */
    public function login(LoginValidacion $request): JsonResponse
    {
        $user = $this->userRepo->verificarCredenciales($request->email, $request->password);

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'INVALID_CREDENTIALS',
                    'message' => 'Credenciales inválidas',
                ],
            ], 401);
        }

        if (!$user->activo) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'ACCOUNT_INACTIVE',
                    'message' => 'Tu cuenta no está activa. Verifica tu email o contacta al administrador.',
                ],
            ], 403);
        }

        // Crear token Sanctum
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login exitoso',
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'nombre' => $user->nombre,
                    'email' => $user->email,
                    'rol' => $user->rol,
                    'cargo' => $user->cargo,
                    'centro_id' => $user->centro_id,
                    'centro_nombre' => $user->centro?->nombre,
                ],
            ],
        ]);
    }

    /**
     * POST /api/auth/logout
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada exitosamente',
        ]);
    }

    /**
     * GET /api/auth/me
     */
    public function me(Request $request): JsonResponse
    {
        $user = $this->userRepo->buscarPorId($request->user()->id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'nombre' => $user->nombre,
                'email' => $user->email,
                'rol' => $user->rol,
                'cargo' => $user->cargo,
                'centro_id' => $user->centro_id,
                'centro' => $user->centro ? [
                    'id' => $user->centro->id,
                    'nombre' => $user->centro->nombre,
                    'tipo' => $user->centro->tipo,
                ] : null,
            ],
        ]);
    }
}
