<?php

require "../core/core.php";
require_once "../controllers/CustomerController.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - Shoppn</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="register-page">
    <div class="register-card">

        <a href="../index.php" class="back">&larr;</a>

        <div class="register-layout">

            <div class="register-art">
                <svg viewBox="0 0 360 290" xmlns="http://www.w3.org/2000/svg">
                    <ellipse cx="180" cy="165" rx="170" ry="115" fill="#e6f6f4"/>
                    <line x1="10" y1="270" x2="350" y2="270" stroke="#b9e4e1" stroke-width="3" stroke-linecap="round"/>

                    <line x1="24" y1="70" x2="24" y2="270" stroke="#0F9B8E" stroke-width="3"/>
                    <line x1="256" y1="70" x2="256" y2="270" stroke="#0F9B8E" stroke-width="3"/>
                    <line x1="24" y1="70" x2="256" y2="70" stroke="#0F9B8E" stroke-width="3"/>

                    <path d="M70 70 L70 64 M70 70 L50 84 L90 84 Z" fill="none" stroke="#0F9B8E" stroke-width="2.5" stroke-linejoin="round"/>
                    <path d="M56 86 L62 86 L66 98 L74 98 L78 86 L84 86 L88 130 L52 130 Z" fill="#ffffff" stroke="#0F9B8E" stroke-width="2.5" stroke-linejoin="round"/>

                    <path d="M112 70 L112 64 M112 70 L96 82 L128 82 Z" fill="none" stroke="#0F9B8E" stroke-width="2.5" stroke-linejoin="round"/>

                    <path d="M205 70 L205 64 M205 70 L186 84 L224 84 Z" fill="none" stroke="#0F9B8E" stroke-width="2.5" stroke-linejoin="round"/>
                    <path d="M190 88 L168 100 L174 132 L186 126 L186 158 L226 158 L226 126 L238 132 L244 100 L222 88 Z" fill="#6cc5c0" stroke="#0c7d72" stroke-width="2.5" stroke-linejoin="round"/>
                    <circle cx="198" cy="108" r="2.5" fill="#ffffff"/>
                    <circle cx="214" cy="116" r="2.5" fill="#ffffff"/>
                    <circle cx="200" cy="132" r="2.5" fill="#ffffff"/>
                    <circle cx="216" cy="144" r="2.5" fill="#ffffff"/>

                    <line x1="140" y1="240" x2="140" y2="266" stroke="#f3c9a8" stroke-width="6" stroke-linecap="round"/>
                    <line x1="160" y1="240" x2="160" y2="266" stroke="#f3c9a8" stroke-width="6" stroke-linecap="round"/>
                    <rect x="133" y="264" width="15" height="6" rx="3" fill="#0c7d72"/>
                    <rect x="153" y="264" width="15" height="6" rx="3" fill="#0c7d72"/>

                    <path d="M128 190 L172 190 L192 242 L108 242 Z" fill="#ffffff" stroke="#6cc5c0" stroke-width="3" stroke-linejoin="round"/>
                    <line x1="110" y1="236" x2="190" y2="236" stroke="#b9e4e1" stroke-width="3"/>

                    <path d="M130 144 Q150 136 170 144 L172 192 L128 192 Z" fill="#6cc5c0"/>

                    <polyline points="168,148 186,122 196,96" fill="none" stroke="#f3c9a8" stroke-width="7" stroke-linecap="round" stroke-linejoin="round"/>
                    <line x1="132" y1="150" x2="126" y2="192" stroke="#f3c9a8" stroke-width="7" stroke-linecap="round"/>

                    <rect x="145" y="132" width="10" height="12" fill="#f3c9a8"/>
                    <path d="M136 104 Q150 82 164 104 Q172 140 160 176 L140 176 Q128 140 136 104 Z" fill="#0c7d72"/>
                </svg>
            </div>

            <div class="register-form-side">
                <h2>Create Account</h2>
                <p class="subtitle">Enter your details to get started</p>

                <?php if (!empty($_SESSION['error'])): ?>
                    <p style="color:red;">
                        <?= htmlspecialchars($_SESSION['error']) ?>
                    </p>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <form id="register-form" action="../actions/register_action.php" method="POST">

                    <label>Full Name</label><br>
                    <input type="text" name="name" id="name" placeholder="Enter your full name"><br>
                    <span class="error-text" id="name-error"></span><br>

                    <label>Email</label><br>
                    <input type="email" name="email" id="email" placeholder="Enter your email"><br>
                    <span class="error-text" id="email-error"></span><br>

                    <div class="pass-wrap">
                        <input type="password" name="pass" id="pass" placeholder="Create a password">
                        <button type="button" class="toggle-pass">Show</button>
                    </div>
                    <span class="error-text" id="pass-error"></span><br>

                    <div class="pass-wrap">
                        <input type="password" name="confirm_pass" id="confirm_pass" placeholder="Re-enter your password">
                        <button type="button" class="toggle-pass">Show</button>
                    </div>
                    <span class="error-text" id="confirm-error"></span><br>
                    
                    <label>Country</label><br>
                    <select name="country" id="country">
                        <option value="Ghana">Ghana</option>
                        <option value="Nigeria">Nigeria</option>
                        <option value="Other">Other</option>
                    </select><br><br>

                    <label>City</label><br>
                    <input type="text" name="city" id="city" placeholder="Enter your city"><br><br>

                    <label>Contact Number</label><br>
                    <input type="text" name="contact" id="contact" placeholder="Enter your phone number"><br>
                    <span class="error-text" id="contact-error"></span><br>

                    <button type="submit" id="submit-btn">Sign Up</button>

                </form>

                <p class="login-link">
                    Already have an account? <a href="login.php">Login</a>
                </p>
            </div>

        </div>
    </div>
</div>

<script src="../js/validate.js"></script>

</body>
</html>