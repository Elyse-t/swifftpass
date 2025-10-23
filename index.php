<?php
session_start();
include('connection.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $error = "❌ Email and password are required.";
    } else {
        // First, check if user exists and get basic info
        $stmt = $conn->prepare("
            SELECT 
                id, firstname, lastname, email, password, role, contact, status
            FROM users 
            WHERE email = ? AND status = 'active'
        ");
        
        if (!$stmt) die("❌ Prepare failed: " . $conn->error);

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user   = $result->fetch_assoc();

        if ($user && $password === $user['password']) {
            // Create basic session for all users
            $_SESSION['user_id']    = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name']  = $user['firstname'] . ' ' . $user['lastname'];
            $_SESSION['user_role']  = $user['role'];
            $_SESSION['user_contact'] = $user['contact'];
            
            // Role-specific additional data
            if ($user['role'] === 'driver') {
                // Get driver ID only (we don't need bus info at login)
                $driver_stmt = $conn->prepare("
                    SELECT driver_id, name as driver_name, license
                    FROM drivers 
                    WHERE user_id = ?
                ");
                $driver_stmt->bind_param("i", $user['id']);
                $driver_stmt->execute();
                $driver_result = $driver_stmt->get_result();
                $driver_data = $driver_result->fetch_assoc();
                
                if ($driver_data) {
                    $_SESSION['driver_id'] = $driver_data['driver_id'];
                    $_SESSION['driver_name'] = $driver_data['driver_name'];
                    $_SESSION['driver_license'] = $driver_data['license'];
                    
                    // We'll load the assigned trips in the driver dashboard, not at login
                    // This makes login faster and simpler
                }
                $driver_stmt->close();
                
            } elseif ($user['role'] === 'passenger') {
                // Get passenger-specific data if needed
                $passenger_stmt = $conn->prepare("
                    SELECT customer_id, firstname, lastname, contact, email
                    FROM customers 
                    WHERE email = ?
                ");
                $passenger_stmt->bind_param("s", $email);
                $passenger_stmt->execute();
                $passenger_result = $passenger_stmt->get_result();
                $passenger_data = $passenger_result->fetch_assoc();
                
                if ($passenger_data) {
                    $_SESSION['customer_id'] = $passenger_data['customer_id'];
                }
                $passenger_stmt->close();
            }

            // Update last login
            $update_sql = "UPDATE users SET last_login = NOW() WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("i", $user['id']);
            $update_stmt->execute();

            // Role-based redirection
            switch ($user['role']) {
                case 'admin':
                    header("Location: admin.php");
                    exit;
                case 'driver':
                    header("Location: drivers_dashboard.php");
                    exit;
                case 'passenger':
                    header("Location: homepage.php");
                    exit;
                default:
                    header("Location: homepage.php");
                    exit;
            }
        } else {
            $error = "❌ Invalid email or password.";
        }
        $stmt->close();
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SwiftPass - Login | Secure Access</title>
  <meta name="description" content="Sign in to your SwiftPass account to book bus tickets and manage your trips.">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    :root {
      --primary-blue: #2563eb;
      --primary-dark: #1d4ed8;
      --secondary-blue: #3b82f6;
      --accent-cyan: #06b6d4;
      --text-primary: #1e293b;
      --text-secondary: #64748b;
      --text-light: #94a3b8;
      --surface: #ffffff;
      --background: #f8fafc;
      --border: #e2e8f0;
      --border-focus: #3b82f6;
      --success: #10b981;
      --error: #ef4444;
      --gradient-primary: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-dark) 100%);
      --gradient-secondary: linear-gradient(135deg, var(--secondary-blue) 0%, var(--accent-cyan) 100%);
      --gradient-bg: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
      --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
      --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
      --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
      --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
      --border-radius: 16px;
    }

    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
      background: var(--gradient-bg);
      color: var(--text-primary);
      line-height: 1.6;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1rem;
      position: relative;
      overflow: hidden;
    }

    /* Animated Background */
    .bg-decorations {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -1;
      overflow: hidden;
    }

    .bg-shape {
      position: absolute;
      border-radius: 50%;
      opacity: 0.08;
      animation: float 25s infinite ease-in-out;
    }

    .bg-shape:nth-child(1) {
      width: 300px;
      height: 300px;
      background: var(--gradient-primary);
      top: -150px;
      right: -150px;
      animation-delay: 0s;
    }

    .bg-shape:nth-child(2) {
      width: 200px;
      height: 200px;
      background: var(--gradient-secondary);
      bottom: -100px;
      left: -100px;
      animation-delay: -10s;
    }

    .bg-shape:nth-child(3) {
      width: 150px;
      height: 150px;
      background: linear-gradient(135deg, var(--accent-cyan), var(--secondary-blue));
      top: 50%;
      right: -75px;
      animation-delay: -20s;
    }

    @keyframes float {
      0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0.08; }
      33% { transform: translateY(-30px) rotate(120deg); opacity: 0.12; }
      66% { transform: translateY(15px) rotate(240deg); opacity: 0.06; }
    }

    /* Main Container */
    .login-container {
      width: 100%;
      max-width: 420px;
      position: relative;
      z-index: 2;
    }

    .login-card {
      background: var(--surface);
      border-radius: var(--border-radius);
      box-shadow: var(--shadow-xl);
      border: 1px solid rgba(255, 255, 255, 0.8);
      padding: 3rem 2rem;
      position: relative;
      overflow: hidden;
      backdrop-filter: blur(10px);
    }

    .login-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: var(--gradient-primary);
    }

    /* Logo Section */
    .logo-section {
      text-align: center;
      margin-bottom: 2.5rem;
    }

    .logo-container {
      display: inline-block;
      position: relative;
      padding: 3px;
      background: var(--gradient-primary);
      border-radius: 50%;
      margin-bottom: 1.5rem;
      animation: pulse 3s infinite;
    }

    .logo-inner {
      background: var(--surface);
      border-radius: 50%;
      padding: 1.2rem;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 80px;
      height: 80px;
    }

    .bus-logo {
      font-size: 2rem;
      background: var(--gradient-primary);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
    }

    @keyframes pulse {
      0%, 100% { box-shadow: var(--shadow-lg); }
      50% { box-shadow: var(--shadow-xl), 0 0 20px rgba(37, 99, 235, 0.3); }
    }

    .welcome-text {
      font-size: 1.75rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
      background: var(--gradient-primary);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
    }

    .subtitle {
      color: var(--text-secondary);
      font-size: 0.95rem;
    }

    /* Error Message */
    .alert {
      padding: 1rem;
      border-radius: 12px;
      margin-bottom: 1.5rem;
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .alert-danger {
      background: #fef2f2;
      border: 1px solid #fecaca;
      color: var(--error);
    }

    /* Form Styles */
    .form-group {
      margin-bottom: 1.5rem;
      position: relative;
    }

    .form-label {
      display: block;
      font-size: 0.875rem;
      font-weight: 600;
      color: var(--text-primary);
      margin-bottom: 0.5rem;
    }

    .form-input {
      width: 100%;
      padding: 0.875rem 1rem;
      border: 2px solid var(--border);
      border-radius: 12px;
      font-size: 1rem;
      transition: all 0.3s ease;
      background: var(--surface);
      color: var(--text-primary);
    }

    .form-input:focus {
      outline: none;
      border-color: var(--border-focus);
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
      transform: translateY(-1px);
    }

    .form-input::placeholder {
      color: var(--text-light);
    }

    /* Password Input */
    .password-container {
      position: relative;
    }

    .password-toggle {
      position: absolute;
      right: 1rem;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: var(--text-secondary);
      font-size: 1.1rem;
      transition: all 0.2s ease;
      z-index: 3;
    }

    .password-toggle:hover {
      color: var(--primary-blue);
    }

    .password-input {
      padding-right: 3rem;
    }

    /* Form Options */
    .form-options {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 2rem;
      font-size: 0.875rem;
    }

    .checkbox-container {
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .custom-checkbox {
      width: 18px;
      height: 18px;
      border: 2px solid var(--border);
      border-radius: 4px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .custom-checkbox.checked {
      background: var(--gradient-primary);
      border-color: var(--primary-blue);
    }

    .custom-checkbox i {
      color: white;
      font-size: 0.7rem;
      opacity: 0;
      transition: opacity 0.2s ease;
    }

    .custom-checkbox.checked i {
      opacity: 1;
    }

    .forgot-link {
      color: var(--primary-blue);
      text-decoration: none;
      font-weight: 500;
      transition: all 0.2s ease;
    }

    .forgot-link:hover {
      color: var(--primary-dark);
      text-decoration: underline;
    }

    /* Buttons */
    .btn {
      width: 100%;
      padding: 1rem;
      border: none;
      border-radius: 12px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      position: relative;
      overflow: hidden;
    }

    .btn-primary {
      background: var(--gradient-primary);
      color: white;
      box-shadow: var(--shadow-md);
      margin-bottom: 1.5rem;
    }

    .btn-primary:hover {
      background: var(--gradient-secondary);
      transform: translateY(-2px);
      box-shadow: var(--shadow-lg);
    }

    .btn-primary:active {
      transform: translateY(0);
    }

    .btn-primary::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
      transition: left 0.5s;
    }

    .btn-primary:hover::before {
      left: 100%;
    }

    .btn-google {
      background: var(--surface);
      color: var(--text-primary);
      border: 2px solid var(--border);
      margin-bottom: 2rem;
    }

    .btn-google:hover {
      border-color: var(--primary-blue);
      box-shadow: var(--shadow-md);
      transform: translateY(-1px);
    }

    .google-icon {
      width: 20px;
      height: 20px;
      border-radius: 2px;
    }

    /* Divider */
    .divider {
      display: flex;
      align-items: center;
      margin: 1.5rem 0;
    }

    .divider::before,
    .divider::after {
      content: '';
      flex: 1;
      height: 1px;
      background: var(--border);
    }

    .divider span {
      padding: 0 1rem;
      color: var(--text-secondary);
      font-size: 0.875rem;
      font-weight: 500;
    }

    /* Sign Up Link */
    .signup-link {
      text-align: center;
      font-size: 0.95rem;
      color: var(--text-secondary);
    }

    .signup-link a {
      color: var(--primary-blue);
      text-decoration: none;
      font-weight: 600;
      transition: color 0.2s ease;
    }

    .signup-link a:hover {
      color: var(--primary-dark);
      text-decoration: underline;
    }

    /* Loading State */
    .btn.loading {
      pointer-events: none;
      opacity: 0.7;
    }

    .btn.loading::after {
      content: '';
      width: 20px;
      height: 20px;
      border: 2px solid transparent;
      border-top: 2px solid currentColor;
      border-radius: 50%;
      animation: spin 1s linear infinite;
      margin-left: 0.5rem;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    /* Responsive Design */
    @media (max-width: 480px) {
      .login-card {
        padding: 2rem 1.5rem;
        margin: 1rem;
      }

      .welcome-text {
        font-size: 1.5rem;
      }

      .form-options {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
      }
    }
  </style>
</head>
<body>
  <!-- Animated Background -->
  <div class="bg-decorations">
    <div class="bg-shape"></div>
    <div class="bg-shape"></div>
    <div class="bg-shape"></div>
  </div>

  <div class="login-container">
    <div class="login-card">
      <!-- Logo Section -->
      <div class="logo-section">
        <div class="logo-container">
          <div class="logo-inner">
            <i class="fas fa-bus bus-logo"></i>
          </div>
        </div>
        <h1 class="welcome-text">Welcome Back</h1>
        <p class="subtitle">Sign in to continue your journey</p>
      </div>

      <!-- Error Message -->
      <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
          <i class="fas fa-exclamation-circle"></i>
          <?php echo $error; ?>
        </div>
      <?php endif; ?>

      <!-- Login Form -->
      <form method="POST" action="" novalidate>
        <!-- Email Field -->
        <div class="form-group">
          <label class="form-label" for="email">
            <i class="fas fa-envelope"></i> Email Address
          </label>
          <input 
            type="email" 
            id="email"
            name="email" 
            class="form-input"
            placeholder="Enter your email"
            required
            autocomplete="email"
            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
          >
        </div>

        <!-- Password Field -->
        <div class="form-group">
          <label class="form-label" for="password">
            <i class="fas fa-lock"></i> Password
          </label>
          <div class="password-container">
            <input 
              type="password" 
              id="password"
              name="password" 
              class="form-input password-input"
              placeholder="Enter your password"
              required
              autocomplete="current-password"
            >
            <i class="fas fa-eye password-toggle" id="togglePassword"></i>
          </div>
        </div>

        <!-- Form Options -->
        <div class="form-options">
          <div class="checkbox-container">
            <div class="custom-checkbox" id="rememberCheckbox">
              <i class="fas fa-check"></i>
            </div>
            <label for="remember">Remember me</label>
          </div>
          <a href="#" class="forgot-link">Forgot password?</a>
        </div>

        <!-- Sign In Button -->
        <button type="submit" class="btn btn-primary" id="signInBtn">
          <i class="fas fa-sign-in-alt"></i>
          Sign In
        </button>

        <!-- Divider -->
        <div class="divider">
          <span>OR</span>
        </div>

        <!-- Google Sign In -->
        <button type="button" class="btn btn-google" id="googleBtn">
          <img src="https://www.google.com/favicon.ico" alt="Google" class="google-icon">
          Continue with Google
        </button>

        <!-- Sign Up Link -->
        <div class="signup-link">
          New to SwiftPass? <a href="register.php">Create your account</a>
        </div>
      </form>
    </div>
  </div>

  <script>
    // DOM Elements
    const togglePassword = document.getElementById('togglePassword');
    const rememberCheckbox = document.getElementById('rememberCheckbox');
    const signInBtn = document.getElementById('signInBtn');
    const googleBtn = document.getElementById('googleBtn');

    // Password Toggle
    togglePassword.addEventListener('click', function() {
      const passwordInput = document.getElementById('password');
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      
      this.classList.toggle('fa-eye');
      this.classList.toggle('fa-eye-slash');
    });

    // Custom Checkbox
    rememberCheckbox.addEventListener('click', function() {
      this.classList.toggle('checked');
    });

    // Add entrance animation
    document.addEventListener('DOMContentLoaded', function() {
      const card = document.querySelector('.login-card');
      card.style.opacity = '0';
      card.style.transform = 'translateY(30px)';
      
      setTimeout(() => {
        card.style.transition = 'all 0.6s ease';
        card.style.opacity = '1';
        card.style.transform = 'translateY(0)';
      }, 100);
    });
  </script>
</body>
</html>