// Debug script for exchange rate widgets
console.log('Debug script loaded');

document.addEventListener('DOMContentLoaded', function () {
    console.log('DOM loaded, checking for exchange rate elements...');

    // Check if elements exist
    const elements = ['gold-inr', 'gold-aed', 'usd-aed', 'usd-inr', 'aed-inr', 'gold-last-updated', 'currency-last-updated'];

    elements.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            console.log(`✅ Element found: ${id}`);
        } else {
            console.log(`❌ Element NOT found: ${id}`);
        }
    });

    // Check if the widget containers exist
    const widgets = document.querySelectorAll('.exchange-widget');
    console.log(`Found ${widgets.length} exchange widgets`);

    // Check if CSS is loaded
    const styles = getComputedStyle(document.body);
    console.log('CSS loaded:', styles.fontFamily);

    // Test the simple widget
    if (typeof SimpleExchangeRateWidget !== 'undefined') {
        console.log('✅ SimpleExchangeRateWidget class is available');
        console.log('⚠️ NOT creating new instance - already initialized by main script');
        // Don't create a new instance here as it's already created by the main script
    } else {
        console.log('❌ SimpleExchangeRateWidget class is NOT available');
    }

    // Test API connectivity
    testAPI();
});

async function testAPI() {
    console.log('Testing PHP proxy connectivity...');

    // Test PHP proxy endpoints instead of external APIs (no CORS issues)
    const possibleUrls = [
        '/wp-content/themes/byline/assets/scripts/exchange-rates.php',
        'assets/scripts/exchange-rates.php'
    ];

    let proxyWorking = false;

    for (const url of possibleUrls) {
        try {
            console.log(`🔄 Testing PHP proxy: ${url}`);
            const response = await fetch(url);
            if (response.ok) {
                const data = await response.json();
                console.log('✅ PHP Proxy working:', data.success ? 'Data received' : 'Fallback data');
                if (data.success) {
                    console.log('📊 Currency rates:', data.currency);
                    console.log('🥇 Gold rates:', data.gold);
                    console.log('🕒 Last updated:', data.timestamp);
                } else {
                    console.log('⚠️ Using fallback data:', data.error);
                }
                proxyWorking = true;
                break;
            }
        } catch (error) {
            console.log(`❌ PHP proxy failed at ${url}:`, error.message);
            continue;
        }
    }

    if (!proxyWorking) {
        console.error('❌ All PHP proxy endpoints failed - check server configuration');
    } else {
        console.log('🎉 Exchange rate system working via PHP proxy (no CORS issues!)');
    }
} 