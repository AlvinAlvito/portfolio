import puppeteer from 'puppeteer-core';
import path from 'node:path';
import { pathToFileURL } from 'node:url';

const chromePath = process.env.CHROME_PATH || 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const source = path.resolve('documentation/manualbook/manualbook.html');
const output = path.resolve('documentation/Manualbook-SI-Pental.pdf');

const browser = await puppeteer.launch({
    executablePath: chromePath,
    headless: true,
    args: ['--no-sandbox', '--allow-file-access-from-files', '--disable-dev-shm-usage'],
});

try {
    const page = await browser.newPage();
    await page.goto(pathToFileURL(source).href, { waitUntil: 'networkidle0', timeout: 30000 });
    await page.emulateMediaType('print');
    await page.pdf({
        path: output,
        printBackground: true,
        preferCSSPageSize: true,
        displayHeaderFooter: true,
        headerTemplate: '<span></span>',
        footerTemplate: `
            <div style="width:100%;padding:0 14mm;color:#82768e;font:8px 'Segoe UI',Arial,sans-serif;display:flex;justify-content:space-between;align-items:center">
                <span>SI Pental · Manualbook & Dokumentasi</span>
                <span><span class="pageNumber"></span> / <span class="totalPages"></span></span>
            </div>`,
        margin: { top: '0', right: '0', bottom: '0', left: '0' },
        outline: true,
    });
    console.log(output);
} finally {
    await browser.close();
}
