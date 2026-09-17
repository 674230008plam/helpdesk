<?php
$title = "รายการแจ้งซ่อมทั้งหมด";
ob_start(); 
?>
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">รายการแจ้งซ่อมคอมพิวเตอร์</h1>
        <p class="text-xs text-slate-500">ติดตามสถานะและตรวจสอบรายละเอียดใบแจ้งซ่อม</p>
    </div>
    <a href="/smart-it-helpdesk/public/tickets/create" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl shadow transition">
        + เปิดใบแจ้งซ่อมใหม่
    </a>
</div>

<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm border-collapse">
            <thead class="bg-slate-50 border-b text-xs font-semibold text-slate-500 uppercase">
                <tr>
                    <th class="p-4">Ticket ID</th>
                    <th class="p-4">หัวข้อปัญหา / อาการ</th>
                    <th class="p-4">หมวดหมู่</th>
                    <th class="p-4">ผู้แจ้ง</th>
                    <th class="p-4">สถานะ</th>
                    <th class="p-4">ระดับความสำคัญ</th>
                    <th class="p-4 text-right">ดำเนินการ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($tickets as $t): ?>
                    <tr class="hover:bg-slate-50 transition">
                        <td class="p-4 font-semibold text-blue-600">#TK-<?= str_pad((string)$t['id'], 4, '0', STR_PAD_LEFT) ?></td>
                        <td class="p-4 font-medium"><?= htmlspecialchars($t['title']) ?></td>
                        <td class="p-4 text-xs text-slate-500"><?= htmlspecialchars($t['category_name']) ?></td>
                        <td class="p-4 text-slate-600"><?= htmlspecialchars($t['user_name']) ?></td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                <?= match($t['status']) {
                                    'Open' => 'bg-amber-100 text-amber-800',
                                    'Assigned' => 'bg-sky-100 text-sky-800',
                                    'InProgress' => 'bg-indigo-100 text-indigo-800',
                                    'Resolved' => 'bg-emerald-100 text-emerald-800',
                                    'Closed' => 'bg-slate-100 text-slate-600',
                                    default => 'bg-gray-100'
                                } ?>">
                                <?= $t['status'] ?>
                            </span>
                        </td>
                        <td class="p-4 text-xs">
                            <span class="font-medium <?= match($t['priority']) {
                                'Urgent' => 'text-red-600',
                                'High' => 'text-orange-600',
                                'Medium' => 'text-slate-600',
                                default => 'text-slate-400'
                            } ?>">
                                <?= $t['priority'] ?>
                            </span>
                        </td>
                        <td class="p-4 text-right">
                            <a href="/smart-it-helpdesk/public/tickets/<?= $t['id'] ?>" class="text-blue-600 hover:underline font-semibold text-xs">เปิดดู &rarr;</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($tickets)): ?>
                    <tr><td colspan="7" class="p-8 text-center text-slate-400">ยังไม่มีรายการแจ้งซ่อมในระบบ</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php 
$content = ob_get_clean(); 
require_once dirname(__DIR__) . '/layouts/main.php';
?>
