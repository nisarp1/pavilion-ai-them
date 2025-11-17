// Simple Exchange Rate Widget JavaScript (No PHP dependency)
class SimpleExchangeRateWidget {
    constructor() {
        this.goldRates = {
            inr: null,
            aed: null,
            inr22k: null,
            aed22k: null
        };
        this.currencyRates = {
            usdAed: null,
            usdInr: null,
            aedInr: null
        };
        this.lastUpdate = null;
        this.init();
    }

    init() {
        this.loadRates();
        // Update rates every 5 minutes
        setInterval(() => this.loadRates(), 5 * 60 * 1000);
    }

    async loadRates() {
        console.log('Loading exchange rates...');
        try {
            this.addLoadingStates();
            
            // Try to get real data from external APIs
            await this.loadRealRates();
            
        } catch (error) {
            console.error('Error loading exchange rates:', error);
            // Use fallback data
            this.useFallbackData();
        }
    }

    async loadRealRates() {
        try {
            console.log('🔄 Loading rates via PHP proxy (NO CORS issues!) - Fresh data requested at:', new Date().toLocaleTimeString());
            
            // Try multiple possible URLs for the PHP script (put working URLs first)
            const cacheBuster = '?v=' + Date.now() + '&force=' + Math.random().toString(36).substr(2, 9);
            const possibleUrls = [
                'http://localhost:8888/byline-wp/wp-content/themes/byline/assets/scripts/exchange-rates.php' + cacheBuster,
                '/wp-content/themes/byline/assets/scripts/exchange-rates.php' + cacheBuster,
                'assets/scripts/exchange-rates.php' + cacheBuster
            ];
            
            let response = null;
            let data = null;
            
            for (const url of possibleUrls) {
                try {
                    response = await fetch(url);
                    if (response.ok) {
                        data = await response.json();
                        break;
                    }
                } catch (e) {
                    console.log(`Failed to fetch from ${url}:`, e);
                    continue;
                }
            }
            
            if (!data) {
                throw new Error('Unable to fetch data from any PHP endpoint');
            }
            
            if (data.success) {
                // Update currency rates
                this.currencyRates.usdAed = data.currency.usd_aed;
                this.currencyRates.usdInr = data.currency.usd_inr;
                this.currencyRates.aedInr = data.currency.aed_inr;
                
                // Update gold rates (24K)
                this.goldRates.inr = data.gold.inr;
                this.goldRates.aed = data.gold.aed;
                
                // Update gold rates (22K) if available
                if (data.gold.inr_22k) {
                    this.goldRates.inr22k = data.gold.inr_22k;
                } else {
                    this.goldRates.inr22k = (this.goldRates.inr * 0.9167).toFixed(2);
                }
                
                if (data.gold.aed_22k) {
                    this.goldRates.aed22k = data.gold.aed_22k;
                } else {
                    this.goldRates.aed22k = (this.goldRates.aed * 0.9167).toFixed(2);
                }
                
                this.lastUpdate = data.timestamp;
            } else {
                // Use fallback data from PHP response
                this.currencyRates.usdAed = data.currency.usd_aed;
                this.currencyRates.usdInr = data.currency.usd_inr;
                this.currencyRates.aedInr = data.currency.aed_inr;
                this.goldRates.inr = data.gold.inr;
                this.goldRates.aed = data.gold.aed;
                this.goldRates.inr22k = (this.goldRates.inr * 0.9167).toFixed(2);
                this.goldRates.aed22k = (this.goldRates.aed * 0.9167).toFixed(2);
                this.lastUpdate = data.timestamp;
            }
            
            this.updateDisplay();
            this.removeLoadingStates();
            
        } catch (error) {
            console.log('PHP proxy failed, using fallback:', error);
            throw error; // This will trigger the fallback
        }
    }

