<x-guest-layout>
    <div style="max-width: 400px; margin: 100px auto; padding: 2rem; background: #fff; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); text-align: center;">
        <h2 style="margin-bottom: 1.5rem; font-size: 1.25rem;">ユーザー新規登録画面</h2>

        <!-- Validation Errors -->
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

            <!-- Email -->
            <div class="mt-4">
                <input id="email" class="block w-full border border-gray-300 rounded px-3 py-2 mt-2" type="email" name="email" required placeholder="アドレス" value="{{ old('email') }}" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <input id="password" class="block w-full border border-gray-300 rounded px-3 py-2 mt-2" type="password" name="password" required placeholder="パスワード" />
            </div>

            <div class="mt-6 flex justify-between">
                
                <button type="submit" style="background-color: orange; color: white; padding: 8px 16px; border: none; border-radius: 5px;">
                    登録
                </button>

                
                <a href="{{ route('login') }}" style="background-color: gray; color: white; padding: 8px 16px; border-radius: 5px; text-decoration: none;">
                    戻る
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>
