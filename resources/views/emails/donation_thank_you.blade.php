<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>شكراً لتبرعك</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            direction: rtl;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 24px;
            color: #2c3e50;
            text-align: center;
        }

        p {
            font-size: 16px;
            color: #555;
            text-align: center;
        }

        .footer {
            font-size: 14px;
            color: #999;
            text-align: center;
            margin-top: 30px;
        }

        .footer a {
            color: #27ae60;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>مرحباً {{ $userName }}</h1>
        <p>شكراً لتبرعك! تبرعك سيساعد العديد من الأشخاص في تحسين حياتهم.</p>
        <p>سيقوم المندوب بزيارة عنوانك قريباً لاستلام التبرع.</p>
        <p><strong>تفاصيل التبرع:</strong></p>
        <ul>
            <li><strong>عدد القطع:</strong> {{ $donation->pieces }}</li>
            <li><strong>الوصف:</strong> {{ $donation->description }}</li>
        </ul>
        <div class="footer">
            <p>إذا كانت لديك أي استفسارات، لا تتردد في <a href="mailto:support@ecowin.com">التواصل معنا</a>.</p>
        </div>
    </div>
</body>
</html>
