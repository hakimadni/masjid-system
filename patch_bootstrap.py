import re

with open('/Users/user/Work/masjid-system/resources/js/bootstrap.js', 'r') as f:
    content = f.read()

interceptor = """
// Marbot-Friendly Human-Readable Error Interceptor
window.axios.interceptors.response.use(
    (response) => response,
    (error) => {
        let humanMessage = "Terjadi kesalahan yang tidak diketahui.";
        
        if (!error.response) {
            humanMessage = "Koneksi internet terputus. Pastikan internet Anda aktif dan coba lagi.";
        } else {
            const status = error.response.status;
            if (status >= 500) {
                humanMessage = "Maaf, sistem/server sedang sibuk. Mohon tunggu sebentar dan coba lagi.";
            } else if (status === 403) {
                humanMessage = "Anda tidak memiliki akses untuk melakukan tindakan ini.";
            } else if (status === 422) {
                humanMessage = "Beberapa data yang Anda masukkan belum tepat. Silakan periksa kolom yang berwarna merah.";
            } else if (status === 404) {
                humanMessage = "Data yang Anda cari tidak dapat ditemukan di sistem.";
            } else {
                humanMessage = error.response.data?.message || humanMessage;
            }
        }
        
        error.humanMessage = humanMessage;
        
        // Globally alert network errors if they are not 422 (which are usually handled inline)
        if (!error.response || (error.response.status !== 422 && error.response.status !== 404)) {
            // Wait for DOM to show simple floating alert
            const div = document.createElement('div');
            div.className = 'fixed top-4 left-1/2 -translate-x-1/2 z-[100] bg-rose-600 text-white px-5 py-3 rounded-full shadow-lg text-sm font-semibold whitespace-nowrap animate-bounce';
            div.innerText = humanMessage;
            document.body.appendChild(div);
            setTimeout(() => div.remove(), 4000);
        }
        
        return Promise.reject(error);
    }
);
"""

if "interceptors.response" not in content:
    content += "\n" + interceptor

with open('/Users/user/Work/masjid-system/resources/js/bootstrap.js', 'w') as f:
    f.write(content)
