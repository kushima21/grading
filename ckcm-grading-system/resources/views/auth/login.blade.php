<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    @vite(['resources/css/login.css', 'resources/js/app.js'])
</head>
<body>
    <div class="login-main-container">
        <style>
            html, body {
                background: url('{{ asset('system_images/background.jpg') }}');
                background-size: cover;
                background-position: center;
            }
        </style>
        <div class="login-box-container">
            <div class="img-container">
                <img src="{{ asset('system_images/logo.jpg') }}" alt="Logo">
            </div>
                <span class="span">Grading System Inc. v1.02.3231</span>
                <h2 class="header">Sign in your CKCM Account!</h2>

                <div class="login-form-container">
                    <form method="POST" action="{{ route('login.post') }}">
                        <label for="email">Id Number or Email</label>
                        <br>
                        <input type="text" name="email" id="name" placeholder="Email & School ID" required>
                        <br>
                        <label for="password">Password</label>
                        <br>
                        <input type="password" name="password" id="password" placeholder="Password" required>

                        <!-- Remember container -->
                        <div class="remember">
                            <div class="remember-left">
                                <input type="checkbox" id="remember">
                                <label for="remember">Remember Me</label>
                            </div>
                            <a href="{{ route('password.request') }}" class="remember-right">Forget your Password?</a>
                        </div>
                        <div class="loginBtn">
                            <button type="submit">Sign in Now</button>
                        </div>
                    </form>
                </div>
                <div class="or">
                    <p>___________or continue with____________</p>
                </div>

                <div class="googleBtn">
                    <a href="#">
                        <button type="button">Sign with Google</button>
                    </a>
                </div>

                <div class="term-condetions">
                    <p>By clicking “Sign in”, you agree to our <span>Terms of Service</span> and <span>Privacy Statement</span> . 
                    We’ll occasionally send you account related emails.</p>
                </div>

                <div class="trademark">
                    <p><span>CKCM Network</span> is a Trademark of MIS CKCM.
                    Copyright © 2021-2025 CKCM Technologies, LLC.</p>
                </div>

        </div>
    </div>
</body>
</html>