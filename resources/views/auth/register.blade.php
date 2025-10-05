{{-- resources/views/auth/register.blade.php --}}
<x-app-layout>
    <x-slot name="title">Register</x-slot>

    <div class="max-w-md mx-auto mt-8 bg-white shadow-md rounded p-6">
        <h1 class="text-2xl font-bold mb-4">Create an account</h1>

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="mb-4 text-sm text-red-600">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            {{-- Name --}}
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}"
                       required autofocus
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
                @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Email --}}
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}"
                       required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
                @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Password --}}
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input id="password" name="password" type="password"
                       required autocomplete="new-password"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
                @error('password') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Confirm Password --}}
            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                    Confirm Password
                </label>
                <input id="password_confirmation" name="password_confirmation" type="password"
                       required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
            </div>

            {{-- Consent --}}
            <div class="mb-6">
                <div class="flex items-start gap-2">
                    <input
                        id="consent"
                        name="consent"
                        type="checkbox"
                        value="1"
                        required
                        class="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                    >
                    <label for="consent" class="text-sm text-gray-700">
                        I agree to the
                        <a href="{{ route('privacy.policy') }}" target="_blank" class="text-indigo-600 hover:underline">
                            Privacy Policy
                        </a>
                        and
                        <a href="{{ route('terms.of.use') }}" target="_blank" class="text-indigo-600 hover:underline">
                            Terms of Use
                        </a>.
                    </label>
                </div>
                @error('consent')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <a href="{{ route('login') }}"
                class="order-2 sm:order-1 text-sm text-indigo-600 hover:underline">
                    Already registered?
                </a>

                <button type="submit"
                        class="inline-flex items-center justify-center
                            px-4 py-2 rounded-md font-medium
                            bg-indigo-600 text-white hover:bg-indigo-700
                            focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2
                            border border-indigo-600"
                        style="background-color:#4f46e5;color:#fff;border-color:#4f46e5;">
                Register
                </button>

            </div>

        </form>
    </div>
</x-app-layout>


