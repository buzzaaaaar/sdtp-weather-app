<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | AirViz</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a;
            color: #e2e8f0;
        }

        .register-card {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            transition: all 0.3s ease;
        }

        .input-field {
            background: rgba(30, 41, 59, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s;
        }

        .input-field:focus {
            background: rgba(30, 41, 59, 0.8);
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }

        .btn-gradient {
            background: linear-gradient(90deg, #3b82f6 0%, #6366f1 100%);
            transition: all 0.3s;
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }

        .password-strength {
            height: 4px;
            background: rgba(30, 41, 59, 0.5);
            transition: all 0.3s ease;
        }

        .password-container:focus-within .password-strength {
            height: 6px;
        }

        .strength-meter {
            height: 100%;
            transition: width 0.4s ease, background-color 0.4s ease;
        }

        .nav-link {
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 2px;
            background: #3b82f6;
            transition: width 0.3s;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .floating {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .fade-in {
            opacity: 0;
            transform: translateY(20px);
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4">
    <div class="absolute inset-0 -z-10 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-900/20 to-cyan-900/10"></div>
        <div class="absolute top-0 right-0 w-1/3 h-1/3 bg-blue-500/10 rounded-full filter blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-1/3 h-1/3 bg-cyan-500/10 rounded-full filter blur-3xl"></div>
    </div>

    <div class="absolute top-0 left-0 w-full">
        <nav class="px-6 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center floating">
                    <i class="fas fa-wind text-white"></i>
                </div>
                <span
                    class="text-xl font-bold bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text text-transparent">AirViz</span>
            </div>

            <div class="hidden md:flex items-center space-x-8">
                <a href="/" class="nav-link font-medium">Dashboard</a>
                <a href="/analytics" class="nav-link">Analytics</a>
                <a href="/login" class="nav-link">Login</a>
                <a href="/register"
                    class="px-4 py-2 bg-blue-500/10 text-blue-400 rounded-lg font-medium hover:bg-blue-500/20 transition active-nav">Register</a>
            </div>

            <button class="md:hidden text-slate-300">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </nav>
    </div>

    <div class="w-full max-w-md fade-in">
        <div class="register-card p-8 sm:p-10">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-blue-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user-plus text-blue-400 text-2xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2">Create Account</h1>
                <p class="text-slate-400">Join our air quality monitoring community</p>
            </div>

            @if ($errors->any())
            <div class="bg-red-900/10 border-l-4 border-red-500 p-4 mb-6 rounded-lg">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-500"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-400">There were errors with your submission</h3>
                        <div class="mt-2 text-sm text-red-300">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <form class="space-y-5" action="/register" method="POST">
                @csrf
                <div class="space-y-5">
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-300 mb-1">Full Name</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-slate-500"></i>
                            </div>
                            <input id="name" name="name" type="text" autocomplete="name" required
                                class="input-field pl-10 block w-full px-4 py-3 rounded-lg placeholder-slate-500 focus:outline-none text-white"
                                placeholder="John Doe">
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-300 mb-1">Email Address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-slate-500"></i>
                            </div>
                            <input id="email" name="email" type="email" autocomplete="email" required
                                class="input-field pl-10 block w-full px-4 py-3 rounded-lg placeholder-slate-500 focus:outline-none text-white"
                                placeholder="you@example.com">
                        </div>
                    </div>

                    <div class="password-container">
                        <label for="password" class="block text-sm font-medium text-slate-300 mb-1">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-slate-500"></i>
                            </div>
                            <input id="password" name="password" type="password" autocomplete="new-password" required
                                class="input-field pl-10 block w-full px-4 py-3 rounded-lg placeholder-slate-500 focus:outline-none text-white"
                                placeholder="••••••••">
                        </div>
                        <div class="password-strength mt-1 rounded-full overflow-hidden">
                            <div id="strength-meter" class="strength-meter rounded-full" style="width: 0%"></div>
                        </div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-300 mb-1">Confirm
                            Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-slate-500"></i>
                            </div>
                            <input id="password_confirmation" name="password_confirmation" type="password"
                                autocomplete="new-password" required
                                class="input-field pl-10 block w-full px-4 py-3 rounded-lg placeholder-slate-500 focus:outline-none text-white"
                                placeholder="••••••••">
                        </div>
                    </div>
                </div>

                <div>
                    <button type="submit"
                        class="btn-gradient w-full flex justify-center items-center py-3 px-4 rounded-lg text-white font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Create Account
                        <i class="fas fa-user-plus ml-2"></i>
                    </button>
                </div>
            </form>

            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-700"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 text-slate-500 bg-slate-900/70">
                            Already have an account?
                        </span>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="/login"
                        class="w-full flex justify-center py-2 px-4 border border-slate-700 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800/50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                        Sign in instead
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            gsap.to('.fade-in', {
                opacity: 1,
                y: 0,
                duration: 0.8,
                ease: 'power2.out'
            });

            const inputFields = document.querySelectorAll('.input-field');
            inputFields.forEach(field => {
                field.addEventListener('focus', function() {
                    gsap.to(this, {
                        scale: 1.02,
                        duration: 0.2
                    });
                });

                field.addEventListener('blur', function() {
                    gsap.to(this, {
                        scale: 1,
                        duration: 0.2
                    });
                });
            });

            const submitBtn = document.querySelector('button[type="submit"]');
            submitBtn.addEventListener('mouseenter', function() {
                gsap.to(this, {
                    y: -2,
                    duration: 0.2
                });
            });

            submitBtn.addEventListener('mouseleave', function() {
                gsap.to(this, {
                    y: 0,
                    duration: 0.2
                });
            });

            const passwordInput = document.getElementById('password');
            const strengthMeter = document.getElementById('strength-meter');

            passwordInput.addEventListener('input', function() {
                const password = this.value;
                let strength = 0;

                if (password.length >= 8) strength += 25;
                if (password.length >= 12) strength += 25;
                if (/[A-Z]/.test(password)) strength += 15;
                if (/[0-9]/.test(password)) strength += 15;
                if (/[^A-Za-z0-9]/.test(password)) strength += 20;

                strength = Math.min(100, strength);
                strengthMeter.style.width = `${strength}%`;

                if (strength < 40) {
                    strengthMeter.style.backgroundColor = '#ef4444';
                } else if (strength < 70) {
                    strengthMeter.style.backgroundColor = '#f59e0b';
                } else {
                    strengthMeter.style.backgroundColor = '#10b981';
                }
            });
        });
    </script>
</body>

</html>
