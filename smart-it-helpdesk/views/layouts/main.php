<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'ระบบแจ้งซ่อมคอมพิวเตอร์' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Sarabun', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col justify-between">
    <header class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-md">
                    <i class="fa-solid fa-screwdriver-wrench text-lg"></i>
                </div>
                <div>
                    <a href="/smart-it-helpdesk/public/tickets" class="font-bold text-slate-900 text-lg">Smart IT Helpdesk</a>
                    <span class="block text-[11px] text-slate-400">ระบบแจ้งและติดตามงานซ่อมคอมพิวเตอร์</span>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <?php if (\App\Core\Auth::check()): ?>
                    <a href="/smart-it-helpdesk/public/tickets" class="text-sm font-medium hover:text-blue-600">รายการแจ้งซ่อม</a>
                    <?php if (\App\Core\Auth::role() === 'admin'): ?>
                        <a href="/smart-it-helpdesk/public/admin/dashboard" class="text-sm font-medium hover:text-blue-600">แดชบอร์ด</a>
                    <?php endif; ?>
                    <span class="text-xs bg-slate-100 text-slate-700 px-3 py-1 rounded-full font-semibold border">
                        <?= htmlspecialchars(\App\Core\Auth::user()['name']) ?> (<?= \App\Core\Auth::role() ?>)
                    </span>
                    <form action="/smart-it-helpdesk/public/logout" method="POST" class="inline">
                        <button type="submit" class="text-slate-400 hover:text-red-600 p-2"><i class="fa-solid fa-power-off"></i></button>
                    </form>
                <?php else: ?>
                    <a href="/smart-it-helpdesk/public/login" class="bg-blue-600 text-white text-sm px-4 py-2 rounded-lg">เข้าสู่ระบบ</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-8 w-full flex-grow">
        <?= $content ?? '' ?>
    </main>

    <footer class="bg-white border-t py-4 text-center text-xs text-slate-400">
        ฝ่ายพัฒนาระบบสารสนเทศและบริการคอมพิวเตอร์ © 2026
    </footer>
</body>
</html>
