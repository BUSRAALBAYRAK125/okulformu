<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Gönderine yeni yorum geldi</title>
</head>
<body style="margin:0; padding:24px; background:#f4f7fb; font-family:Arial, sans-serif; color:#0f172a;">
    <div style="max-width:640px; margin:0 auto; background:#ffffff; border:1px solid #d8e1eb; border-radius:16px; overflow:hidden;">
        <div style="background:#10213f; color:#ffffff; padding:20px 24px;">
            <h1 style="margin:0; font-size:24px; line-height:1.2;">Gönderine yeni yorum geldi</h1>
        </div>

        <div style="padding:24px;">
            <p style="margin:0 0 14px; font-size:15px; line-height:1.7;">
                Merhaba {{ $receiver->name }},
            </p>

            <p style="margin:0 0 14px; font-size:15px; line-height:1.7;">
                <strong>{{ $sender->name }} {{ $sender->surname }}</strong> gönderine yorum yaptı.
            </p>

            <p style="margin:0 0 14px; font-size:15px; line-height:1.7;">
                <strong>Gönderi:</strong> {{ $post->title }}
            </p>

            <p style="margin:0 0 22px; font-size:15px; line-height:1.7; color:#475569;">
                Yorumu görmek ve gönderiye gitmek için aşağıdaki butonu kullanabilirsin.
            </p>

            <a href="{{ $postUrl }}"
               style="display:inline-block; padding:12px 18px; background:#10213f; color:#ffffff; text-decoration:none; border-radius:10px; font-weight:700;">
                Gönderiye Git
            </a>
        </div>
    </div>
</body>
</html>