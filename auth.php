<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SignSpeakers - Login/Signup</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #1a73e8;
            --primary-light: #4285f4;
            --primary-lighter: #e8f0fe;
            --secondary: #0d47a1;
            --accent: #fbbc04;
            --light: #ffffff;
            --light-gray: #f8f9fa;
            --dark: #202124;
            --text: #3c4043;
            --success: #34a853;
            --error: #ea4335;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Google Sans', 'Segoe UI', sans-serif;
            background-color: var(--light-gray);
            color: var(--text);
            line-height: 1.6;
            overflow-x: hidden;
        }
        
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            background-color: var(--light);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: fixed;
            width: 100%;
            z-index: 1000;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary);
            text-decoration: none;
        }
        
        .logo i {
            color: var(--accent);
        }
        
        .auth-container {
            display: flex;
            min-height: 100vh;
            padding-top: 80px;
        }
        
        .auth-illustration {
            flex: 1;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            display: none;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }
        
        .illustration-content {
            max-width: 500px;
            color: white;
            z-index: 1;
            text-align: center;
        }
        
        .illustration-content h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .illustration-content p {
            opacity: 0.9;
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }
        
        .auth-form-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }
        
        .auth-card {
            background: var(--light);
            padding: 3rem;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            width: 100%;
            max-width: 450px;
            position: relative;
            overflow: hidden;
        }
        
        .auth-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--accent) 100%);
        }
        
        .form-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        
        .form-header h2 {
            font-size: 2rem;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }
        
        .form-header p {
            color: var(--text);
            opacity: 0.8;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark);
        }
        
        .input-with-icon {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text);
            opacity: 0.6;
        }
        
        .form-control {
            width: 100%;
            padding: 0.8rem 1rem 0.8rem 45px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px var(--primary-lighter);
        }
        
        .btn {
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            border: none;
            width: 100%;
            font-size: 1rem;
            margin-top: 0.5rem;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: var(--light);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(26, 115, 232, 0.2);
        }
        
        .btn-google {
            background-color: var(--light);
            color: var(--text);
            border: 1px solid #ddd;
        }
        
        .btn-google:hover {
            background-color: var(--light-gray);
        }
        
        .form-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--text);
        }
        
        .form-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }
        
        .form-footer a:hover {
            text-decoration: underline;
        }
        
        .divider {
            display: flex;
            align-items: center;
            margin: 1.5rem 0;
            color: var(--text);
            opacity: 0.7;
        }
        
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #ddd;
        }
        
        .divider::before {
            margin-right: 1rem;
        }
        
        .divider::after {
            margin-left: 1rem;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .alert-error {
            background-color: #fde8e8;
            color: var(--error);
            border-left: 4px solid var(--error);
        }
        
        .alert-success {
            background-color: #e6f4ea;
            color: var(--success);
            border-left: 4px solid var(--success);
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--text);
            opacity: 0.6;
        }
        
        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            overflow: hidden;
            z-index: 0;
        }
        
        .shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.1;
        }
        
        .shape-1 {
            width: 300px;
            height: 300px;
            background: var(--accent);
            top: -100px;
            right: -100px;
        }
        
        .shape-2 {
            width: 200px;
            height: 200px;
            background: var(--light);
            bottom: -50px;
            left: -50px;
        }
        
        @media (min-width: 992px) {
            .auth-illustration {
                display: flex;
            }
            
            .auth-card {
                padding: 3.5rem;
            }
        }
        
        @media (max-width: 576px) {
            .auth-card {
                padding: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <a href="index.html" class="logo">
            <i class="fas fa-hands"></i>
            <span>SignSpeakers</span>
        </a>
    </nav>
    
    <!-- Auth Container -->
    <div class="auth-container">
        <div class="auth-illustration">
            <div class="floating-shapes">
                <div class="shape shape-1"></div>
                <div class="shape shape-2"></div>
            </div>
            <div class="illustration-content">
                <h2>Breaking Communication Barriers</h2>
                <p>Join our community to experience seamless sign language translation</p>
                <img src="https://www.signsolutions.uk.com/wp-content/uploads/2022/11/AdobeStock_119534501-1024x682.jpeg" alt="Sign language illustration" style="max-width: 100%; border-radius: 8px; margin-top: 2rem;">
            </div>
        </div>
        
        <div class="auth-form-container">
            <div class="auth-card">
                <!-- Login Form (default) -->
                <div id="login-form">
                    <div class="form-header">
                        <h2>Welcome back</h2>
                        <p>Sign in to continue to SignSpeakers</p>
                    </div>
                    
                    <?php if (isset($_GET['error']) && $_GET['error'] == 'invalid'): ?>
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-circle"></i>
                            Invalid username or password
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($_GET['success']) && $_GET['success'] == 'registered'): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            Registration successful! Please log in.
                        </div>
                    <?php endif; ?>
                    
                    <form action="login.php" method="POST">
                        <div class="form-group">
                            <label for="login-username">Username</label>
                            <div class="input-with-icon">
                                <i class="fas fa-user input-icon"></i>
                                <input type="text" id="login-username" name="username" class="form-control" placeholder="Enter your username" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="login-password">Password</label>
                            <div class="input-with-icon">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password" id="login-password" name="password" class="form-control" placeholder="Enter your password" required>
                                <i class="fas fa-eye password-toggle" id="toggle-password"></i>
                            </div>
                        </div>
                        
                        <div class="form-group" style="display: flex; justify-content: space-between; align-items: center;">
                            <div style="display: flex; align-items: center;">
                                <input type="checkbox" id="remember-me" name="remember" style="margin-right: 8px;">
                                <label for="remember-me" style="margin-bottom: 0;">Remember me</label>
                            </div>
                            <a href="#" style="font-size: 0.9rem;">Forgot password?</a>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i> Sign In
                        </button>
                    </form>
                    
                    
                    
                    
                    
                    <div class="form-footer">
                        Don't have an account? <a href="#" id="show-signup">Sign up</a>
                    </div>
                </div>
                
                <!-- Signup Form (hidden initially) -->
                <div id="signup-form" style="display: none;">
                    <div class="form-header">
                        <h2>Create an account</h2>
                        <p>Start your journey with SignSpeakers</p>
                    </div>
                    
                    <?php if (isset($_GET['error']) && $_GET['error'] == 'exists'): ?>
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-circle"></i>
                            Username already exists
                        </div>
                    <?php endif; ?>
                    
                    <form action="register.php" method="POST">
                        <div class="form-group">
                            <label for="signup-username">Username</label>
                            <div class="input-with-icon">
                                <i class="fas fa-user input-icon"></i>
                                <input type="text" id="signup-username" name="username" class="form-control" placeholder="Choose a username" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="signup-email">Email</label>
                            <div class="input-with-icon">
                                <i class="fas fa-envelope input-icon"></i>
                                <input type="email" id="signup-email" name="email" class="form-control" placeholder="Enter your email" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="signup-password">Password</label>
                            <div class="input-with-icon">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password" id="signup-password" name="password" class="form-control" placeholder="Create a password" required>
                                <i class="fas fa-eye password-toggle" id="toggle-signup-password"></i>
                            </div>
                            <small style="display: block; margin-top: 0.5rem; color: var(--text); opacity: 0.7;">Minimum 8 characters</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="signup-confirm-password">Confirm Password</label>
                            <div class="input-with-icon">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password" id="signup-confirm-password" name="confirm_password" class="form-control" placeholder="Confirm your password" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <input type="checkbox" id="terms" name="terms" required style="margin-right: 8px;">
                            <label for="terms" style="margin-bottom: 0;">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> Create Account
                        </button>
                    </form>
                    
                    <div class="divider">or</div>
                    
                    <button class="btn btn-google">
                        <i class="fab fa-google"></i> Continue with Google
                    </button>
                    
                    <div class="form-footer">
                        Already have an account? <a href="#" id="show-login">Sign in</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle between login and signup forms
        document.getElementById('show-signup').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('login-form').style.display = 'none';
            document.getElementById('signup-form').style.display = 'block';
        });
        
        document.getElementById('show-login').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('signup-form').style.display = 'none';
            document.getElementById('login-form').style.display = 'block';
        });
        
        // Check if URL has #signup and show signup form
        if (window.location.hash === '#signup') {
            document.getElementById('login-form').style.display = 'none';
            document.getElementById('signup-form').style.display = 'block';
        }
        
        // Password toggle functionality
        document.getElementById('toggle-password').addEventListener('click', function() {
            const passwordInput = document.getElementById('login-password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
        
        document.getElementById('toggle-signup-password').addEventListener('click', function() {
            const passwordInput = document.getElementById('signup-password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
        
        // Password match validation
        const signupForm = document.querySelector('#signup-form form');
        if (signupForm) {
            signupForm.addEventListener('submit', function(e) {
                const password = document.getElementById('signup-password').value;
                const confirmPassword = document.getElementById('signup-confirm-password').value;
                
                if (password !== confirmPassword) {
                    e.preventDefault();
                    alert('Passwords do not match!');
                }
                
                if (password.length < 8) {
                    e.preventDefault();
                    alert('Password must be at least 8 characters long!');
                }
                
                if (!document.getElementById('terms').checked) {
                    e.preventDefault();
                    alert('You must agree to the terms and conditions!');
                }
            });
        }
    </script>
</body>
</html>