<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Property;
use App\Services\Logger;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;

class UserController extends Controller
{
    protected Logger $logger;

    public function __construct()
    {
        $this->logger = Logger::getInstance();
    }
    // ------------------- CRUD Methods -------------------

    /**
     * Display a list of users.
     */
    public function index()
    {
        try {
            $users = User::all();
            return view('users.index', compact('users'));
        } catch (\Throwable $e) {
            return $this->errorResponse('Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        try {
            return view('users.create');
        } catch (\Throwable $e) {
            return $this->errorResponse('Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(StoreUserRequest $request)
    {
        try {
            $data = $request->validated();
            $user = $this->createUser($data);

            $this->logger->info('New user created', [
                'user_id' => $user->id,
                'email' => $user->email,
                'created_by' => auth()->id(),
            ]);

            return $this->successResponse('User created successfully', $user);

        } catch (\Throwable $e) {
            $this->logger->error('User creation failed', [
                'email' => $request->email,
                'error' => $e->getMessage(),
            ]);
            return $this->errorResponse('Failed to create user. Please try again.');
        }
    }

    /**
     * Search for users.
     */
    public function search(Request $request)
    {
        $query = $request->input('query');

        $users = User::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->get();

        $this->logger->info('User search performed', [
            'user_id' => auth()->id(),
            'search_query' => $query,
        ]);

        return $request->ajax()
            ? response()->json($users)
            : view('users-management', ['users' => $users]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            $data = $request->validated();

            if (!empty($data['password'])) {
                $data['password'] = Hash::make($this->hmacPassword($data['password']));
            } else {
                unset($data['password']);
            }

            $user->update($data);

            $this->logger->info('User updated', [
                'user_id' => $user->id,
                'updated_by' => auth()->id(),
            ]);

            return $this->successResponse('User updated successfully', $user);

        } catch (\Throwable $e) {
            $this->logger->error('User update failed', [
                'user_id' => $user->id,
                'updated_by' => auth()->id(),
                'error' => $e->getMessage(),
            ]);
            return $this->errorResponse('Failed to update user.');
        }
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        try {
            $user->delete();

            $this->logger->info('User deleted', [
                'user_id' => $user->id,
                'deleted_by' => auth()->id(),
            ]);

            return $this->successResponse('User deleted successfully');

        } catch (\Throwable $e) {
            $this->logger->error('User deletion failed', [
                'user_id' => $user->id,
                'deleted_by' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse('Failed to delete user.');
        }
    }

    /**
     * Display the users management page.
     */
    public function usersManagement()
    {
        try {
            $users = User::all();
            $properties = Property::all();

            $this->logger->info('Accessed users management page', [
                'accessed_by' => auth()->id(),
                'users_count' => $users->count(),
                'properties_count' => $properties->count(),
            ]);

            return view('users.users-management', compact('users', 'properties'));

        } catch (\Throwable $e) {
            $this->logger->error('Failed to load users management page', [
                'accessed_by' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse('Unable to load users management page.');
        }
    }

    // ------------------- Helper Methods -------------------

    /**
     * HMAC a password before storing.
     */
    private function hmacPassword(string $password): string
    {
        $secretKey = env('PASSWORD_HMAC_KEY');
        return hash_hmac('sha256', $password, $secretKey);
    }

    /**
     * Create a new user with hashed password.
     */
    private function createUser(array $data): User
    {
        $data['password'] = Hash::make($this->hmacPassword($data['password']));
        return User::create($data);
    }

    /**
     * Return success response for AJAX or normal requests.
     */
    private function successResponse(string $message, $data = null)
    {
        return request()->ajax()
            ? response()->json(['success' => true, 'data' => $data])
            : back()->with('success', $message);
    }

    /**
     * Return error response for AJAX or normal requests.
     */
    private function errorResponse(string $message, int $code = 500)
    {
        return request()->ajax()
            ? response()->json(['success' => false, 'message' => $message], $code)
            : back()->with('error', $message);
    }
}
