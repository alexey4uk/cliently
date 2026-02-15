<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request)
    {
        $isPanel = $request->routeIs('panel.profile.edit') || str_starts_with($request->path(), 'panel');

        return view('profile.account', [
            'user' => $request->user(),
            'countries' => Country::getForPhoneSelect(),
            'layout' => $isPanel ? 'panel' : 'user',
            'profileUpdateRoute' => $isPanel ? 'panel.profile.update' : 'profile.update',
            'profilePasswordUpdateRoute' => $isPanel ? 'panel.profile.password.update' : 'profile.password.update',
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
            'phone_country_id' => ['nullable', 'required_with:phone', 'exists:countries,id'],
            'phone' => [
                'nullable',
                'string',
                'regex:/^\+[0-9]{10,15}$/',
                Rule::unique('phones', 'phone')
                    ->where('phoneable_type', User::class)
                    ->ignore($user->primaryPhone?->id),
            ],
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'remove_avatar' => 'sometimes|boolean',
        ], [
            'name.required' => 'Поле "Имя" обязательно для заполнения.',
            'email.required' => 'Поле "Email" обязательно для заполнения.',
            'email.email' => 'Неверный формат email адреса.',
            'email.unique' => 'Этот email уже используется.',
            'phone_country_id.required_with' => 'Выберите страну при указании телефона.',
            'phone.regex' => 'Телефон должен быть в формате E.164 (например, +375291234567).',
            'phone.unique' => 'Этот телефон уже используется.',
            'avatar.image' => 'Файл должен быть изображением.',
            'avatar.mimes' => 'Изображение должно быть в формате: jpeg, png, jpg, gif или webp.',
            'avatar.max' => 'Размер изображения не должен превышать 5 МБ.',
        ]);

        // Обработка удаления аватара
        if ($request->has('remove_avatar') && $request->remove_avatar == '1') {
            if ($user->avatar) {
                // Удаляем файл только если это локальный файл, а не внешний URL
                if (! $user->hasExternalAvatar()) {
                    Storage::delete($user->avatar);
                }
                $user->avatar = null;
            }
        }

        // Обработка загрузки нового аватара
        if ($request->hasFile('avatar')) {
            // Удаляем старый аватар если есть и это локальный файл
            if ($user->avatar && ! $user->hasExternalAvatar()) {
                Storage::delete($user->avatar);
            }

            // Сохраняем новый аватар
            $file = $request->file('avatar');

            // Собираем имя: avatar_1_1675345678.jpg
            $fileName = 'avatar_'.$user->id.'_'.now()->format('Y-m-d_H-i-s').'_'.Str::random(5).'.'.$file->extension();
            $path = $file->storeAs('avatars', $fileName);

            $user->avatar = $path;
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->save();

        $phoneE164 = $validated['phone'] ?? null;
        $phoneCountryId = isset($validated['phone_country_id']) ? (int) $validated['phone_country_id'] : null;

        if ($phoneE164 && $phoneCountryId) {
            $primary = $user->primaryPhone;
            if ($primary) {
                $primary->update(['country_id' => $phoneCountryId, 'phone' => $phoneE164]);
            } else {
                $user->phones()->create([
                    'country_id' => $phoneCountryId,
                    'phone' => $phoneE164,
                    'type' => 'primary',
                ]);
            }
        } elseif ($user->primaryPhone) {
            $user->primaryPhone->delete();
        }

        // Определяем контекст для редиректа
        $isPanel = $request->routeIs('panel.profile.update') || str_starts_with($request->path(), 'panel');
        $redirectRoute = $isPanel ? 'panel.profile.edit' : 'profile.edit';

        return redirect()->route($redirectRoute)->with('success', 'Профиль успешно обновлен');
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

        // Определяем контекст для редиректа
        $isPanel = $request->routeIs('panel.profile.password.update') || str_starts_with($request->path(), 'panel');
        $redirectRoute = $isPanel ? 'panel.profile.edit' : 'profile.edit';

        return redirect()->route($redirectRoute)->with('success', 'Пароль успешно изменен');
    }

    /**
     * Delete the user's avatar.
     */
    public function deleteAvatar(Request $request)
    {
        $user = Auth::user();

        if ($user->avatar) {
            // Удаляем файл только если это локальный файл, а не внешний URL
            if (! $user->hasExternalAvatar()) {
                Storage::delete($user->avatar);
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
            Storage::delete($user->avatar);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
