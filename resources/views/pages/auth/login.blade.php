<x-layouts::auth :title="__('Log in')">
    <h1 class="text-3xl py-3 font-bold text-[#14BEF0] tracking-wide">
        {{ __('Welcome Back') }}
    </h1>

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form method="POST" action="{{ route('login.store') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div class="flex flex-col gap-2">
            <label for="email" class="text-[#28328C] text-sm font-normal tracking-wide">
                {{ __('Email') }}
            </label>
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email') }}"
                class="border border-gray-300 bg-white p-3 text-gray-700 rounded text-sm outline-none focus:border-[#28328C] focus:ring-1 focus:ring-[#28328C] w-full"
                placeholder="admin@docspe.my"
                required
                autofocus
                autocomplete="email"
            />
            @error('email')
                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
            @enderror
        </div>

        <!-- Password -->
        <div class="flex flex-col gap-2">
            <label for="password" class="text-[#28328C] text-sm font-normal tracking-wide">
                {{ __('Password') }}
            </label>
            <input
                id="password"
                name="password"
                type="password"
                class="border border-gray-300 bg-white p-3 text-gray-700 rounded text-sm outline-none focus:border-[#28328C] focus:ring-1 focus:ring-[#28328C] w-full"
                required
                autocomplete="current-password"
            />
            @error('password')
                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="flex items-center gap-2">
            <label for="remember" class="font-normal flex items-center text-sm text-[#28328C] tracking-wide">
                <input
                    type="checkbox"
                    value="1"
                    name="remember"
                    id="remember"
                    class="h-5 w-5 mr-2 rounded-sm accent-[#28328C]"
                    {{ old('remember') ? 'checked' : '' }}
                />
                {{ __('Remember me on this computer') }}
            </label>
        </div>

        <button
            type="submit"
            class="w-full bg-[#28328C] rounded-lg py-3 px-2 shadow-lg text-sm font-semibold text-white uppercase hover:bg-blue-950 duration-500"
            data-test="login-button"
        >
            {{ __('Log in') }}
        </button>
    </form>

    <div class="flex flex-col justify-center items-center gap-3">
        <a
            href="{{ route('register') }}"
            class="text-[#28328C] font-normal text-base"
            wire:navigate
        >
            {{ __('Register') }}
        </a>
    </div>
</x-layouts::auth>
