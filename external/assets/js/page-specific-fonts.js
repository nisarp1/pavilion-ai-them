// Page-specific font detection and application
document.addEventListener('DOMContentLoaded', function() {
    console.log('Page-specific fonts script loaded');
    console.log('Current pathname:', window.location.pathname);
    
    // Check if current URL contains 'home-kanaka'
    if (window.location.pathname.includes('/home-kanaka/')) {
        // Add CSS class to body for Kanaka font
        document.body.classList.add('home-kanaka-page');
        
        // Also add data attribute for additional CSS targeting
        document.body.setAttribute('data-page', 'home-kanaka');
        
        // Add page ID class for maximum specificity (page ID 184)
        document.body.classList.add('page-id-184');
        
        console.log('Kanaka font applied for home-kanaka page');
        console.log('Body classes:', document.body.className);
        console.log('Body data-page:', document.body.getAttribute('data-page'));
        
        // Debug: Log all body classes and IDs
        console.log('All body classes:', document.body.className);
        console.log('Body ID:', document.body.id);
        console.log('All body attributes:', document.body.attributes);
        
        // Add a visible indicator for testing
        var indicator = document.createElement('div');
        indicator.style.cssText = 'position: fixed; top: 10px; right: 10px; background: red; color: white; padding: 5px; z-index: 9999; font-size: 12px;';
        indicator.textContent = 'KANAKA FONT ACTIVE';
        document.body.appendChild(indicator);
        
        // Also add inline styles as backup
        document.body.style.setProperty('font-family', 'ML-KV-Kanaka, sans-serif', 'important');
        document.body.style.setProperty('font-weight', 'bold', 'important');
        
        // Force apply Kanaka font to all heading elements
        const headings = document.querySelectorAll('h1, h2, h3, h4, h5, h6');
        headings.forEach(function(heading) {
            heading.style.setProperty('font-family', 'ML-KV-Kanaka, sans-serif', 'important');
            heading.style.setProperty('font-weight', '900', 'important');
        });
        
        // Also apply to specific heading classes
        const headingClasses = document.querySelectorAll('.heading, .axil-post-title, .featured-title, .area-title h2');
        headingClasses.forEach(function(element) {
            element.style.setProperty('font-family', 'ML-KV-Kanaka, sans-serif', 'important');
            element.style.setProperty('font-weight', '900', 'important');
        });
        
        console.log('Applied Kanaka font to', headings.length, 'headings and', headingClasses.length, 'heading classes');
    } else {
        console.log('Not on home-kanaka page, using default Navya font');
    }
});

// Also run immediately if DOM is already loaded
if (document.readyState === 'loading') {
    // DOM is still loading, wait for DOMContentLoaded
} else {
    // DOM is already loaded, run immediately
    console.log('DOM already loaded, running font detection immediately');
    console.log('Current pathname:', window.location.pathname);
    
    if (window.location.pathname.includes('/home-kanaka/')) {
        document.body.classList.add('home-kanaka-page');
        document.body.setAttribute('data-page', 'home-kanaka');
        document.body.classList.add('page-id-184');
        console.log('Kanaka font applied for home-kanaka page (immediate)');
        console.log('Body classes:', document.body.className);
        console.log('Body data-page:', document.body.getAttribute('data-page'));
        
        // Debug: Log all body classes and IDs
        console.log('All body classes:', document.body.className);
        console.log('Body ID:', document.body.id);
        console.log('All body attributes:', document.body.attributes);
        
        // Add a visible indicator for testing
        var indicator = document.createElement('div');
        indicator.style.cssText = 'position: fixed; top: 10px; right: 10px; background: red; color: white; padding: 5px; z-index: 9999; font-size: 12px;';
        indicator.textContent = 'KANAKA FONT ACTIVE';
        document.body.appendChild(indicator);
        
        // Also add inline styles as backup
        document.body.style.setProperty('font-family', 'ML-KV-Kanaka, sans-serif', 'important');
        document.body.style.setProperty('font-weight', 'bold', 'important');
        
        // Force apply Kanaka font to all heading elements
        const headings = document.querySelectorAll('h1, h2, h3, h4, h5, h6');
        headings.forEach(function(heading) {
            heading.style.setProperty('font-family', 'ML-KV-Kanaka, sans-serif', 'important');
            heading.style.setProperty('font-weight', '900', 'important');
        });
        
        // Also apply to specific heading classes
        const headingClasses = document.querySelectorAll('.heading, .axil-post-title, .featured-title, .area-title h2');
        headingClasses.forEach(function(element) {
            element.style.setProperty('font-family', 'ML-KV-Kanaka, sans-serif', 'important');
            element.style.setProperty('font-weight', '900', 'important');
        });
        
        console.log('Applied Kanaka font to', headings.length, 'headings and', headingClasses.length, 'heading classes');
    }
}
