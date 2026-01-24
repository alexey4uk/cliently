<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request)
    {
        return view('profile', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:20|regex:/^\+375\d{9}$/|unique:users,phone,'.$user->id,
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'remove_avatar' => 'sometimes|boolean',
        ], [
            'name.required' => 'Поле "Имя" обязательно для заполнения.',
            'email.required' => 'Поле "Email" обязательно для заполнения.',
            'email.email' => 'Неверный формат email адреса.',
            'email.unique' => 'Этот email уже используется.',
            'phone.unique' => 'Этот телефон уже используется',
            'phone.regex' => 'Телефон должен быть в формате +375XXXXXXXXX (9 цифр после +375).',
            'avatar.image' => 'Файл должен быть изображением.',
            'avatar.mimes' => 'Изображение должно быть в формате: jpeg, png, jpg, gif или webp.',
            'avatar.max' => 'Размер изображения не должен превышать 5 МБ.',
        ]);

        // Обработка удаления аватара
        if ($request->has('remove_avatar') && $request->remove_avatar == '1') {
            if ($user->avatar) {
                // Удаляем файл только если это локальный файл, а не внешний URL
                if (!$user->hasExternalAvatar()) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $user->avatar = null;
            }
        }

        // Обработка загрузки нового аватара
        if ($request->hasFile('avatar')) {
            // Удаляем старый аватар если есть и это локальный файл
            if ($user->avatar && !$user->hasExternalAvatar()) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Сохраняем новый аватар
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        // Обновляем остальные данные
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;
        $user->save();

        return back()->with('success', 'Профиль успешно обновлен');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'current_password.required' => 'Поле "Текущий пароль" обязательно для заполнения.',
            'current_password.current_password' => 'Неверный текущий пароль.',
            'password.required' => 'Поле "Новый пароль" обязательно для заполнения.',
            'password.confirmed' => 'Пароли не совпадают.',
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Пароль успешно изменен');
    }

    /**
     * Delete the user's avatar.
     */
    public function deleteAvatar(Request $request)
    {
        $user = Auth::user();

        if ($user->avatar) {
            // Удаляем файл только если это локальный файл, а не внешний URL
            if (!$user->hasExternalAvatar()) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            $user->avatar = null;
            $user->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Аватар успешно удален',
        ]);
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

        // Удаляем аватар если есть
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
