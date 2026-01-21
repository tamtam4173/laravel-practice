<x-guest-layout>
    <div style="max-width: 400px; margin: 100px auto; padding: 2rem; background: #fff; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); text-align: center;">
        <h2 style="margin-bottom: 1.5rem; font-size: 1.25rem;">ユーザー新規登録</h2>

        @if (session('status'))
            <div style="color: green; margin-bottom: 1rem;">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div style="color: red; margin-bottom: 1rem;">
                <ul style="list-style-type: none; padding-left: 0;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mt-4">
                <input id="email" type="email" name="email" required value="{{ old('email') }}"
                       class="block w-full border border-gray-300 rounded px-3 py-2 mt-2"
                       placeholder="アドレス" />
            </div>

            <div class="mt-4">
                <input id="password" type="password" name="password" required
                       class="block w-full border border-gray-300 rounded px-3 py-2 mt-2"
                       placeholder="パスワード（8文字以上）" />
            </div>

            {{-- 確認を使うなら以下を有効化し、controller で confirmed を有効に --}}
            {{-- <div class="mt-4">
                <input id="password_confirmation" type="password" name="password_confirmation"
                       class="block w-full border border-gray-300 rounded px-3 py-2 mt-2"
                       placeholder="パスワード（確認）" />
            </div> --}}

            <div class="mt-6 flex justify-between">
                <a href="{{ route('login') }}" style="background-color: gray; color: white; padding: 8px 16px; border-radius: 5px; text-decoration: none;">
                    戻る
                </a>
                <button type="submit" style="background-color: orange; color: white; padding: 8px 16px; border: none; border-radius: 5px;">
                    登録
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