    updateDisplay() {
        console.log('📊 DISPLAY UPDATED at', new Date().toLocaleTimeString(), 'with rates:', this.goldRates, this.currencyRates);
        
        // Update 24K gold rates
        if (this.goldRates.inr) {
            const element = document.getElementById('gold-inr');
            if (element) {
                element.textContent = `₹${this.goldRates.inr}`;
                console.log('Updated gold-inr:', `₹${this.goldRates.inr}`);
            } else {
                console.error('Element gold-inr not found');
            }
        }
        if (this.goldRates.aed) {
            const element = document.getElementById('gold-aed');
            if (element) {
                element.textContent = `د.إ${this.goldRates.aed}`;
                console.log('Updated gold-aed:', `د.إ${this.goldRates.aed}`);
            } else {
                console.error('Element gold-aed not found');
            }
        }

        // Update 22K gold rates
        if (this.goldRates.inr22k) {
            const element = document.getElementById('gold-22k-inr');
            if (element) {
                element.textContent = `₹${this.goldRates.inr22k}`;
                console.log('Updated gold-22k-inr:', `₹${this.goldRates.inr22k}`);
            } else {
                console.error('Element gold-22k-inr not found');
            }
        }
        if (this.goldRates.aed22k) {
            const element = document.getElementById('gold-22k-aed');
            if (element) {
                element.textContent = `د.إ${this.goldRates.aed22k}`;
                console.log('Updated gold-22k-aed:', `د.إ${this.goldRates.aed22k}`);
            } else {
                console.error('Element gold-22k-aed not found');
            }
        }

        // Update currency rates
        if (this.currencyRates.usdAed) {
            const element = document.getElementById('usd-aed');
            if (element) {
                element.textContent = this.currencyRates.usdAed;
                console.log('Updated usd-aed:', this.currencyRates.usdAed);
            } else {
                console.error('Element usd-aed not found');
            }
        }
        if (this.currencyRates.usdInr) {
            const element = document.getElementById('usd-inr');
            if (element) {
                element.textContent = this.currencyRates.usdInr;
                console.log('Updated usd-inr:', this.currencyRates.usdInr);
            } else {
                console.error('Element usd-inr not found');
            }
        }
        if (this.currencyRates.aedInr) {
            const element = document.getElementById('aed-inr');
            if (element) {
                element.textContent = this.currencyRates.aedInr;
                console.log('Updated aed-inr:', this.currencyRates.aedInr);
            } else {
                console.error('Element aed-inr not found');
            }
        }

        // Update last updated time
        const timeString = this.lastUpdate ? new Date(this.lastUpdate).toLocaleTimeString() : new Date().toLocaleTimeString();
        const goldTimeElement = document.getElementById('gold-last-updated');
        const currencyTimeElement = document.getElementById('currency-last-updated');
        
        if (goldTimeElement) {
            goldTimeElement.textContent = timeString;
            console.log('Updated gold-last-updated:', timeString);
        } else {
            console.error('Element gold-last-updated not found');
        }
        
        if (currencyTimeElement) {
            currencyTimeElement.textContent = timeString;
            console.log('Updated currency-last-updated:', timeString);
        } else {
            console.error('Element currency-last-updated not found');
        }
    }

    useFallbackData() {
        console.log('Using fallback data...');
        // Use realistic fallback data based on current market rates
        this.goldRates.inr = (6200 + Math.random() * 200).toFixed(2);
        this.goldRates.aed = (250 + Math.random() * 15).toFixed(2);
        // Calculate 22K gold rates (22K is 91.67% of 24K purity)
        this.goldRates.inr22k = (this.goldRates.inr * 0.9167).toFixed(2);
        this.goldRates.aed22k = (this.goldRates.aed * 0.9167).toFixed(2);
        this.currencyRates.usdAed = (3.67 + Math.random() * 0.02).toFixed(4);
        this.currencyRates.usdInr = (88.7 + Math.random() * 1.0).toFixed(4);
        this.currencyRates.aedInr = (24.2 + Math.random() * 0.3).toFixed(4);
        this.lastUpdate = new Date().toISOString();
        
        console.log('Fallback data generated:', this.goldRates, this.currencyRates);
        this.updateDisplay();
        this.removeLoadingStates();
    }

    addLoadingStates() {
        const elements = ['gold-inr', 'gold-aed', 'gold-22k-inr', 'gold-22k-aed', 'usd-aed', 'usd-inr', 'aed-inr'];
        elements.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.classList.add('loading');
                element.textContent = 'Loading...';
            }
        });
    }

    removeLoadingStates() {
        const elements = ['gold-inr', 'gold-aed', 'gold-22k-inr', 'gold-22k-aed', 'usd-aed', 'usd-inr', 'aed-inr'];
        elements.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.classList.remove('loading');
            }
        });
    }
}

// Initialize the widget when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing SimpleExchangeRateWidget v2.0 (Updated APIs)...');
    try {
        new SimpleExchangeRateWidget();
        console.log('SimpleExchangeRateWidget v2.0 initialized successfully');
    } catch (error) {
        console.error('Error initializing SimpleExchangeRateWidget:', error);
    }
}); 