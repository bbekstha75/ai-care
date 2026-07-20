<x-layouts::auth :title="__('Register')">
    <h1 class="text-4xl py-3 font-semibold text-[#28328C] tracking-wide">
        {{ __('Create Your Account') }}
    </h1>

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form method="POST" action="{{ route('register.store') }}" class="space-y-4 text-black">
        @csrf

        <!-- Name -->
        <div class="flex flex-col gap-2">
            <label for="name" class="text-[#28328C] text-xs font-normal leading-5 tracking-wide">
                {{ __('Name') }} <span class="text-red-500">*</span>
            </label>
            <input
                id="name"
                name="name"
                type="text"
                value="{{ old('name') }}"
                class="border border-gray-300 bg-white p-3 text-gray-700 rounded text-sm outline-none focus:border-[#28328C] focus:ring-1 focus:ring-[#28328C] w-full"
                placeholder="{{ __('Full name') }}"
                required
                autofocus
                autocomplete="name"
            />
            @error('name')
                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
            @enderror
        </div>

        <!-- Email Address -->
        <div class="flex flex-col gap-2">
            <label for="email" class="text-[#28328C] text-xs font-normal leading-5 tracking-wide">
                {{ __('Email') }} <span class="text-red-500">*</span>
            </label>
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email') }}"
                class="border border-gray-300 bg-white p-3 text-gray-700 rounded text-sm outline-none focus:border-[#28328C] focus:ring-1 focus:ring-[#28328C] w-full"
                placeholder="email@example.com"
                required
                autocomplete="email"
            />
            @error('email')
                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
            @enderror
        </div>

        <!-- Password fields in grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Password -->
            <div class="flex flex-col gap-2">
                <label for="password" class="text-[#28328C] text-xs font-normal leading-5 tracking-wide">
                    {{ __('Password') }} <span class="text-red-500">*</span>
                </label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    class="border border-gray-300 bg-white p-3 text-gray-700 rounded text-sm outline-none focus:border-[#28328C] focus:ring-1 focus:ring-[#28328C] w-full"
                    placeholder="{{ __('Password') }}"
                    required
                    autocomplete="new-password"
                />
                @error('password')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="flex flex-col gap-2">
                <label for="password_confirmation" class="text-[#28328C] text-xs font-normal leading-5 tracking-wide">
                    {{ __('Confirm Password') }} <span class="text-red-500">*</span>
                </label>
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    class="border border-gray-300 bg-white p-3 text-gray-700 rounded text-sm outline-none focus:border-[#28328C] focus:ring-1 focus:ring-[#28328C] w-full"
                    placeholder="{{ __('Confirm') }}"
                    required
                    autocomplete="new-password"
                />
                @error('password_confirmation')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <button
            type="submit"
            class="w-full bg-[#28328C] rounded-lg py-3 px-2 shadow-lg text-sm font-semibold text-white uppercase hover:bg-blue-950 duration-500"
            data-test="register-user-button"
        >
            {{ __('Register') }}
        </button>
    </form>

    <div class="flex text-[#28328C] justify-center items-center gap-3">
        {{ __('I have an account.') }}
        <a
            href="{{ route('login') }}"
            class="text-[#14BEF0] underline underline-offset-4 font-bold"
            wire:navigate
        >
            {{ __('Login') }}
        </a>
    </div>
</x-layouts::auth>
