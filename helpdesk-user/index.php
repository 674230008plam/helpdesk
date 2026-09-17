<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart IT Helpdesk - ผู้ใช้งานแจ้งซ่อม</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-50 text-slate-800 font-sans">

    <header class="bg-indigo-700 text-white shadow sticky top-0 z-50">
        <div class="max-w-5xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <span class="text-xl font-bold tracking-wide">Smart IT Helpdesk</span>
                <span class="text-xs bg-indigo-500 px-2 py-0.5 rounded text-indigo-100">User Portal</span>
            </div>
            <span class="text-xs text-indigo-200">ระบบแจ้งปัญหาออนไลน์</span>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 py-6 space-y-6">

        <!-- แผงสถิติรวม -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex justify-between items-center">
                <div>
                    <p class="text-xs text-slate-500">รอดำเนินการ (Open)</p>
                    <p id="stat-open" class="text-2xl font-bold text-amber-600">0</p>
                </div>
                <div class="p-2 bg-amber-50 rounded-lg text-amber-600 text-xl">⏳</div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex justify-between items-center">
                <div>
                    <p class="text-xs text-slate-500">กำลังซ่อม (In Progress)</p>
                    <p id="stat-prog" class="text-2xl font-bold text-blue-600">0</p>
                </div>
                <div class="p-2 bg-blue-50 rounded-lg text-blue-600 text-xl">🛠️</div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex justify-between items-center">
                <div>
                    <p class="text-xs text-slate-500">เสร็จสิ้นแล้ว (Closed)</p>
                    <p id="stat-close" class="text-2xl font-bold text-emerald-600">0</p>
                </div>
                <div class="p-2 bg-emerald-50 rounded-lg text-emerald-600 text-xl">✅</div>
            </div>
        </div>

        <!-- ฟอร์มแจ้งปัญหา -->
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                <span class="w-2.5 h-2.5 bg-indigo-600 rounded-full"></span> แจ้งปัญหา / ซ่อมบำรุง
            </h2>
            <form id="ticketForm" onsubmit="handleCreate(event)" class="space-y-4">
                <div
                    class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm bg-indigo-50/50 p-3 rounded-lg border border-indigo-100">
                    <div>
                        <label class="block text-slate-700 font-medium mb-1">ชื่อผู้แจ้งซ่อม *</label>
                        <input id="user_name" required
                            class="w-full border border-slate-300 rounded-lg p-2.5 outline-none bg-white focus:ring-2 focus:ring-indigo-500"
                            placeholder="เช่น ปาล์มมี่">
                    </div>
                    <div>
                        <label class="block text-slate-700 font-medium mb-1">อีเมลสำหรับรับแจ้งเตือนสถานะ *</label>
                        <input id="user_email" type="email" required
                            class="w-full border border-slate-300 rounded-lg p-2.5 outline-none bg-white focus:ring-2 focus:ring-indigo-500"
                            placeholder="เช่น 674230008@webmail.npru.ac.th">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <label class="block text-slate-600 font-medium mb-1">หัวข้อปัญหา *</label>
                        <input id="title" required
                            class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-indigo-500"
                            placeholder="เช่น คอมพิวเตอร์เปิดไม่ติด">
                    </div>
                    <div>
                        <label class="block text-slate-600 font-medium mb-1">หมวดหมู่งานซ่อม *</label>
                        <select id="category_id" required
                            class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- กำลังโหลดหมวดหมู่ --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-600 font-medium mb-1">สถานที่ตั้ง (อาคารและห้อง) *</label>
                        <select id="location_id" required
                            class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- กำลังโหลดสถานที่ --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-600 font-medium mb-1">ระดับความเร่งด่วน</label>
                        <select id="priority"
                            class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="Low">ต่ำ (Low)</option>
                            <option value="Medium" selected>ปานกลาง (Medium)</option>
                            <option value="High">สูง (High)</option>
                            <option value="Urgent">เร่งด่วนที่สุด (Urgent)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-slate-600 font-medium mb-1">รายละเอียดอาการเพิ่มเติม</label>
                    <textarea id="description" rows="3"
                        class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="ระบุอาการผิดปกติ"></textarea>
                </div>

                <button id="submitBtn" type="submit"
                    class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg hover:bg-indigo-700 font-medium text-sm shadow transition">
                    บันทึกและส่งเรื่องแจ้งซ่อม (ส่งแจ้งเตือนเข้าเมล)
                </button>
            </form>
        </div>

        <!-- ตารางติดตามสถานะงานของผู้ใช้ -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <h3 class="font-bold text-slate-800">ติดตามสถานะงานแจ้งซ่อมทั้งหมด</h3>
                <button onclick="loadData()" class="text-xs text-indigo-600 hover:underline">รีเฟรชข้อมูล</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-100 text-slate-600 border-b">
                        <tr>
                            <th class="p-3">ID</th>
                            <th class="p-3">ปัญหา / หมวดหมู่</th>
                            <th class="p-3">ผู้แจ้ง / อีเมล</th>
                            <th class="p-3">สถานที่</th>
                            <th class="p-3">สถานะ</th>
                        </tr>
                    </thead>
                    <tbody id="ticketList" class="divide-y divide-slate-100"></tbody>
                </table>
            </div>
        </div>

    </main>

    <script>
        const API_URL = 'http://localhost/helpdesk-tech/api.php';

        async function init() {
            try {
                const res = await fetch(`${API_URL}?action=meta`);
                const meta = await res.json();

                document.getElementById('category_id').innerHTML =
                    '<option value="">-- เลือกหมวดหมู่งานซ่อม --</option>' +
                    meta.categories.map(c => `<option value="${c.id}">${c.name}</option>`).join('');

                document.getElementById('location_id').innerHTML =
                    '<option value="">-- เลือกสถานที่ตั้ง --</option>' +
                    meta.locations.map(l => `<option value="${l.id}">${l.building} - ${l.room}</option>`).join('');

                loadData();
            } catch (err) {
                console.error("เชื่อมต่อระบบ API ไม่สำเร็จ:", err);
            }
        }

        async function loadData() {
            try {
                const res = await fetch(`${API_URL}?action=tickets`);
                const tickets = await res.json();

                document.getElementById('stat-open').innerText = tickets.filter(t => t.status === 'Open').length;
                document.getElementById('stat-prog').innerText = tickets.filter(t => t.status === 'InProgress').length;
                document.getElementById('stat-close').innerText = tickets.filter(t => t.status === 'Closed').length;

                const tbody = document.getElementById('ticketList');
                tbody.innerHTML = tickets.map(t => {
                    let badgeColor = 'bg-amber-100 text-amber-800';
                    if (t.status === 'InProgress') badgeColor = 'bg-blue-100 text-blue-800';
                    if (t.status === 'Closed') badgeColor = 'bg-emerald-100 text-emerald-800';

                    return `
            <tr class="hover:bg-slate-50 transition border-b">
              <td class="p-3 font-semibold text-slate-500">#${t.id}</td>
              <td class="p-3">
                <p class="font-medium text-slate-800">${t.title}</p>
                <span class="text-xs text-slate-500">${t.category_name || '-'}</span>
              </td>
              <td class="p-3">
                <p class="text-slate-800 font-medium">${t.user_name || '-'}</p>
                <span class="text-xs text-indigo-600">${t.user_email || '-'}</span>
              </td>
              <td class="p-3 text-slate-600">${t.location_name || '-'}</td>
              <td class="p-3">
                <span class="px-2.5 py-1 rounded-full text-xs font-medium ${badgeColor}">${t.status}</span>
              </td>
            </tr>
          `;
                }).join('');
            } catch (err) {
                console.error("โหลดข้อมูลตารางล้มเหลว:", err);
            }
        }

        async function handleCreate(e) {
            e.preventDefault();
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerText = "กำลังบันทึกและส่งข้อมูล...";

            const payload = {
                user_name: document.getElementById('user_name').value.trim(),
                user_email: document.getElementById('user_email').value.trim(),
                title: document.getElementById('title').value.trim(),
                category_id: document.getElementById('category_id').value,
                location_id: document.getElementById('location_id').value,
                priority: document.getElementById('priority').value,
                description: document.getElementById('description').value.trim()
            };

            try {
                const res = await fetch(`${API_URL}?action=create`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const result = await res.json();

                if (result.success) {
                    alert("แจ้งซ่อมสำเร็จ! ข้อมูลถูกส่งเข้าสู่ระบบช่างแล้ว");
                    document.getElementById('ticketForm').reset();
                    loadData();
                }
            } catch (err) {
                alert("เกิดข้อผิดพลาดในการส่งข้อมูล");
            } finally {
                btn.disabled = false;
                btn.innerText = "บันทึกและส่งเรื่องแจ้งซ่อม (ส่งแจ้งเตือนเข้าเมล)";
            }
        }

        init();
    </script>
</body>

</html>