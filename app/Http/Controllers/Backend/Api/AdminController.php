<?php

namespace App\Http\Controllers\Backend\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="ISPERP Multi-Tenant API",
 *      description="API Documentation for Admin & Multi-Tenant",
 *      @OA\Contact(
 *          email="support@isperp.xyz"
 *      )
 * )
 *
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="API Server"
 * )
 */
class AdminController extends Controller
{
    /**
     * @OA\Post(
     *     path="https://dg.isperp.xyz/api/v1/auth/login",
     *     summary="Admin Login",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"login","password"},
     *             @OA\Property(property="login", type="string", example="admin@gmail.com"),
     *             @OA\Property(property="password", type="string", example="123456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login success",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="admin", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Admin Name"),
     *                 @OA\Property(property="email", type="string", example="admin@domain.com"),
     *                 @OA\Property(property="user_type", type="integer", example=1),
     *                 @OA\Property(property="pop_id", type="integer", example=0)
     *             ),
     *             @OA\Property(property="token", type="string", example="1|abc123xyz")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid credentials")
     *         )
     *     )
     * )
     */
    public function api_login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $login = $request->input('login');
        $password = $request->input('password');

        $admin = Admin::where('email', $login)
                      ->orWhere('username', $login)
                      ->first();

        if (! $admin || ! Hash::check($password, $admin->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $admin->createToken('admin-api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'admin' => [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
                'user_type' => $admin->user_type,
                'pop_id' => $admin->pop_id,
            ],
            'token' => $token
        ]);
    }

    /**
     * @OA\Post(
     *     path="https://isperp.xyz/api/v1/admin/logout",
     *     summary="Admin Logout",
     *     tags={"Authentication"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout success",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logout successful")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function api_logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful',
        ]);
    }
}
