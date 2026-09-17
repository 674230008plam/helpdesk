<?php
$title = "เปิดใบแจ้งซ่อมใหม่";
ob_start(); 
?>
<div class="max-w-2xl mx-auto bg-white p-8 rounded-2xl border border-slate-200 shadow-sm">
    <h2 class="text-xl font-bold mb-6 text-slate-900 border-b pb-4">เปิดใบแจ้งซ่อมอุปกรณ์คอมพิวเตอร์</h2>
    <form action="/smart-it-helpdesk/public/tickets" method="POST" enctype="multipart/form-data" class="space-y-4">
        <input type="hidden" name="_csrf_token" value="<?= $_SESSION['_csrf_token'] ?>">

        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">หมวดหมู่งานซ่อม *</label>
            <select name="category_id" class="w-full border rounded-xl p-2.5 text-sm bg-slate-50">
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?> (<?= htmlspecialchars($cat['description']) ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">ระดับความเร่งด่วน *</label>
            <select name="priority" class="w-full border rounded-xl p-2.5 text-sm bg-slate-50">
                <option value="Low">Low (ต่ำ - ใช้งานได้ปกติแต่ขัดข้อง)</option>
                <option value="Medium" selected>Medium (ปกติ - กระทบงานบางส่วน)</option>
                <option value="High">High (ด่วน - ทำงานต่อไม่ได้)</option>
                <option value="Urgent">Urgent (ด่วนที่สุด - อุปกรณ์หลักขัดข้อง)</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">หัวข้อปัญหาที่พบ *</label>
            <input type="text" name="title" required placeholder="เช่น คอมพิวเตอร์เปิดไม่ติด, ปริ้นเตอร์ไม่ฟีดกระดาษ" class="w-full border rounded-xl p-2.5 text-sm bg-slate-50">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">รายละเอียดและสถานที่ตั้ง *</label>
            <textarea name="description" rows="4" required placeholder="ระบุอาการ หมายเลขครุภัณฑ์ และห้องที่ตั้งเครื่อง..." class="w-full border rounded-xl p-3 text-sm bg-slate-50"></textarea>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">รูปถ่ายอุปกรณ์หรือข้อความ Error (ไม่เกิน 5MB)</label>
            <input type="file" name="image" accept="image/*" class="w-full border rounded-xl p-2 text-sm bg-slate-50">
        </div>
        <div class="flex justify-end gap-3 pt-4 border-t">
            <a href="/smart-it-helpdesk/public/tickets" class="px-4 py-2 border rounded-xl text-xs font-semibold">ยกเลิก</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-xl text-xs font-semibold shadow">ยืนยันการแจ้งซ่อม</button>
        </div>
    </form>
</div>
<?php 
$content = ob_get_clean(); 
require_once dirname(__DIR__) . '/layouts/main.php';
?>
