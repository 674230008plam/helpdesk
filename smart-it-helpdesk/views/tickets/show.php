<?php
$title = "Ticket #{$ticket['id']}";
ob_start(); 
$role = \App\Core\Auth::role();
?>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex justify-between items-start mb-4">
                <span class="text-xs font-semibold text-blue-600">#TK-<?= str_pad((string)$ticket['id'], 4, '0', STR_PAD_LEFT) ?> | <?= htmlspecialchars($ticket['category_name']) ?></span>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800"><?= $ticket['status'] ?></span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 mb-2"><?= htmlspecialchars($ticket['title']) ?></h1>
            <p class="text-slate-600 text-sm whitespace-pre-line leading-relaxed"><?= htmlspecialchars($ticket['description']) ?></p>

            <?php if (!empty($ticket['image_path'])): ?>
                <div class="mt-4 pt-4 border-t">
                    <span class="text-xs font-bold text-slate-400 uppercase">รูปภาพแนบ:</span>
                    <img src="/smart-it-helpdesk/public/storage/uploads/<?= htmlspecialchars($ticket['image_path']) ?>" class="mt-2 max-h-72 rounded-xl border object-cover">
                </div>
            <?php endif; ?>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <h3 class="text-sm font-bold text-slate-800 uppercase mb-4">ประวัติการดำเนินงานและความคิดเห็น</h3>
            <div class="space-y-4 divide-y divide-slate-100">
                <?php foreach ($comments as $c): ?>
                    <div class="pt-3">
                        <div class="flex justify-between text-xs mb-1">
                            <span class="font-bold text-slate-800"><?= htmlspecialchars($c['author_name']) ?> (<?= $c['author_role'] ?>)</span>
                            <span class="text-slate-400"><?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></span>
                        </div>
                        <p class="text-sm text-slate-700"><?= nl2br(htmlspecialchars($c['body'])) ?></p>
                        <?php if ($c['image_path']): ?>
                            <img src="/smart-it-helpdesk/public/storage/uploads/<?= htmlspecialchars($c['image_path']) ?>" class="mt-2 max-h-48 rounded-lg border">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($comments)): ?>
                    <p class="text-xs text-slate-400 py-2">ยังไม่มีข้อความบันทึก</p>
                <?php endif; ?>
            </div>

            <form action="/smart-it-helpdesk/public/tickets/<?= $ticket['id'] ?>/comment" method="POST" enctype="multipart/form-data" class="mt-6 pt-4 border-t space-y-3">
                <input type="hidden" name="_csrf_token" value="<?= $_SESSION['_csrf_token'] ?>">
                <textarea name="body" required rows="2" placeholder="พิมพ์บันทึกงานซ่อม หรือตอบกลับข้อความ..." class="w-full border rounded-xl p-2.5 text-sm bg-slate-50"></textarea>
                <div class="flex justify-between items-center">
                    <input type="file" name="image" accept="image/*" class="text-xs text-slate-500">
                    <button type="submit" class="bg-slate-800 text-white text-xs px-4 py-2 rounded-xl font-semibold">ส่งข้อความ</button>
                </div>
            </form>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900 border-b pb-2">ข้อมูลผู้เกี่ยวข้อง</h3>
            <div class="text-xs space-y-2 text-slate-600">
                <p><strong>ผู้แจ้งซ่อม:</strong> <?= htmlspecialchars($ticket['user_name']) ?></p>
                <p><strong>ช่างผู้ดูแล:</strong> <?= $ticket['tech_name'] ? htmlspecialchars($ticket['tech_name']) : '<span class="text-amber-500">ยังไม่ระบุช่าง</span>' ?></p>
                <p><strong>ความสำคัญ:</strong> <?= $ticket['priority'] ?></p>
                <p><strong>วันที่แจ้ง:</strong> <?= date('d/m/Y H:i', strtotime($ticket['created_at'])) ?></p>
            </div>

            <div class="pt-4 border-t space-y-3">
                <h4 class="text-xs font-bold text-slate-700 uppercase">จัดการสถานะใบแจ้งซ่อม</h4>

                <?php if ($ticket['status'] === 'Open' && $role === 'admin'): ?>
                    <form action="/smart-it-helpdesk/public/tickets/<?= $ticket['id'] ?>/status" method="POST" class="space-y-2">
                        <input type="hidden" name="_csrf_token" value="<?= $_SESSION['_csrf_token'] ?>">
                        <input type="hidden" name="status" value="Assigned">
                        <label class="block text-xs font-medium">มอบหมายงานให้ช่าง:</label>
                        <select name="technician_id" required class="w-full border rounded-lg p-2 text-xs">
                            <?php foreach ($technicians as $tech): ?>
                                <option value="<?= $tech['id'] ?>"><?= htmlspecialchars($tech['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold py-2 rounded-lg">มอบหมายงาน (Assign)</button>
                    </form>
                <?php endif; ?>

                <?php if ($ticket['status'] === 'Assigned' && in_array($role, ['technician', 'admin'])): ?>
                    <form action="/smart-it-helpdesk/public/tickets/<?= $ticket['id'] ?>/status" method="POST">
                        <input type="hidden" name="_csrf_token" value="<?= $_SESSION['_csrf_token'] ?>">
                        <input type="hidden" name="status" value="InProgress">
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold py-2 rounded-lg">กดรับงานซ่อม (Start)</button>
                    </form>
                <?php endif; ?>

                <?php if ($ticket['status'] === 'InProgress' && in_array($role, ['technician', 'admin'])): ?>
                    <form action="/smart-it-helpdesk/public/tickets/<?= $ticket['id'] ?>/status" method="POST" class="space-y-2">
                        <input type="hidden" name="_csrf_token" value="<?= $_SESSION['_csrf_token'] ?>">
                        <input type="hidden" name="status" value="Resolved">
                        <input type="text" name="note" required placeholder="สรุปผลและวิธีแก้ไข..." class="w-full border rounded-lg p-2 text-xs">
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold py-2 rounded-lg">แจ้งซ่อมเสร็จสิ้น (Resolve)</button>
                    </form>
                <?php endif; ?>

                <?php if ($ticket['status'] === 'Resolved' && in_array($role, ['user', 'admin'])): ?>
                    <form action="/smart-it-helpdesk/public/tickets/<?= $ticket['id'] ?>/rate" method="POST" class="space-y-2 p-3 bg-emerald-50 rounded-xl border border-emerald-200">
                        <input type="hidden" name="_csrf_token" value="<?= $_SESSION['_csrf_token'] ?>">
                        <p class="text-xs font-bold text-emerald-800">เครื่องใช้งานได้เรียบร้อยแล้วหรือไม่?</p>
                        <select name="score" class="w-full border rounded-lg p-1.5 text-xs bg-white">
                            <option value="5">⭐⭐⭐⭐⭐ ดีเยี่ยม (5 ดาว)</option>
                            <option value="4">⭐⭐⭐⭐ ดีมาก (4 ดาว)</option>
                            <option value="3">⭐⭐⭐ ปานกลาง (3 ดาว)</option>
                            <option value="2">⭐⭐ พอใช้ (2 ดาว)</option>
                            <option value="1">⭐ ปรับปรุง (1 ดาว)</option>
                        </select>
                        <input type="text" name="feedback" placeholder="ความเห็นเพิ่มเติม..." class="w-full border rounded-lg p-1.5 text-xs bg-white">
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold py-2 rounded-lg">ยืนยันปิดงานและให้คะแนน (Close)</button>
                    </form>

                    <form action="/smart-it-helpdesk/public/tickets/<?= $ticket['id'] ?>/status" method="POST" class="pt-2">
                        <input type="hidden" name="_csrf_token" value="<?= $_SESSION['_csrf_token'] ?>">
                        <input type="hidden" name="status" value="InProgress">
                        <input type="text" name="note" required placeholder="ระบุเหตุผลที่ยังใช้งานไม่ได้..." class="w-full border rounded-lg p-1.5 text-xs mb-2">
                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white text-xs font-semibold py-2 rounded-lg">ส่งกลับไปซ่อมต่อ (Reject)</button>
                    </form>
                <?php endif; ?>

                <?php if ($ticket['status'] === 'Closed'): ?>
                    <div class="p-3 bg-slate-100 rounded-xl text-xs text-slate-600 text-center font-medium">
                        ✓ ปิดงานซ่อมสมบูรณ์แล้ว
                        <?php if ($rating): ?>
                            <div class="text-amber-500 mt-1 font-bold"><?= str_repeat('⭐', (int)$rating['score']) ?> (<?= $rating['score'] ?>/5)</div>
                            <?php if ($rating['feedback']): ?><p class="text-slate-400 italic mt-1 font-normal">"<?= htmlspecialchars($rating['feedback']) ?>"</p><?php endif; ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 text-xs shadow-sm">
            <h4 class="font-bold text-slate-700 uppercase mb-3">Audit Logs การเปลี่ยนสถานะ</h4>
            <div class="space-y-2.5">
                <?php foreach ($logs as $l): ?>
                    <div class="border-l-2 border-blue-500 pl-2.5">
                        <span class="text-slate-400 block text-[10px]"><?= date('d/m/Y H:i', strtotime($l['created_at'])) ?></span>
                        <span class="font-medium text-slate-800"><?= htmlspecialchars($l['changer_name']) ?>: <?= $l['from_status'] ?> &rarr; <?= $l['to_status'] ?></span>
                        <?php if ($l['note']): ?><p class="text-slate-500 italic text-[11px] mt-0.5">"<?= htmlspecialchars($l['note']) ?>"</p><?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php 
$content = ob_get_clean(); 
require_once dirname(__DIR__) . '/layouts/main.php';
?>
