<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تأكيد الطلب</title>
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

        ul {
            list-style-type: none;
            padding: 0;
        }

        ul li {
            font-size: 16px;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }

        .order-details {
            background-color: #fafafa;
            padding: 10px;
            margin: 20px 0;
            border-radius: 8px;
        }

        .order-summary {
            margin-top: 20px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #27ae60;
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
        <p>شكراً لثقتك في Ecowin!</p>

        <div class="order-details">
            <p>تفاصيل الطلب الخاص بك:</p>
            <ul>
                @foreach ($order->items as $item)
                    <li><strong>اسم المنتج:</strong> {{ $item->product->{'name_' . app()->getLocale()} }} <br>
                        <strong>الكمية:</strong> {{ $item->quantity }} <br>
                        <strong>السعر/النقاط:</strong> {{ $item->total_price }}</li>
                @endforeach
            </ul>
        </div>

        <div class="order-summary">
            <p><strong>إجمالي النقاط: </strong>{{ $order->points }}</p>
            <p>سيصلك مندوبنا قريبًا.</p>
        </div>

        <div class="footer">
            <p>إذا كانت لديك أي استفسارات، لا تتردد في <a href="mailto:support@ecowin.com">التواصل معنا</a>.</p>
        </div>
    </div>
</body>
</html>
