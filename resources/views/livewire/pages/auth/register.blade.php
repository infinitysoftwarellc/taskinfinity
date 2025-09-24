<?php

use App\Models\User;
use App\Models\Organization;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        // Envolve a lógica em uma transação de banco de dados para garantir a integridade
        DB::transaction(function () use ($validated) {
            
            // Cria o usuário
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            // Cria uma organização para o usuário
            $organization = Organization::create([
                'name' => $validated['name'] . "'s Organization",
                'owner_id' => $user->id,
            ]);

            // Atribui o usuário à sua nova organização
            $user->organization_id = $organization->id;
            $user->save();

            event(new Registered($user));

            Auth::login($user);
        });

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <x-auth-header title="Create an account" description="Start your journey with us today" />

    <form wire:submit="register" class="mt-8 grid gap-y-6">
        <div>
            <x-input-label for="name" value="Name" />
            <x-text-input wire:model="name" id="name" name="name" type="text" required autofocus autocomplete="name" class="mt-1 w-full" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input wire:model="email" id="email" name="email" type="email" required autocomplete="username" class="mt-1 w-full" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="password" value="Password" />
            <x-text-input wire:model="password" id="password" name="password" type="password" required autocomplete="new-password" class="mt-1 w-full" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="password_confirmation" value="Confirm Password" />
            <x-text-input wire:model="password_confirmation" id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="mt-1 w-full" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>
        <x-primary-button type="submit" intent="primary" full-width>
            Register
        </x-primary-button>
    </form>
    <p class="mt-8 text-center text-sm/6 text-zinc-500 dark:text-zinc-400">
        Already registered?
        <a href="{{ route('login') }}" wire:navigate class="font-semibold text-zinc-950 hover:text-zinc-800 dark:text-white dark:hover:text-zinc-100">
            Sign in
        </a>
    </p>
</div>