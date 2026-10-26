<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Anda Telah Dibuat</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
            background-color: #f4f4f9;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #eeeeee;
        }
        .header h1 {
            color: #3d48e0;
            margin: 0;
        }
        .content {
            padding: 20px 0;
        }
        .content p {
            margin: 0 0 10px;
        }
        .credentials {
            background-color: #f0f2ff;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #3d48e0;
        }
        .credentials strong {
            display: block;
            margin-bottom: 5px;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #777;
            padding-top: 20px;
            border-top: 1px solid #eeeeee;
        }
        .button {
            display: inline-block;
            padding: 12px 25px;
            margin-top: 20px;
            background-color: #3d48e0;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .button:hover {
            background-color: #3640c8;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Selamat Datang di Sistem Helpdesk</h1>
        </div>
        <div class="content">
            <p>Halo, <strong>{{ $user->name }}</strong>,</p>
            <p>Akun Anda sebagai Teknisi telah berhasil dibuat. Anda dapat login ke sistem menggunakan kredensial di bawah ini.</p>
            
            <div class="credentials">
                <strong>Email:</strong> {{ $user->email }}<br>
                <strong>Password Sementara:</strong> {{ $plainPassword }}
            </div>

            <p>Untuk keamanan, kami sangat menyarankan Anda untuk segera mengganti password setelah login pertama kali.</p>

            <div style="text-align: center;">
                <a href="{{ route('login') }}" class="button">Login ke Sistem</a>
            </div>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Sistem Helpdesk. Semua hak cipta dilindungi.</p>
        </div>
    </div>
</body>
</html>
