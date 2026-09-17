<?php
// mailer.php - ส่งอีเมลจริงผ่าน Resend API

define('RESEND_API_KEY', 're_dgU8d5Fm_3v6HUVYY6MRXpjmpGWNahZKs'); // API Key ของ Resend

function send_email_notification($to_email, $to_name, $subject, $html_content)
{
  $payload = [
    'from' => 'Smart IT Helpdesk <onboarding@resend.dev>',
    'to' => [$to_email],
    'subject' => $subject,
    'html' => $html_content
  ];

  $ch = curl_init('https://api.resend.com/emails');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

  // ปิดการตรวจ SSL ชั่วคราว ป้องกัน cURL Error บน Localhost (XAMPP Windows)
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

  curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . RESEND_API_KEY,
    'Content-Type: application/json'
  ]);

  $response = curl_exec($ch);
  $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $curl_error = curl_error($ch);
  curl_close($ch);

  // ถ้าส่งไม่ผ่าน ให้บันทึกเหตุผลลงไฟล์ resend_error.log เพื่อเปิดดูได้ทันที
  if ($http_code < 200 || $http_code >= 300 || $curl_error) {
    $log_msg = date('Y-m-d H:i:s') . " | To: $to_email | HTTP: $http_code | cURL: $curl_error | Resend: $response\n";
    file_put_contents(__DIR__ . '/resend_error.log', $log_msg, FILE_APPEND);
  }

  return ($http_code >= 200 && $http_code < 300);
}

function build_new_ticket_email($ticket_id, $title, $category, $location, $priority, $desc)
{
  return "
    <div style='font-family: Arial, sans-serif; background-color: #f8fafc; padding: 20px;'>
      <div style='max-width: 500px; margin: auto; background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden;'>
        <div style='background-color: #4338ca; color: #fff; padding: 14px 20px;'>
          <h3 style='margin: 0;'>[เคสแจ้งซ่อมใหม่ #{$ticket_id}] {$title}</h3>
        </div>
        <div style='padding: 20px; font-size: 14px; color: #334155; line-height: 1.6;'>
          <p><strong>หมวดหมู่:</strong> {$category}</p>
          <p><strong>สถานที่:</strong> {$location}</p>
          <p><strong>ความเร่งด่วน:</strong> {$priority}</p>
          <p><strong>รายละเอียด:</strong> {$desc}</p>
        </div>
      </div>
    </div>";
}

function build_closed_ticket_email($ticket_id, $title)
{
  return "
    <div style='font-family: Arial, sans-serif; background-color: #f8fafc; padding: 20px;'>
      <div style='max-width: 500px; margin: auto; background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden;'>
        <div style='background-color: #059669; color: #fff; padding: 14px 20px;'>
          <h3 style='margin: 0;'>งานซ่อม #{$ticket_id} เสร็จสิ้นแล้ว</h3>
        </div>
        <div style='padding: 20px; font-size: 14px; color: #334155; line-height: 1.6;'>
          <p>งานหัวข้อ: <strong>{$title}</strong></p>
          <p>เจ้าหน้าที่ได้ดำเนินการปิดงานเรียบร้อยแล้ว ขอบคุณที่ใช้บริการ Smart IT Helpdesk</p>
        </div>
      </div>
    </div>";
}