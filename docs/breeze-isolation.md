# Breeze Authentication Isolation Guide

This document explains how the Breeze authentication stack is isolated from the rest of TaskInfinity and outlines the conventions that keep it that way.

## Folder structure overview

```
resources/views/
  auth_breeze/        # all Breeze templates (auth pages + related partials)
  layouts/            # global app layouts
  livewire/           # product-facing Livewire views
```

* **Auth views** live exclusively in `resources/views/auth_breeze`. Copy or extend templates inside this directory only when working on authentication flows (login, registration, password reset, verification, two-factor, etc.).
* **App views** should extend `resources/views/layouts/app.blade.php` (or a purpose-built layout inside `resources/views/layouts`). They must not reference files from `auth_breeze` to avoid leaking Breeze styling.

## Creating new authentication screens

1. Add the Blade template inside `resources/views/auth_breeze`. Reuse the existing partials in that folder for inputs, cards, and email layouts.
2. Update the relevant route or Fortify view binding to point at the new Blade file. Breeze controllers already return views using this namespace (for example, `view('auth_breeze.login')`).
3. Keep dependencies limited to Breeze requirements. Avoid pulling in application layouts, Livewire components, or third-party Blade components.
4. If the screen needs shared assets, place them under `resources/views/auth_breeze/_partials` (create the directory if required) and include them with standard Blade directives.

## Building product screens

1. Use `resources/views/layouts/app.blade.php` as the base layout. It provides Vite assets, Livewire directives, and the navigation shell built specifically for TaskInfinity.
2. Compose the UI with custom Blade/HTML markup and the Tailwind utilities defined in our stylesheet. Third-party Blade components (`<x-*>`) are prohibited outside `auth_breeze`.
3. Register Livewire components under `app/Livewire/**` and render them from Blade with `@livewire` or `<livewire:...>`.
4. If a page requires guest/public layout, create another layout inside `resources/views/layouts` (for example, `guest.blade.php`) that follows the same asset loading pattern as `app.blade.php`.

## Package boundaries

* **Allowed**: Laravel Breeze, Laravel Fortify, Livewire core, Tailwind, Alpine, and other first-party packages already in `composer.json` / `package.json`.
* **Disallowed**: Jetstream, WireUI, Livewire Volt/Flux, Blade UI Kit, or any dependency that introduces generic `<x-*>` Blade components for the application layer.
* Any new dependency that ships with Blade components must be vetted and, if needed, wrapped inside our own view partials to keep the `<x-*>` elements out of the product views.

## Testing checklist

Before opening a pull request that touches authentication or layout code:

- Run `php artisan test` to cover the feature tests adjusted for Breeze controllers.
- Execute `npm run build` to ensure the Tailwind compilation succeeds with the updated templates.
- Confirm the auth flows manually (login, register, password reset, email verification, two-factor challenge).
- Load the main application screens to verify Livewire components and modals render correctly without Breeze assets.

Keeping these practices ensures Breeze remains an isolated dependency for authentication, while the TaskInfinity product keeps its bespoke Livewire and Tailwind interface.
