<?php

// Ensure PHPMailer files are loaded when using the bundled library (no Composer)
$phBase = __DIR__ . '/PHPMailer/src/';
if (!class_exists('PHPMailer\\PHPMailer\\PHPMailer') && file_exists($phBase . 'PHPMailer.php')) {
    require_once $phBase . 'Exception.php';
    require_once $phBase . 'PHPMailer.php';
    require_once $phBase . 'SMTP.php';
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class MailHelper
{

    static function sendEmail($to, $subject, $body)
    {
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.zoho.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'qkhanh282@zohomail.com';
            $mail->Password   = 'pq99TwAfy14P'; // App Password Zoho Mail
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            //Recipients
            $mail->setFrom('qkhanh282@zohomail.com', 'Mailer');
            $mail->addAddress($to);

            //Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
            return false;
        }
    }

    public static function sendPasswordResetEmail($to, $code_reset)
    {
        $subject = 'Password Reset Request';

        $body = '
    <!DOCTYPE html>
    <html lang="vi">
    <head>
      <meta charset="UTF-8" />
      <title>Password Reset</title>
    </head>
    <body style="margin:0;background:#f9fafb;font-family:Arial,Helvetica,sans-serif;">
      <div style="max-width:560px;margin:0 auto;padding:24px;color:#1f2937;">
        <h2 style="text-align:center;margin-bottom:20px;">BookZone</h2>
        <div style="background:#ffffff;border:1px solid #e5e7eb;border-radius:8px;padding:24px;">
          <p>Chào bạn,</p>
          <p>Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn. 
             Vui lòng sử dụng mã xác nhận bên dưới để tiếp tục quá trình:</p>
          <p style="text-align:center;margin:18px 0;">
            <span style="font-family:monospace;background:#f3f4f6;padding:12px 20px;
                         border-radius:6px;display:inline-block;letter-spacing:3px;
                         font-size:18px;">' . $code_reset . '</span>
          </p>
          <p style="color:#6b7280;font-size:13px;text-align:center;margin-top:8px;">
            Mã có hiệu lực trong 15 phút. Nếu bạn không yêu cầu, vui lòng bỏ qua email này.
          </p>
          <hr style="border-top:1px solid #e5e7eb;margin:20px 0;" />
          <p>Trân trọng,<br />Đội ngũ BookZone</p>
        </div>
        <p style="text-align:center;color:#6b7280;font-size:12px;margin-top:16px;">
          © ' . date("Y") . ' BookZone. Mọi quyền được bảo lưu.
        </p>
      </div>
    </body>
    </html>';

        return self::sendEmail($to, $subject, $body);
    }
}
