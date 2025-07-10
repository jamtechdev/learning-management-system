<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Your Email - QTN Vault</title>
    <style>
        body {
            font-family: Comic Sans MS, Arial, sans-serif;
            background-color: #fdf6e3;
            color: #333;
            padding: 20px;
            text-align: center;
        }
        .container {
            background: #f3e8ff;
            border: 3px dashed #ffcc00;
            border-radius: 20px;
            padding: 30px;
            max-width: 500px;
            margin: 0 auto;
            box-shadow: 5px 5px 15px rgba(0,0,0,0.1);
        }
        h1 {
            color: #0671b6;
            font-size: 28px;
        }
        p {
            font-size: 18px;
            color: #444;
        }
        .btn {
            display: inline-block;
            padding: 15px 25px;
            margin-top: 20px;
            background-color: #00c9a7;
            color: white;
            font-weight: bold;
            text-decoration: none;
            border-radius: 12px;
            font-size: 18px;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #00b89c;
        }
        .highlight {
            color: #fa9837;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            font-size: 14px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
         <h1>🎉 Welcome to <span class="highlight">QTN Vault</span>! 🎉</h1>
        <p>Hi there! We're super excited to have you join us!</p>
        <p>Before we start the fun, please verify your email address:</p>
        <a href="{{ $verificationUrl }}" class="btn">🔒 Verify My Email</a>
        <div class="footer">
            If you didn’t sign up, no worries – just ignore this email. 😊
        </div>
    </div>
</body>
</html>
