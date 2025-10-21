<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dev Coffee - Confirmação</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #2d1b14; background: #f8f4f0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .header { background: #6d4c41; color: #fff5e6; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 30px; text-align: center; }
        .success-icon { width: 80px; height: 80px; background: #8d6e63; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: white; font-size: 30px; }
        .btn { display: inline-block; background: #6d4c41; color: white; padding: 12px 25px; border-radius: 25px; text-decoration: none; margin: 15px 0; }
        .footer { background: #3e2723; color: #fff5e6; padding: 20px; text-align: center; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>☕ Dev Coffee</h1>
            <p>Caçapava - SP</p>
        </div>
        
        <div class="content">
            <div class="success-icon">✅</div>
            <h2>Mensagem Recebida!</h2>
            <p>Olá, <strong>{NOME}</strong>!<br>
            Recebemos sua mensagem e entraremos em contato em breve! ☕</p>
            
            <div style="background: #fff8e1; padding: 20px; border-radius: 10px; margin: 20px 0; border-left: 4px solid #6d4c41;">
                <p><strong>📧 Respondemos em até 24h</strong></p>
                <p>📱 Via WhatsApp: {TELEFONE}</p>
            </div>
            
            <a href="https://devcoffee.com.br" class="btn">Ver Cardápio</a>
        </div>
        
        <div class="footer">
            <p><strong>Dev Coffee ☕</strong> | (12) 99999-9999</p>
        </div>
    </div>
</body>
</html>