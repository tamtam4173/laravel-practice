<x-guest-layout>
    <div style="max-width: 400px; margin: 100px auto; padding: 2rem; background: #fff; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); text-align: center;">
        <h2 style="margin-bottom: 1.5rem; font-size: 1.25rem;">ユーザーログイン画面</h2>

        <!-- バリデーションエラー表示 -->
        @if ($errors->any())
            <div style="color: red; margin-bottom: 1rem;">
                <ul style="list-style-type: none; padding-left: 0;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- メールアドレス -->
            <div class="mt-4">
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="アドレス"
                    style="width: 100%; border: 1px solid #ccc; border-radius: 4px; padding: 10px; margin-top: 10px;" />
            </div>

            <!-- パスワード -->
            <div class="mt-4">
                <input id="password" type="password" name="password" required placeholder="パスワード"
                    style="width: 100%; border: 1px solid #ccc; border-radius: 4px; padding: 10px; margin-top: 10px;" />
            </div>

            <!-- ボタン -->
            <div class="mt-6 flex justify-between" style="margin-top: 20px; display: flex; justify-content: space-between;">
                <a href="{{ route('register') }}" style="background-color: orange; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none;">
                    新規登録
                </a>
                <button type="submit" style="background-color: deepskyblue; color: white; padding: 10px 20px; border: none; border-radius: 5px;">
                    ログイン
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
