<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'remove_avatar' => 'sometimes|boolean',
        ]);

        // Обработка удаления аватара
        if ($request->has('remove_avatar') && $request->remove_avatar == '1') {
            if ($user->avatar) {
                // Удаляем файл из хранилища
                Storage::disk('public')->delete($user->avatar);
                $user->avatar = null;
            }
        }

        // Обработка загрузки нового аватара
        if ($request->hasFile('avatar')) {
            // Удаляем старый аватар если есть
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Сохраняем новый аватар
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        // Обновляем остальные данные
        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];

        $user->save();

        return back()->with('success', 'Профиль успешно обновлен');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function deleteAvatar(Request $request)
    {
        \Log::info('Starting avatar deletion for user: ' . auth()->id());

        try {
            $user = auth()->user();
            \Log::info('User current avatar: ' . $user->avatar);

            if ($user->avatar) {
                \Log::info('Deleting avatar file: ' . $user->avatar);

                // Удаляем файл из хранилища
                $deleted = Storage::disk('public')->delete($user->avatar);
                \Log::info('File deletion result: ' . ($deleted ? 'success' : 'failed'));

                // Обновляем запись в базе данных
                $user->avatar = null;
                $user->save();

                \Log::info('Avatar set to null in database');
            } else {
                \Log::info('User has no avatar to delete');
            }

            \Log::info('Avatar deletion completed successfully');

            return response()->json([
                'success' => true,
                'message' => 'Аватар успешно удален'
            ]);

        } catch (\Exception $e) {
            \Log::error('Avatar deletion error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении аватара: ' . $e->getMessage()
            ], 500);
        }
    }
}
