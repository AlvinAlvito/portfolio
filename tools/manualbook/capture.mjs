import puppeteer from 'puppeteer-core';
import fs from 'node:fs/promises';
import path from 'node:path';

const baseUrl = process.env.MANUAL_BASE_URL || 'http://127.0.0.1:8000';
const resultId = process.env.MANUAL_RESULT_ID || '137';
const chromePath = process.env.CHROME_PATH || 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const outputDir = path.resolve('documentation/manualbook/assets/screenshots');

await fs.mkdir(outputDir, { recursive: true });

const browser = await puppeteer.launch({
    executablePath: chromePath,
    headless: true,
    args: ['--no-sandbox', '--disable-dev-shm-usage', '--font-render-hinting=medium'],
});

const page = await browser.newPage();
await page.setViewport({ width: 1440, height: 900, deviceScaleFactor: 1.35 });

page.on('console', message => {
    if (message.type() === 'error') console.error(`[browser] ${message.text()}`);
});

async function settle(delay = 1300) {
    await page.evaluate(() => {
        document.getElementById('pageLoader')?.classList.add('is-hidden');
        document.querySelectorAll('.reveal').forEach(element => element.classList.add('visible'));
    });
    await new Promise(resolve => setTimeout(resolve, delay));
}

async function open(url) {
    await page.goto(`${baseUrl}${url}`, { waitUntil: 'domcontentloaded', timeout: 30000 });
    await settle();
}

async function shot(name, options = {}) {
    await page.screenshot({
        path: path.join(outputDir, `${name}.jpg`),
        type: 'jpeg',
        quality: 91,
        fullPage: options.fullPage ?? false,
    });
    console.log(`captured ${name}`);
}

async function captureSection(name, selector) {
    const element = await page.$(selector);
    if (!element) throw new Error(`Selector not found: ${selector}`);
    await element.evaluate(node => node.scrollIntoView({ block: 'center' }));
    await settle(600);
    await shot(name);
}

try {
    await open('/');
    await shot('01-beranda');
    await captureSection('02-beranda-konseling', '#konseling');
    await page.evaluate(() => window.scrollTo(0, 0));
    await page.click('#chatLauncher');
    await settle(450);
    await shot('03-chatbot');

    await open('/edukasi');
    await shot('04-pusat-edukasi');
    await captureSection('05-edukasi-klasifikasi', '#klasifikasi');
    await captureSection('06-edukasi-tanda', '#tanda');
    await captureSection('07-edukasi-rawat-diri', '#rawat-diri');

    await open('/edukasi/gangguan/ocd');
    await shot('08-detail-materi');

    await open('/profil');
    await shot('09-tentang-kami');

    await open('/questioner');
    await shot('10-kuesioner-bagian-1');
    await page.type('#name', 'Pengguna Contoh');
    await page.type('#age', '21');
    await page.select('#gender', 'female');
    const firstPageQuestions = await page.$$('[data-wizard-page="0"] [data-question]');
    for (const question of firstPageQuestions) {
        const radio = await question.$('input[type="radio"][value="1"]');
        await radio.click();
    }
    await page.click('#nextButton');
    await settle(700);
    await shot('11-kuesioner-bagian-2');

    await open(`/questioner/${resultId}/hasil`);
    const firstExplanation = await page.$('.score-details');
    if (firstExplanation) {
        await firstExplanation.evaluate(element => {
            element.open = true;
            element.scrollIntoView({ block: 'center' });
        });
        await settle(500);
    }
    await shot('12-hasil-refleksi');

    await open('/');
    await page.click('[data-open-login]');
    await settle(350);
    await shot('13-login-admin');
    await page.type('#adminUsername', 'admin');
    await page.type('#adminPassword', 'sipental2026');
    await Promise.all([
        page.waitForNavigation({ waitUntil: 'domcontentloaded', timeout: 30000 }),
        page.click('#loginModal button[type="submit"]'),
    ]);
    await settle(2200);
    if (!page.url().includes('/admin')) throw new Error(`Login admin gagal; URL saat ini ${page.url()}`);
    await shot('14-admin-dashboard');

    await open('/admin/questioner');
    await shot('15-admin-bank-pertanyaan');
    await page.click('[data-modal-open="addQuestion"]');
    await settle(350);
    await shot('16-admin-form-pertanyaan');

    await open('/admin/responder');
    await shot('17-admin-data-responden');
    const detailHref = await page.$$eval('a[href*="/admin/responder/"]', elements => {
        const link = elements.find(element => /\/admin\/responder\/\d+$/.test(new URL(element.href).pathname));
        if (!link) throw new Error('Tautan detail responden tidak ditemukan');
        return link.getAttribute('href');
    });
    await open(new URL(detailHref, baseUrl).pathname);
    await settle(2200);
    await shot('18-admin-detail-responden');
} finally {
    await browser.close();
}
