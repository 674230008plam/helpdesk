<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เข้าสู่ระบบ - Smart IT Helpdesk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;600&display=swap" rel="stylesheet">
    <style>body { font-family: 'Sarabun', sans-serif; }</style>
</head>
<body class="bg-slate-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200 w-full max-w-md">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-slate-900">เข้าสู่ระบบ IT Helpdesk</h2>
            <p class="text-xs text-slate-500 mt-1">ระบบแจ้งซ่อมและติดตามงานซ่อมอุปกรณ์ไอที</p>
        </div>
        <?php if (!empty($_SESSION['error'])): ?>
            <div class="bg-red-50 text-red-600 p-3 rounded-xl mb-4 text-xs font-medium border border-red-200">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        <form action="/smart-it-helpdesk/public/login" method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">อีเมลผู้ใช้งาน</label>
                <input type="email" name="email" required placeholder="admin@helpdesk.local" class="w-full border rounded-xl p-2.5 text-sm bg-slate-50 focus:bg-white">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">รหัสผ่าน</label>
                <input type="password" name="password" required placeholder="••••••••" class="w-full border rounded-xl p-2.5 text-sm bg-slate-50 focus:bg-white">
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-xl shadow transition">
                เข้าสู่ระบบ
            </button>
        </form>
        <div class="mt-6 text-xs text-slate-500 bg-slate-50 p-3 rounded-xl border border-slate-100">
            <p class="font-bold text-slate-700 mb-1">บัญชีทดสอบในระบบ (Password: password123):</p>
            <p>• Admin: <code class="text-blue-600">admin@helpdesk.local</code></p>
            <p>• ช่างเทคนิค: <code class="text-blue-600">tech1@helpdesk.local</code></p>
            <p>• ผู้แจ้งซ่อม: <code class="text-blue-600">user1@helpdesk.local</code></p>
        </div>
    </div>
</body>
</html>
