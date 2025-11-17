// Debug script for exchange rate widgets
console.log('Debug script loaded');

document.addEventListener('DOMContentLoaded', function() {
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
        try {
            new SimpleExchangeRateWidget();
            console.log('✅ SimpleExchangeRateWidget initialized successfully');
        } catch (error) {
            console.error('❌ Error initializing SimpleExchangeRateWidget:', error);
        }
    } else {
        console.log('❌ SimpleExchangeRateWidget class is NOT available');
    }
    
    // Test API connectivity
    testAPI();
});

async function testAPI() {
    console.log('Testing API connectivity...');
    
    try {
        const response = await fetch('https://api.exchangerate-api.com/v4/latest/USD');
        const data = await response.json();
        console.log('✅ Currency API working:', data.rates ? 'Data received' : 'No data');
    } catch (error) {
        console.error('❌ Currency API failed:', error);
    }
    
    // Test PHP proxy instead of external APIs (no CORS issues)
    try {
        console.log('🔄 Testing PHP proxy...');
        const response = await fetch('assets/scripts/exchange-rates.php');
        if (response.ok) {
            const data = await response.json();
            console.log('✅ PHP Proxy working:', data.success ? 'Data received' : 'Fallback data');
            if (data.gold) {
                console.log('🥇 Gold rates:', data.gold);
            }
        }
    } catch (error) {
        console.error('❌ PHP proxy failed:', error);
    }
} 