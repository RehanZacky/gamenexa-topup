<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrator — GameNexa</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        dark: {
                            950: '#040308',
                            900: '#07060e',
                            800: '#0c0919',
                            700: '#110d24',
                            600: '#181333',
                        },
                        neon: {
                            purple: '#a855f7',
                            light: '#c084fc',
                            vivid: '#9333ea',
                        }
                    },
                    fontFamily: {
                        heading: ['Outfit', 'sans-serif'],
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-dark-900 min-h-screen flex items-center justify-center p-4 antialiased text-white selection:bg-purple-500 selection:text-white"
      style="background-image: radial-gradient(circle at 50% 30%, rgba(168, 85, 247, 0.15) 0%, transparent 60%), radial-gradient(circle at 80% 80%, rgba(124, 58, 237, 0.12) 0%, transparent 50%);">

    <div class="w-full max-w-md">
        <!-- Logo & Title -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center p-3 rounded-2xl bg-dark-800 border border-purple-500/30 shadow-xl shadow-purple-500/10 mb-4">
                <img src="{{ asset('images/logo.png') }}" alt="GameNexa Logo" class="w-12 h-12 rounded-xl object-cover">
            </div>
            <h1 class="font-heading font-black text-3xl tracking-tight text-white">
                GAME<span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-fuchsia-400">NEXA</span>
            </h1>
            <p class="text-xs uppercase tracking-widest text-purple-300 font-bold mt-1">Administrator Portal</p>
        </div>

        <!-- Login Card -->
        <div class="bg-dark-800/90 backdrop-blur-xl border border-purple-900/30 rounded-3xl p-8 shadow-2xl shadow-purple-950/50 relative overflow-hidden">
            <div class="absolute -top-24 -right-24 w-48 h-48 bg-purple-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="mb-6">
                <h2 class="font-heading font-extrabold text-xl text-white">Selamat Datang</h2>
                <p class="text-sm text-zinc-400 mt-1">Masukkan kredensial admin untuk masuk ke kontrol panel.</p>
            </div>

            <!-- Error Alerts -->
            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm font-semibold flex items-center gap-3">
                    <i class="fa-solid fa-triangle-exclamation text-base"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm font-semibold flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-base"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-bold text-zinc-300 uppercase tracking-wider mb-2">Email Administrator</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-purple-400">
                            <i class="fa-solid fa-envelope"></i>
                        </span>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                               class="w-full bg-dark-900/80 border border-purple-900/40 rounded-xl pl-11 pr-4 py-3.5 text-sm text-white placeholder-zinc-500 focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all"
                               placeholder="admin@gamenexa.com">
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-400 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-zinc-300 uppercase tracking-wider mb-2">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-purple-400">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" name="password" id="password" required
                               class="w-full bg-dark-900/80 border border-purple-900/40 rounded-xl pl-11 pr-4 py-3.5 text-sm text-white placeholder-zinc-500 focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all"
                               placeholder="••••••••">
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-400 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 cursor-pointer text-zinc-400 hover:text-zinc-200">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-dark-900 border-purple-900/50 text-purple-600 focus:ring-purple-500/20">
                        <span>Ingat saya</span>
                    </label>
                </div>

                <button type="submit"
                        class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-purple-600 via-purple-500 to-fuchsia-500 hover:from-purple-500 hover:to-fuchsia-400 text-white font-heading font-extrabold text-sm tracking-wider uppercase transition-all duration-200 shadow-lg shadow-purple-600/30 hover:shadow-purple-600/50 active:scale-[0.99] flex items-center justify-center gap-2">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    <span>Masuk ke Dashboard</span>
                </button>
            </form>
        </div>

        <div class="text-center mt-6">
            <a href="{{ route('home') }}" class="text-xs font-semibold text-zinc-400 hover:text-purple-300 transition-colors inline-flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Website Utama</span>
            </a>
        </div>
    </div>

</body>
</html>
