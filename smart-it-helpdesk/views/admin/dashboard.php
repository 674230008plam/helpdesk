<?php
$title = "Admin Dashboard";
ob_start(); 
?>
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-900">แดชบอร์ดสรุปภาพรวมระบบ (Admin Dashboard)</h1>
    <p class="text-xs text-slate-500">สถิติประสิทธิภาพการซ่อมแซมและภาระงานของทีมช่างไอที</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <span class="text-xs uppercase text-slate-400 font-bold tracking-wider">เวลาเฉลี่ยในการซ่อม (MTTR)</span>
        <div class="text-3xl font-bold text-slate-900 mt-2"><?= $stats['avg_resolution_hours'] ?> <span class="text-sm font-normal text-slate-500">ชั่วโมง</span></div>
    </div>
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <span class="text-xs uppercase text-slate-400 font-bold tracking-wider">ความพึงพอใจเฉลี่ย</span>
        <div class="text-3xl font-bold text-amber-500 mt-2"><?= $stats['avg_rating'] ?> / 5.0</div>
    </div>
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <span class="text-xs uppercase text-slate-400 font-bold tracking-wider">ใบแจ้งซ่อมทั้งหมด</span>
        <div class="text-3xl font-bold text-blue-600 mt-2"><?= array_sum(array_column($stats['status_counts'], 'total')) ?> <span class="text-sm font-normal text-slate-500">รายการ</span></div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <h3 class="text-sm font-bold uppercase text-slate-800 mb-4">จำนวนงานแยกตามสถานะ</h3>
        <div class="space-y-3 text-sm">
            <?php foreach ($stats['status_counts'] as $sc): ?>
                <div class="flex justify-between p-2.5 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="font-medium text-slate-700">สถานะ <?= $sc['status'] ?></span>
                    <span class="font-bold text-slate-900"><?= $sc['total'] ?> งาน</span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <h3 class="text-sm font-bold uppercase text-slate-800 mb-4">ผลงานการปิดงานของช่างเทคนิค</h3>
        <div class="space-y-3 text-sm">
            <?php foreach ($stats['top_technicians'] as $tt): ?>
                <div class="flex justify-between p-2.5 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="font-medium text-slate-700"><?= htmlspecialchars($tt['name']) ?></span>
                    <span class="font-bold text-emerald-600"><?= $tt['resolved_count'] ?> งานสำเร็จ</span>
                </div>
            <?php endforeach; ?>
            <?php if (empty($stats['top_technicians'])): ?>
                <p class="text-xs text-slate-400 py-4 text-center">ยังไม่มีข้อมูลการปิดงานซ่อม</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php 
$content = ob_get_clean(); 
require_once dirname(__DIR__) . '/layouts/main.php';
?>
