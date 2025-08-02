const puppeteer = require('puppeteer');

async function generatePDF(url, outputPath) {
    const browser = await puppeteer.launch();
    const page = await browser.newPage();
    await page.goto(url);
    await page.pdf({ path: outputPath, format: 'A4', waitForFonts:true, printBackground: true, scale: 0.7 });
    await browser.close();
}

generatePDF(process.argv[2], process.argv[3])
    .then(() => process.exit(0))
    .catch(err => process.exit(1));
