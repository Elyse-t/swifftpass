

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SwiftPass - Create Account | Join Today</title>
  <meta name="description" content="Create your SwiftPass account to book bus tickets easily and manage your travel plans.">
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
      --warning: #f59e0b;
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
      padding: 2rem 1rem;
      position: relative;
      overflow-x: hidden;
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
      animation: float 30s infinite ease-in-out;
    }

    .bg-shape:nth-child(1) {
      width: 400px;
      height: 400px;
      background: var(--gradient-primary);
      top: -200px;
      right: -200px;
      animation-delay: 0s;
    }

    .bg-shape:nth-child(2) {
      width: 250px;
      height: 250px;
      background: var(--gradient-secondary);
      bottom: -125px;
      left: -125px;
      animation-delay: -15s;
    }

    .bg-shape:nth-child(3) {
      width: 180px;
      height: 180px;
      background: linear-gradient(135deg, var(--accent-cyan), var(--secondary-blue));
      top: 40%;
      right: -90px;
      animation-delay: -25s;
    }

    @keyframes float {
      0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0.08; }
      33% { transform: translateY(-40px) rotate(120deg); opacity: 0.12; }
      66% { transform: translateY(20px) rotate(240deg); opacity: 0.06; }
    }

    /* Main Container */
    .register-container {
      width: 100%;
      max-width: 520px;
      position: relative;
      z-index: 2;
    }

    .register-card {
      background: var(--surface);
      border-radius: var(--border-radius);
      box-shadow: var(--shadow-xl);
      border: 1px solid rgba(255, 255, 255, 0.8);
      overflow: hidden;
      position: relative;
      backdrop-filter: blur(10px);
    }

    .register-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: var(--gradient-primary);
    }

    /* Header Section */
    .header-section {
      background: var(--gradient-primary);
      color: white;
      text-align: center;
      padding: 2rem;
      position: relative;
      overflow: hidden;
    }

    .header-section::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -20%;
      width: 150px;
      height: 150px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 50%;
      transform: rotate(45deg);
    }

    .logo-container {
      display: inline-block;
      position: relative;
      padding: 3px;
      background: rgba(255, 255, 255, 0.2);
      border-radius: 50%;
      margin-bottom: 1rem;
      z-index: 2;
    }

    .logo-inner {
      background: var(--surface);
      border-radius: 50%;
      padding: 1rem;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 70px;
      height: 70px;
    }

    .bus-logo {
      font-size: 1.8rem;
      background: var(--gradient-primary);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
    }

    .header-title {
      font-size: 1.75rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
      position: relative;
      z-index: 2;
    }

    .header-subtitle {
      opacity: 0.9;
      font-size: 1rem;
      position: relative;
      z-index: 2;
    }

    /* Form Section */
    .form-section {
      padding: 2.5rem;
    }

    .form-group {
      margin-bottom: 1.5rem;
      position: relative;
    }

    .form-group.half {
      width: calc(50% - 0.5rem);
      display: inline-block;
    }

    .form-group.half:first-child {
      margin-right: 1rem;
    }

    .form-row {
      display: flex;
      gap: 1rem;
      margin-bottom: 1.5rem;
    }

    .form-row .form-group {
      flex: 1;
      margin-bottom: 0;
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

    /* Password Fields */
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

    /* Password Strength Meter */
    .password-strength {
      height: 4px;
      background: var(--border);
      margin-top: 0.5rem;
      border-radius: 2px;
      overflow: hidden;
      transition: all 0.3s ease;
    }

    .strength-bar {
      height: 100%;
      width: 0%;
      background: var(--error);
      transition: all 0.3s ease;
      border-radius: 2px;
    }

    .strength-text {
      font-size: 0.75rem;
      margin-top: 0.25rem;
      font-weight: 500;
    }

    .strength-weak { color: var(--error); }
    .strength-fair { color: var(--warning); }
    .strength-good { color: var(--success); }
    .strength-strong { color: var(--success); }

    .password-requirements {
      font-size: 0.8rem;
      color: var(--text-secondary);
      margin-top: 0.5rem;
      line-height: 1.4;
    }

    /* Checkbox */
    .checkbox-container {
      display: flex;
      align-items: flex-start;
      gap: 0.75rem;
      margin-bottom: 2rem;
    }

    .custom-checkbox {
      width: 20px;
      height: 20px;
      border: 2px solid var(--border);
      border-radius: 4px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.2s ease;
      flex-shrink: 0;
      margin-top: 2px;
    }

    .custom-checkbox.checked {
      background: var(--gradient-primary);
      border-color: var(--primary-blue);
    }

    .custom-checkbox i {
      color: white;
      font-size: 0.75rem;
      opacity: 0;
      transition: opacity 0.2s ease;
    }

    .custom-checkbox.checked i {
      opacity: 1;
    }

    .checkbox-label {
      font-size: 0.9rem;
      color: var(--text-secondary);
      line-height: 1.4;
    }

    .checkbox-label a {
      color: var(--primary-blue);
      text-decoration: none;
      font-weight: 500;
    }

    .checkbox-label a:hover {
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

    /* Sign In Link */
    .signin-link {
      text-align: center;
      font-size: 0.95rem;
      color: var(--text-secondary);
    }

    .signin-link a {
      color: var(--primary-blue);
      text-decoration: none;
      font-weight: 600;
      transition: color 0.2s ease;
    }

    .signin-link a:hover {
      color: var(--primary-dark);
      text-decoration: underline;
    }

    /* Form Validation */
    .form-input.error {
      border-color: var(--error);
      animation: shake 0.3s ease-in-out;
    }

    .form-input.success {
      border-color: var(--success);
    }

    .form-input.warning {
      border-color: var(--warning);
    }

    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      25% { transform: translateX(-5px); }
      75% { transform: translateX(5px); }
    }

    .error-message {
      color: var(--error);
      font-size: 0.8rem;
      margin-top: 0.5rem;
      display: none;
      align-items: center;
      gap: 0.25rem;
    }

    .error-message.show {
      display: flex;
    }

    .success-message {
      color: var(--success);
      font-size: 0.8rem;
      margin-top: 0.5rem;
      display: none;
      align-items: center;
      gap: 0.25rem;
    }

    .success-message.show {
      display: flex;
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
    @media (max-width: 768px) {
      body {
        padding: 1rem;
      }

      .form-section {
        padding: 2rem 1.5rem;
      }

      .form-row {
        flex-direction: column;
        gap: 0;
      }

      .form-row .form-group {
        margin-bottom: 1.5rem;
      }

      .header-section {
        padding: 1.5rem;
      }

      .header-title {
        font-size: 1.5rem;
      }
    }

    @media (max-width: 480px) {
      .register-container {
        margin: 0;
      }

      .form-section {
        padding: 1.5rem 1rem;
      }

      .header-section {
        padding: 1.25rem;
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

  <div class="register-container">
    <div class="register-card">
      <!-- Header Section -->
      <div class="header-section">
        <div class="logo-container">
          <div class="logo-inner">
            <i class="fas fa-bus bus-logo"></i>
          </div>
        </div>
        <h1 class="header-title">Create Your Account</h1>
        <p class="header-subtitle">Join SwiftPass and start your journey</p>
      </div>

      <!-- Form Section -->
      <div class="form-section">
        <form method="POST" action="" novalidate>

          <!-- Name Fields -->
          <div class="form-row">
            <div class="form-group">
              <label class="form-label" name="firstName">
                <i class="fas fa-user"></i> First Name
              </label>
              <input 
                type="text" 
                id="firstName"
                name="firstName"   
                class="form-input"
                placeholder="Enter first name"
                required
                autocomplete="given-name"
              >
              <div class="error-message" id="firstNameError">
                <i class="fas fa-exclamation-circle"></i>
                <span>First name is required</span>
              </div>
            </div>

            <div class="form-group">
              <label class="form-label" name="lastName">
                <i class="fas fa-user"></i> Last Name
              </label>
              <input 
                type="text" 
                id="lastName"
                 name="lastName"   
                class="form-input"
                placeholder="Enter last name"
                required
                autocomplete="family-name"
              >
              <div class="error-message" id="lastNameError">
                <i class="fas fa-exclamation-circle"></i>
                <span>Last name is required</span>
              </div>
            </div>
          </div>

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
            >
            <div class="error-message" id="emailError">
              <i class="fas fa-exclamation-circle"></i>
              <span>Please enter a valid email address</span>
  </div>
          <!-- Phone Field -->
          <div class="form-group">
            <label class="form-label" name="contact">
              <i class="fas fa-phone"></i> Phone Number
            </label>
            <input 
              type="tel" 
              id="phone"
              name="contact"   
              class="form-input"
              placeholder="Enter phone number"
              required
              autocomplete="tel"
            >
            <div class="error-message" id="phoneError">
              <i class="fas fa-exclamation-circle"></i>
              <span>Please enter a valid phone number</span>
            </div>
          </div>

          <!-- Password Field -->
          <div class="form-group">
            <label class="form-label" name="password">
              <i class="fas fa-lock"></i> Password
            </label>
            <div class="password-container">
              <input 
                type="password" 
                id="password"
                name="password"   
                class="form-input password-input"
                placeholder="Create a strong password"
                required
                autocomplete="new-password"
              >
              <i class="fas fa-eye password-toggle" id="togglePassword"></i>
            </div>
            <div class="password-strength" id="strengthMeter">
              <div class="strength-bar" id="strengthBar"></div>
            </div>
            <div class="strength-text" id="strengthText"></div>
            <div class="password-requirements">
              Use 8+ characters with a mix of letters, numbers & symbols
            </div>
            <div class="error-message" id="passwordError">
              <i class="fas fa-exclamation-circle"></i>
              <span>Password must meet requirements</span>
            </div>
          </div>

          <!-- Confirm Password Field -->
          <div class="form-group">
            <label class="form-label" name="confirmPassword">
              <i class="fas fa-lock"></i> Confirm Password
            </label>
            <div class="password-container">
              <input 
                type="password" 
                id="confirmPassword"
                name="confirmPassword"   
                class="form-input password-input"
                placeholder="Confirm your password"
                required
                autocomplete="new-password"
              >
              <i class="fas fa-eye password-toggle" id="toggleConfirmPassword"></i>
            </div>
            <div class="error-message" id="confirmPasswordError">
              <i class="fas fa-exclamation-circle"></i>
              <span>Passwords do not match</span>
            </div>
            <div class="success-message" id="confirmPasswordSuccess">
              <i class="fas fa-check-circle"></i>
              <span>Passwords match</span>
            </div>
          </div>

          <!-- Terms Checkbox -->
          <div class="checkbox-container">
            <div class="custom-checkbox" id="termsCheckbox">
              <i class="fas fa-check"></i>
            </div>
            <label class="checkbox-label" for="terms">
              I agree to SwiftPass <a href="#" target="_blank">Terms of Service</a> 
              and <a href="#" target="_blank">Privacy Policy</a>
            </label>
          </div>

          <!-- Create Account Button -->
          <button type="submit" class="btn btn-primary" id="createAccountBtn">
            <i class="fas fa-user-plus"></i>
            Create Account
          </button>

          <!-- Divider -->
          <div class="divider">
            <span>OR CONTINUE WITH</span>
          </div>

          <!-- Google Sign Up -->
         <a href="https://accounts.google.com/signin" target="_blank">
  <button type="button" class="btn btn-google" id="googleSignUpBtn">
    <img src="https://www.google.com/favicon.ico" alt="Google" class="google-icon">
    Sign up with Google
  </button>
</a>


          <!-- Sign In Link -->
          <div class="signin-link">
            Already have an account? <a href="index.php">Sign in here</a>
          </div>
          
        </form>
   <?php
include('connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first   = trim($_POST['firstName'] ?? '');
    $last    = trim($_POST['lastName'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $phone   = trim($_POST['contact'] ?? '');
    $pass    = trim($_POST['password'] ?? '');
    $confirm = trim($_POST['confirmPassword'] ?? '');

    // Password match check
    if ($pass !== $confirm) {
        die("❌ Passwords do not match.");
    }

    // Check if email already exists
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        die("❌ Email already registered.");
    }
    $check->close();

    // Plain-text password
    $passwordToStore = $pass;

    // Default role
    $role = 'user';

    // Insert into database
    $sql = "INSERT INTO users (firstName, lastName, email, contact, password, role) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) die("Prepare failed: " . $conn->error);

    $stmt->bind_param("ssssss", $first, $last, $email, $phone, $passwordToStore, $role);

    if ($stmt->execute()) {
        echo "✅ Registration successful!";
    } else {
        echo "❌ Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

      </div>
    </div>
  </div>

  <script>
    // DOM Elements
    const form = document.getElementById('registerForm');
    const inputs = {
      firstName: document.getElementById('firstName'),
      lastName: document.getElementById('lastName'),
      email: document.getElementById('email'),
      contact: document.getElementById('contact'),
      password: document.getElementById('password'),
      confirmPassword: document.getElementById('confirmPassword')
    };
    
    const toggles = {
      password: document.getElementById('togglePassword'),
      confirmPassword: document.getElementById('toggleConfirmPassword')
    };
    
    const termsCheckbox = document.getElementById('termsCheckbox');
    const createAccountBtn = document.getElementById('createAccountBtn');
    const googleSignUpBtn = document.getElementById('googleSignUpBtn');

    // Password Toggle Functions
    function setupPasswordToggle(toggleId, inputId) {
      const toggle = document.getElementById(toggleId);
      const input = document.getElementById(inputId);
      
      toggle.addEventListener('click', function() {
        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
        input.setAttribute('type', type);
        
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
      });
    }

    setupPasswordToggle('togglePassword', 'password');
    setupPasswordToggle('toggleConfirmPassword', 'confirmPassword');

    // Terms Checkbox
    termsCheckbox.addEventListener('click', function() {
      this.classList.toggle('checked');
    });

    // Password Strength Checker
    function checkPasswordStrength(password) {
      const strengthBar = document.getElementById('strengthBar');
      const strengthText = document.getElementById('strengthText');
      
      let score = 0;
      let feedback = '';
      
      // Length check
      if (password.length >= 8) score += 1;
      else feedback = 'Too short';
      
      // Character variety checks
      if (password.match(/[a-z]/)) score += 1;
      if (password.match(/[A-Z]/)) score += 1;
      if (password.match(/[0-9]/)) score += 1;
      if (password.match(/[^a-zA-Z0-9]/)) score += 1;
      
      // Update strength bar and text
      const colors = ['#ef4444', '#f59e0b', '#10b981', '#10b981', '#10b981'];
      const labels = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
      const classes = ['strength-weak', 'strength-weak', 'strength-fair', 'strength-good', 'strength-strong'];
      
      const width = Math.max(0, (score / 5) * 100);
      strengthBar.style.width = width + '%';
      strengthBar.style.background = colors[Math.max(0, score - 1)];
      
      strengthText.textContent = password.length > 0 ? labels[Math.max(0, score - 1)] : '';
      strengthText.className = `strength-text ${classes[Math.max(0, score - 1)]}`;
      
      return score >= 3;
    }

    // Validation Functions
    function validateEmail(email) {
      const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return re.test(email);
    }

    function validatePhone(contact) {
      const re = /^[\+]?[\d\s\-\(\)]{10,}$/;
      return re.test(contact.replace(/\s/g, ''));
    }

    function showError(inputId, message) {
      const input = inputs[inputId];
      const errorDiv = document.getElementById(inputId + 'Error');
      const successDiv = document.getElementById(inputId + 'Success');
      
      input.classList.add('error');
      input.classList.remove('success');
      errorDiv.classList.add('show');
      if (successDiv) successDiv.classList.remove('show');
    }

    function showSuccess(inputId, message = '') {
      const input = inputs[inputId];
      const errorDiv = document.getElementById(inputId + 'Error');
      const successDiv = document.getElementById(inputId + 'Success');
      
      input.classList.add('success');
      input.classList.remove('error');
      errorDiv.classList.remove('show');
      if (successDiv) successDiv.classList.add('show');
    }

    function clearValidation(inputId) {
      const input = inputs[inputId];
      const errorDiv = document.getElementById(inputId + 'Error');
      const successDiv = document.getElementById(inputId + 'Success');
      
      input.classList.remove('error', 'success');
      errorDiv.classList.remove('show');
      if (successDiv) successDiv.classList.remove('show');
    }

    // Real-time Validation
    inputs.firstName.addEventListener('blur', function() {
      if (this.value.trim().length < 2) {
        showError('firstName', 'First name must be at least 2 characters');
      } else {
        showSuccess('firstName');
      }
    });

    inputs.lastName.addEventListener('blur', function() {
      if (this.value.trim().length < 2) {
        showError('lastName', 'Last name must be at least 2 characters');
      } else {
        showSuccess('lastName');
      }
    });

    inputs.email.addEventListener('blur', function() {
      if (!validateEmail(this.value)) {
        showError('email', 'Please enter a valid email address');
      } else {
        showSuccess('email');
      }
    });

    inputs.phone.addEventListener('blur', function() {
      if (!validatePhone(this.value)) {
        showError('contact', 'Please enter a valid phone number');
      } else {
        showSuccess('contact');
      }
    });

    inputs.password.addEventListener('input', function() {
      const isStrong = checkPasswordStrength(this.value);
      if (this.value.length > 0) {
        if (isStrong) {
          showSuccess('password');
        } else {
          clearValidation('password');
        }
      }
      
      // Check confirm password if it has value
      if (inputs.confirmPassword.value) {
        validateConfirmPassword();
      }
    });

    function validateConfirmPassword() {
      if (inputs.confirmPassword.value !== inputs.password.value) {
        showError('confirmPassword', 'Passwords do not match');
        return false;
      } else if (inputs.confirmPassword.value.length > 0) {
        showSuccess('confirmPassword');
        return true;
      }
      return false;
    }

    inputs.confirmPassword.addEventListener('blur', validateConfirmPassword);

    // Clear validation on input
    Object.keys(inputs).forEach(key => {
      inputs[key].addEventListener('input', function() {
        clearValidation(key);
      });
    });