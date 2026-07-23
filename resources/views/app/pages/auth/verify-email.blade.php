<main class="min-h-[calc(100vh-72px)] pt-[72px] flex flex-col items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-[#111111] border-2 border-[#333333] rounded-3xl p-8 md:p-10">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-black text-white mb-2">{{ __('ui.verify_email_title') }}</h1>
                <p class="text-[#CCCCCC] text-sm">{{ __('ui.verify_email_message') }}</p>
            </div>
            @if (session('status') == 'verification-link-sent')
                <p class="mb-4 text-sm text-green-400">{{ __('ui.verify_email_sent') }}</p>
            @endif
            <div class="flex flex-col gap-3">
                <form action="{{ route('verification.send') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-[#F97316] hover:bg-[#EF4444] text-white font-bold py-3 rounded-xl">
                        {{ __('ui.verify_email_resend') }}
                    </button>
                </form>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-[#CCCCCC] hover:text-[#F97316] text-sm">{{ __('ui.verify_email_logout') }}</button>
                </form>
            </div>
        </div>
    </div>
</main>
