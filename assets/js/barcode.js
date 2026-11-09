/**
 * Pure Offline Code-128 Barcode SVG Generator
 * WAM Tech POS & ERP - Almubarmig
 * No external dependencies, 100% offline, crisp thermal printer output.
 */
const BarcodeGenerator = (function() {
    // Code 128 Character Set B encoding patterns (107 patterns, each 11 modules wide)
    const CODE128_PATTERNS = [
        "212222", "222122", "222221", "121223", "121322", "131222", "122213", "122312", "132212", "221213",
        "221312", "231212", "112232", "122132", "122231", "113222", "123122", "123221", "223211", "221132",
        "221231", "213212", "223112", "312131", "311222", "321122", "321221", "312212", "322112", "322211",
        "212123", "212321", "232121", "111323", "131123", "131321", "112313", "132113", "132311", "211313",
        "231113", "231311", "112133", "112331", "132131", "113123", "113321", "133121", "313121", "211331",
        "231131", "213113", "213311", "213131", "311123", "311321", "331121", "312113", "312311", "332111",
        "314111", "221411", "431111", "111224", "111422", "121124", "121421", "141122", "141221", "112214",
        "112412", "122114", "122411", "142112", "142211", "241211", "221114", "413111", "241112", "134111",
        "111242", "121142", "121241", "114212", "124112", "124211", "411212", "421112", "421211", "212141",
        "214121", "412121", "111143", "111341", "131141", "114113", "114311", "411113", "411311", "113141",
        "114131", "311141", "411131", "211412", "211214", "211232", "2331112"
    ];

    const START_B = 104;
    const STOP = 106;

    function encodeCode128B(text) {
        let codes = [START_B];
        let checksum = START_B;

        for (let i = 0; i < text.length; i++) {
            let code = text.charCodeAt(i) - 32;
            if (code < 0 || code > 95) code = 0;
            codes.push(code);
            checksum += code * (i + 1);
        }

        codes.push(checksum % 103);
        codes.push(STOP);

        // Convert codes to bar pattern widths
        let patternStr = "";
        for (let code of codes) {
            patternStr += CODE128_PATTERNS[code];
        }

        return patternStr;
    }

    function renderSvg(text, options = {}) {
        const height = options.height || 50;
        const barWidth = options.barWidth || 1.8;
        const showText = options.showText !== false;
        const quietZone = 10;

        const pattern = encodeCode128B(text);

        let totalModules = 0;
        for (let i = 0; i < pattern.length; i++) {
            totalModules += parseInt(pattern[i]);
        }

        const svgWidth = Math.ceil(totalModules * barWidth + (quietZone * 2));
        const svgHeight = height + (showText ? 18 : 0);

        let rects = [];
        let currentX = quietZone;
        let isBar = true;

        for (let i = 0; i < pattern.length; i++) {
            let w = parseInt(pattern[i]) * barWidth;
            if (isBar) {
                rects.push(`<rect x="${currentX.toFixed(2)}" y="0" width="${w.toFixed(2)}" height="${height}" fill="#000000" />`);
            }
            currentX += w;
            isBar = !isBar;
        }

        let textSvg = "";
        if (showText) {
            textSvg = `<text x="${(svgWidth / 2).toFixed(2)}" y="${(height + 14).toFixed(2)}" text-anchor="middle" font-family="monospace, monospace" font-size="11" font-weight="700" fill="#000000">${text}</text>`;
        }

        return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ${svgWidth} ${svgHeight}" width="${svgWidth}" height="${svgHeight}" style="display: block; margin: 0 auto; background: #ffffff;">
            ${rects.join('')}
            ${textSvg}
        </svg>`;
    }

    return {
        renderSvg: renderSvg
    };
})();

