<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use Laravel\Fortify\Rules\Password;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;


class UserController extends Controller
{
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name'     => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255', 'unique:users'],
                'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'phone'    => ['nullable', 'string', 'max:255'],
                'password' => ['required', 'string', new Password],
            ]);
            User::create([
                'name'     => $request->name,
                'username' => $request->username,
                'email'    => $request->email,
                'phone'    => $request->phone,
                'roles'    => 'USER',
                'password' => Hash::make($request->password),
            ]);

            $user = User::where('email', $request->email)->first();
            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success(
                [
                    'access_token' => $tokenResult,
                    'token_type'   => 'Bearer',
                    'user'         => $user
                ],
                'User Registered'
            );
        } catch (Exception $error) {
            return ResponseFormatter::error(
                [
                    'message' => 'Something went wrong',
                    'error'   => $error
                ],
                'Authentication Failed',
                500
            );
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'email|required',
                'password' => 'required'
            ]);
            $credentials = request(['email', 'password']);
            if (!Auth::attempt($credentials)) {
                return ResponseFormatter::error(
                    [
                        'message' => 'Unauthorized'
                    ],
                    'Authentication Failed',
                    500
                );
            } else {
                $user = User::where('email', $request->email)->first();
                if (!Hash::check($request->password, $user->password, [])) {
                    throw new \Exception('Invalid Crendentials');
                } else {
                    $tokenResult = $user->createToken('authToken')->plainTextToken;
                    return ResponseFormatter::success(
                        [
                            'access_token' => $tokenResult,
                            'token_type'   => 'Bearer',
                            'user'         => $user
                        ],
                        'Authenticated'
                    );
                }
            }
        } catch (Exception $error) {
            return ResponseFormatter::error(
                [
                    'message' => 'Something went wrong',
                    'error'   => $error
                ],
                'Authentication Failed',
                500
            );
        }
    }

    public function try(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return ResponseFormatter::error(null, 'periksa kembali email anda', 500);
        } else {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken($request->email)->plainTextToken;
                return ResponseFormatter::success([
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'password' => $user->password,
                    'user' => $user
                ], 'Authenticated');
            } else {
                return ResponseFormatter::error($user, 'Password Wrong', 505);
            }
        }
    }

    public function create(Request $request)
    {
        $user = new User();
        $user->name     = $request->input('name');
        $user->email    = $request->input('email');
        $user->username = $request->input('username');
        $user->phone    = $request->input('phone');
        $user->roles    = $request->input('roles');
        $user->password = Hash::make($request->input('password'));
        $user->save();
        return ResponseFormatter::success($user, 'Data Saved');
    }

    public function all()
    {
        $users = DB::table('users')->get();
        return response()->json($users);
    }

    public function find(Request $request)
    {
        $user = DB::table('users')->find($request->id);
        if ($user) {
            return ResponseFormatter::success($user, 'Successfull');
        } else {
            return ResponseFormatter::error(null, 'Data Empty', 404);
        }
    }

    public function editID(Request $request)
    {
        $user = DB::table('users')->where('id', $request->id)->update(['name' => $request->name]);
        return ResponseFormatter::success($user, 'Updated Success');
    }

    public function tryRegister(Request $request)
    {
        $email = DB::table('users')->where('email', $request->email)->first();
        if ($email) {
            return ResponseFormatter::error(null, 'Email telah digunakan', 505);
        } else {
            return ResponseFormatter::success($request, 'Email belum digunakan');
        }
    }

    public function fetch(Request $request)
    {
        return ResponseFormatter::success($request->user(), 'Data Profile User berhasil diambil');
    }

    public function updateProfile(Request $request)
    {
        $data          = $request->all(); // take all input
        $user          = Auth::user(); // session login
        $checkEmail    = filter_var($request->email, FILTER_VALIDATE_EMAIL);
        $userID        = DB::table('users')->where('id', $user['id'])->first(); // data menjadi array cara akses $userID->email;
        $emailInDB     = DB::table('users')->where('email', $checkEmail)->first();
        $checkUsername = DB::table('users')->where('username', $request->username)->first();
        if ($userID) {
            if ($checkEmail) {
                if ($checkEmail == $userID->email) {
                    if ($request->username == $userID->username) {
                        DB::table('users')->where('id', $user['id'])->update([
                            'name' => $request->name,
                            'phone' => $request->phone
                        ]);
                        return ResponseFormatter::success($userID, 'Data Updated', 200);
                    } else {
                        if ($checkUsername) {
                            return ResponseFormatter::error(null, 'Username Sudah Digunakan');
                        } else {
                            DB::table('users')->where('id', $user['id'])->update([
                                'name'     => $request->name,
                                'username' => $request->username,
                                'phone'    => $request->phone
                            ]);
                            return ResponseFormatter::success($userID, 'Data Updated', 200);
                        }
                    }
                } else {
                    if ($emailInDB) {
                        return ResponseFormatter::error(null, 'Email Sudah Digunakan');
                    } else {
                        if ($request->username == $userID->username) {
                            DB::table('users')->where('id', $user['id'])->update([
                                'name'     => $request->name,
                                'email'    => $checkEmail,
                                'phone'    => $request->phone
                            ]);
                            return ResponseFormatter::success($userID, 'Data Updated', 200);
                        } else {
                            if ($checkUsername) {
                                return ResponseFormatter::error(null, 'Username Sudah Digunakan');
                            } else {
                                DB::table('users')->where('id', $user['id'])->update([
                                    'name'     => $request->name,
                                    'email'    => $checkEmail,
                                    'username' => $request->username,
                                    'phone'    => $request->phone
                                ]);
                                return ResponseFormatter::success($userID, 'Data Updated', 200);
                            }
                        }
                    }
                }
            } else {
                return ResponseFormatter::error(null, 'Email Tidak valid');
            }
        }
    }

    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken()->delete();
        return ResponseFormatter::success($token, 'Token Revoke');
    }

    public function newPass(Request $request)
    {
        $user    = Auth::user();
        $userID  = DB::table('users')->where('id', $user['id'])->first();
        $oldPass = Hash::make($request->passwordlama);
        $newPass = $request->newpass;
        $confirm = $request->confirmpass;
        if (Hash::check($request->passwordlama, $userID->password)) {
            if ($confirm == $newPass) {
                DB::table('users')->where('id', $user['id'])->update([
                    'password' => Hash::make($newPass),
                ]);
                return ResponseFormatter::success($userID, 'Password has Updated', 200);
                return 'konfirmasi cocok';
            } else {
                return ResponseFormatter::error(null, 'Konfirmasi Password Salah');
            }
        } else {
            return ResponseFormatter::error(null, 'Password anda salah');
        }
    }
}
