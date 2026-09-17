<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ระบบจัดการงานช่าง - IT Technician</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100 text-slate-800 font-sans">

  <header class="bg-slate-900 text-white shadow sticky top-0 z-50">
    <div class="max-w-6xl mx-auto px-4 py-3 flex justify-between items-center">
      <div class="flex items-center space-x-2">
        <span class="text-xl font-bold tracking-wide">IT Technician Portal</span>
        <span class="text-xs bg-amber-500 text-slate-950 font-bold px-2 py-0.5 rounded">ระบบช่างซ่อม</span>
      </div>
      <button onclick="loadTechData()"
        class="text-xs bg-slate-800 hover:bg-slate-700 text-slate-200 px-3 py-1.5 rounded transition">
        รีเฟรชงาน 🔄
      </button>
    </div>
  </header>

  <main class="max-w-6xl mx-auto px-4 py-6 space-y-6">

    <!-- การ์ดสรุปสถานะงาน -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex justify-between items-center">
        <div>
          <p class="text-xs text-slate-500 font-semibold">รอดำเนินการ (Open)</p>
          <p id="stat-open" class="text-2xl font-bold text-amber-600">0</p>
        </div>
        <div class="p-2 bg-amber-50 rounded-lg text-amber-600 text-xl">⏳</div>
      </div>
      <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex justify-between items-center">
        <div>
          <p class="text-xs text-slate-500 font-semibold">กำลังซ่อม (In Progress)</p>
          <p id="stat-prog" class="text-2xl font-bold text-blue-600">0</p>
        </div>
        <div class="p-2 bg-blue-50 rounded-lg text-blue-600 text-xl">🛠️</div>
      </div>
      <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex justify-between items-center">
        <div>
          <p class="text-xs text-slate-500 font-semibold">เสร็จสิ้นแล้ว (Closed)</p>
          <p id="stat-close" class="text-2xl font-bold text-emerald-600">0</p>
        </div>
        <div class="p-2 bg-emerald-50 rounded-lg text-emerald-600 text-xl">✅</div>
      </div>
    </div>

    <!-- ตารางงานซ่อม -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
      <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
        <h3 class="font-bold text-slate-800">รายการงานแจ้งซ่อมทั้งหมด</h3>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
          <thead class="bg-slate-100 text-slate-600 border-b">
            <tr>
              <th class="p-3">ID</th>
              <th class="p-3">ปัญหา / รายละเอียด</th>
              <th class="p-3">ผู้แจ้ง / อีเมล</th>
              <th class="p-3">สถานที่</th>
              <th class="p-3">ความเร่งด่วน</th>
              <th class="p-3">สถานะ</th>
              <th class="p-3">การดำเนินการ</th>
            </tr>
          </thead>
          <tbody id="techTicketList" class="divide-y divide-slate-100"></tbody>
        </table>
      </div>
    </div>

  </main>

  <script>
    async function loadTechData() {
      const res = await fetch('api.php?action=tickets');
      const tickets = await res.json();

      document.getElementById('stat-open').innerText = tickets.filter(t => t.status === 'Open').length;
      document.getElementById('stat-prog').innerText = tickets.filter(t => t.status === 'InProgress').length;
      document.getElementById('stat-close').innerText = tickets.filter(t => t.status === 'Closed').length;

      const tbody = document.getElementById('techTicketList');
      tbody.innerHTML = tickets.map(t => {
        let badgeColor = 'bg-slate-100 text-slate-700';
        if (t.status === 'Open') badgeColor = 'bg-amber-100 text-amber-800';
        if (t.status === 'InProgress') badgeColor = 'bg-blue-100 text-blue-800';
        if (t.status === 'Closed') badgeColor = 'bg-emerald-100 text-emerald-800';

        return `
          <tr class="hover:bg-slate-50 transition">
            <td class="p-3 font-semibold text-slate-500">#${t.id}</td>
            <td class="p-3">
              <p class="font-medium text-slate-800">${t.title}</p>
              <p class="text-xs text-slate-500">${t.description || '-'}</p>
            </td>
            <td class="p-3">
              <p class="text-slate-800 font-medium">${t.user_name || 'ไม่ระบุ'}</p>
              <span class="text-xs text-indigo-600">${t.user_email || '-'}</span>
            </td>
            <td class="p-3 text-slate-600">${t.location_name}</td>
            <td class="p-3">
              <span class="text-xs font-semibold ${t.priority === 'Urgent' ? 'text-red-600 font-bold' : 'text-slate-600'}">${t.priority}</span>
            </td>
            <td class="p-3">
              <span class="px-2.5 py-1 rounded-full text-xs font-medium ${badgeColor}">${t.status}</span>
            </td>
            <td class="p-3 space-x-1">
              ${t.status === 'Open' ? `<button onclick="updateStatus(${t.id}, 'InProgress')" class="text-xs bg-blue-600 text-white px-2.5 py-1 rounded hover:bg-blue-700">รับงาน</button>` : ''}
              ${t.status === 'InProgress' ? `<button onclick="updateStatus(${t.id}, 'Closed')" class="text-xs bg-emerald-600 text-white px-2.5 py-1 rounded hover:bg-emerald-700">ปิดงาน (ส่งเมล)</button>` : ''}
              ${t.status === 'Closed' ? `<span class="text-xs text-slate-400">ปิดงานแล้ว</span>` : ''}
            </td>
          </tr>
        `;
      }).join('');
    }

    async function updateStatus(id, status) {
      await fetch('api.php?action=update_status', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id, status })
      });
      loadTechData();
    }

    loadTechData();
  </script>
</body>

</html>