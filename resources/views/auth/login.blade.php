<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | AirViz</title>
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

        .login-card {
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

        /* Modern Toggle Switch */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #334155;
            transition: .4s;
            border-radius: 24px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.toggle-slider {
            background-color: #3b82f6;
        }

        input:checked+.toggle-slider:before {
            transform: translateX(20px);
        }

        input:focus+.toggle-slider {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }

        .toggle-label {
            margin-left: 12px;
            font-size: 14px;
            color: #e2e8f0;
        }

        .nav-link {
            position: relative;
            color: #94a3b8;
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

        .active-nav {
            color: #e2e8f0;
        }

        .active-nav::after {
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
                <a href="/login" class="nav-link active-nav">Login</a>
                <a href="/register"
                    class="px-4 py-2 bg-blue-500/10 text-blue-400 rounded-lg font-medium hover:bg-blue-500/20 transition">Register</a>
            </div>

            <button class="md:hidden text-slate-300">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </nav>
    </div>

    <div class="w-full max-w-md fade-in">
        <div class="login-card p-8 sm:p-10">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-blue-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-lock-open text-blue-400 text-2xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2">Welcome Back</h1>
                <p class="text-slate-400">Sign in to access your air quality dashboard</p>
            </div>

            <form class="space-y-6" action="/login" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-300 mb-1">Email address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-slate-500"></i>
                            </div>
                            <input id="email" name="email" type="email" autocomplete="email" required
                                class="input-field pl-10 block w-full px-4 py-3 rounded-lg placeholder-slate-500 focus:outline-none text-white"
                                placeholder="you@example.com">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-300 mb-1">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-slate-500"></i>
                            </div>
                            <input id="password" name="password" type="password" autocomplete="current-password"
                                required
                                class="input-field pl-10 block w-full px-4 py-3 rounded-lg placeholder-slate-500 focus:outline-none text-white"
                                placeholder="••••••••">
                        </div>
                    </div>
                </div>

                <!-- ... rest of your form content ... -->

                <div>
                    <button type="submit"
                        class="btn-gradient w-full flex justify-center items-center py-3 px-4 rounded-lg text-white font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Sign in
                        <i class="fas fa-arrow-right ml-2"></i>
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
                            Don't have an account?
                        </span>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="/register"
                        class="w-full flex justify-center py-2 px-4 border border-slate-700 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800/50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                        Create new account
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

            // Add animation to the toggle switch
            const toggleSwitch = document.getElementById('remember_me');
            if (toggleSwitch) {
                toggleSwitch.addEventListener('change', function() {
                    const slider = this.nextElementSibling;
                    if (this.checked) {
                        gsap.to(slider, {
                            backgroundColor: '#3b82f6',
                            duration: 0.3
                        });
                    } else {
                        gsap.to(slider, {
                            backgroundColor: '#334155',
                            duration: 0.3
                        });
                    }
                });
            }

            // Set active nav link based on current page
            const currentPage = window.location.pathname;
            const navLinks = document.querySelectorAll('.nav-link');

            navLinks.forEach(link => {
                if (link.getAttribute('href') === currentPage) {
                    link.classList.add('active-nav');
                } else {
                    link.classList.remove('active-nav');
                }
            });
        });
    </script>
</body>

</html>
