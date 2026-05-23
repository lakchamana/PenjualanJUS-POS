<?php
session_start();
if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Daftar — POS Toko Jus</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-slate-50 to-white min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full px-6">
        <div class="bg-white rounded-2xl p-6 shadow-lg">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-slate-900 text-white rounded-lg flex items-center justify-center font-bold">JP</div>
                <div>
                    <h1 class="text-xl font-bold">Buat Akun</h1>
                    <p class="text-sm text-gray-500">Daftar sebagai kasir / user</p>
                </div>
            </div>


            <form id="regForm" class="space-y-4" onsubmit="return false;">
                <div>
                    <label class="text-xs text-gray-600">Nama Lengkap</label>
                    <input id="name" type="text" class="mt-1 block w-full p-3 border rounded-lg" placeholder="Nama lengkap" required>
                </div>
                <div>
                    <label class="text-xs text-gray-600">Email</label>
                    <input id="emailReg" type="email" class="mt-1 block w-full p-3 border rounded-lg" placeholder="email@contoh.com" required>
                </div>
                <div>
                    <label class="text-xs text-gray-600">Password</label>
                    <input id="passwordReg" type="password" class="mt-1 block w-full p-3 border rounded-lg" placeholder="Minimal 8 karakter" required>
                </div>


                <div>
                    <button id="regBtn" class="w-full px-4 py-3 bg-slate-900 text-white rounded-lg font-medium">Daftar</button>
                </div>


                <div id="regError" class="text-sm text-red-600 hidden"></div>


                <div class="pt-4 text-sm text-center text-gray-600">
                    Sudah punya akun? <a href="login.php" class="text-slate-900 font-medium">Masuk</a>
                </div>
            </form>
        </div>
    </div>


    <script>
        const API_AUTH = '../api/auth.php';

        const regBtn = document.getElementById('regBtn');
        const regError = document.getElementById('regError');

        regBtn.addEventListener('click', async () => {
            regError.classList.add('hidden');
            regBtn.disabled = true;
            regBtn.textContent = 'Memproses...';

            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('emailReg').value.trim();
            const password = document.getElementById('passwordReg').value;

            if (password.length < 6) {
                regError.textContent = 'Password minimal 6 karakter';
                regError.classList.remove('hidden');
                regBtn.disabled = false;
                regBtn.textContent = 'Daftar';
                return;
            }

            try {
                // Pastikan path berikut benar untuk struktur projectmu
                const res = await fetch(`${API_AUTH}?action=register`, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        email,
                        password,
                        name
                    })
                });

                // ambil text dulu untuk debug kalau bukan JSON
                const text = await res.text();
                let j = null;
                try {
                    j = JSON.parse(text);
                } catch (e) {
                    // bukan JSON -> tampilkan response mentah di console & UI
                    console.error('Non-JSON response from server:', text);
                    regError.textContent = 'Server mengembalikan response tidak valid. Lihat console (Network/Response).';
                    regError.classList.remove('hidden');
                    regBtn.disabled = false;
                    regBtn.textContent = 'Daftar';
                    return;
                }

                if (res.ok && j.success) {
                    alert('Registrasi berhasil. Silakan login.');
                    window.location.href = 'login.php';
                } else {
                    // tampilkan pesan error spesifik dari server bila ada
                    regError.textContent = j.error || j.detail || j.message || 'Registrasi gagal';
                    regError.classList.remove('hidden');
                }

            } catch (e) {
                // kemungkinan network error atau ReferenceError (API_AUTH undefined)
                console.error('Fetch error:', e);
                // Periksa jika ReferenceError (variabel API_AUTH tidak ada)
                if (e instanceof ReferenceError) {
                    regError.textContent = 'Internal error: konfigurasi client JS (API_AUTH) tidak ditemukan. Periksa script.';
                } else {
                    regError.textContent = 'Gagal terhubung ke server. Periksa URL API dan DevTools Network.';
                }
                regError.classList.remove('hidden');
            } finally {
                regBtn.disabled = false;
                regBtn.textContent = 'Daftar';
            }
        });

        // submit on Enter in password field
        document.getElementById('passwordReg').addEventListener('keydown', (ev) => {
            if (ev.key === 'Enter') regBtn.click();
        });
    </script>

</body>

</html>