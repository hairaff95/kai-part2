<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP — KAI Tracker App</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #FFFFFF;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #000000;
            -webkit-font-smoothing: antialiased;
        }
        .email-wrapper {
            width: 100%;
            background-color: #FFFFFF;
            padding: 32px 16px;
            box-sizing: border-box;
        }
        .email-card {
            max-width: 480px;
            margin: 0 auto;
            background-color: #FFFFFF;
            border: 1.5px solid #1E293B;
            border-radius: 20px;
            padding: 40px 28px 36px 28px;
            text-align: center;
            box-sizing: border-box;
        }
        .illustration-wrap {
            margin: 0 auto 28px auto;
            text-align: center;
            line-height: 0;
        }
        .illustration-wrap svg {
            width: 125px;
            height: 125px;
            display: inline-block;
        }
        .main-heading {
            font-size: 28px;
            font-weight: 800;
            color: #000000;
            margin: 0 0 14px 0;
            letter-spacing: -0.5px;
            line-height: 1.25;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        .sub-heading {
            font-size: 13.5px;
            color: #4B5563;
            line-height: 1.5;
            margin: 0 auto 30px auto;
            max-width: 430px;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        .otp-display {
            font-size: 30px;
            font-weight: 800;
            color: #000000;
            margin: 0 0 30px 0;
            letter-spacing: 16px;
            padding-left: 16px;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1;
        }
        .expiry-note {
            font-size: 13.5px;
            color: #6B7280;
            margin: 0 0 30px 0;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        .expiry-note strong {
            color: #0066FF;
            font-weight: 700;
        }
        .security-warning {
            font-size: 12.5px;
            color: #8C929D;
            line-height: 1.55;
            margin: 0 auto 36px auto;
            max-width: 430px;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        .footer-logo {
            text-align: center;
            margin: 0 auto;
            line-height: 0;
        }
        .footer-logo svg {
            width: 145px;
            height: auto;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-card">
            
            {{-- Top Envelope Illustration --}}
            <div class="illustration-wrap">
                <x-icon name="asset-status-request-mail-otp" class="w-[125px] h-[125px]" width="125" height="125" />
            </div>

            {{-- Title: Verifikasi Email Anda --}}
            <h1 class="main-heading">Verifikasi Email Anda</h1>

            {{-- Subtitle --}}
            <p class="sub-heading">
                Masukan kode verifikasi berikut untuk menyelesaikan<br>proses verifikasi permintaan.
            </p>

            {{-- OTP Code Display: Spaced Digits --}}
            <div class="otp-display">{{ implode(' ', str_split($otp ?? '123456')) }}</div>

            {{-- Expiry Notice --}}
            <p class="expiry-note">
                Kode ini berlaku selama <strong>2 menit.</strong>
            </p>

            {{-- Security Notice --}}
            <p class="security-warning">
                Jangan bagikan kode ini kepada siapa pun, termasuk pihak<br>yang mengatasnamakan layanan.
            </p>

            {{-- Footer KAI Tracker App Logo --}}
            <div class="footer-logo">
                <x-icon name="asset-logo-mail-otp" class="w-[145px] h-auto" width="145" height="35" />
            </div>

        </div>
    </div>
</body>
</html>
