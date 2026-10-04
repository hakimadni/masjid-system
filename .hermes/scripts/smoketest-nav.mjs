// Quick smoke test of all sidebar links in one session
import { chromium } from 'playwright';

(async () => {
  const browser = await chromium.launch();
  const page = await browser.newPage();
  
  // Login
  await page.goto('http://localhost:8000/login');
  await page.fill('input[type="email"]', 'admin@masjid.local');
  await page.fill('input[type="password"]', 'password');
  await page.click('button:text("LOG IN")');
  await page.waitForURL('**/dashboard');
  
  // All sidebar URLs
  const targets = [
    { name: 'Inventaris', url: '/aset', expect: 'Inventaris' },
    { name: 'Dokumen', url: '/dokumen', expect: 'Dokumen' },
    { name: 'Pengumuman', url: '/admin/pengumuman', expect: 'Pengumuman' },
    { name: 'Laporan', url: '/laporan', expect: 'Laporan' },
    { name: 'Pengaturan', url: '/pengaturan', expect: 'Pengaturan' },
    { name: 'Dashboard Qurban', url: '/qurban/dashboard', expect: 'Qurban' },
    { name: 'Tabungan Qurban', url: '/qurban/savings', expect: 'Qurban' },
    { name: 'Hewan Qurban', url: '/qurban/animals', expect: 'Hewan Qurban' },
    { name: 'Peserta Qurban', url: '/qurban/participants', expect: 'Peserta Qurban' },
    { name: 'Penyembelihan Qurban', url: '/qurban/slaughterings', expect: 'Penyembelihan' },
    { name: 'Relawan Qurban', url: '/qurban/volunteers', expect: 'Relawan Qurban' },
    { name: 'Distribusi Qurban', url: '/qurban/distributions', expect: 'Distribusi Daging' },
  ];
  
  const results = [];
  for (const t of targets) {
    await page.goto(`http://localhost:8000${t.url}`);
    const title = await page.title();
    const body = await page.$eval('body', el => el.innerText?.slice(0, 500) || '');
    const loaded = title !== '404' && !body.includes('404');
    const status = loaded ? (body.trim() ? '✅ OK' : '⚠️ Blank') : '❌ FAIL';
    results.push({ name: t.name, url: t.url, status, title, preview: body.replace(/\n+/g, ' | ') });
  }
  
  console.table(results);
  await browser.close();
})();