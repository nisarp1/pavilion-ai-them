<?php
/*
Template Name: Font Test
*/

get_header(); ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <h1 class="text-center mb-5">Font Rendering Test - Chrome vs Firefox</h1>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h2>Anek Malayalam Font Test</h2>
                </div>
                <div class="card-body">
                    <h1>Heading 1 - Anek Malayalam Heavy (900)</h1>
                    <h2>Heading 2 - Anek Malayalam Heavy (900)</h2>
                    <h3>Heading 3 - Anek Malayalam Heavy (900)</h3>
                    <h4>Heading 4 - Anek Malayalam Heavy (900)</h4>
                    <h5>Heading 5 - Anek Malayalam Heavy (900)</h5>
                    <h6>Heading 6 - Anek Malayalam Heavy (900)</h6>
                    
                    <p class="mt-4"><strong>Body Text - Anek Malayalam Bold:</strong> This is a sample paragraph using the Anek Malayalam font. The text should render consistently across Chrome and Firefox browsers. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                    
                    <p><em>Italic Text - Anek Malayalam Bold Italic:</em> This is italic text using the Anek Malayalam font. It should display properly in both browsers.</p>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h2>Anek Malayalam Font Test</h2>
                </div>
                <div class="card-body">
                    <h1 style="font-family: 'Anek Malayalam', sans-serif; font-weight: 900;">Heading 1 - Anek Malayalam Heavy (900)</h1>
                    <h2 style="font-family: 'Anek Malayalam', sans-serif; font-weight: 900;">Heading 2 - Anek Malayalam Heavy (900)</h2>
                    <h3 style="font-family: 'Anek Malayalam', sans-serif; font-weight: 900;">Heading 3 - Anek Malayalam Heavy (900)</h3>
                    <h4 style="font-family: 'Anek Malayalam', sans-serif; font-weight: 900;">Heading 4 - Anek Malayalam Heavy (900)</h4>
                    <h5 style="font-family: 'Anek Malayalam', sans-serif; font-weight: 900;">Heading 5 - Anek Malayalam Heavy (900)</h5>
                    <h6 style="font-family: 'Anek Malayalam', sans-serif; font-weight: 900;">Heading 6 - Anek Malayalam Heavy (900)</h6>
                    
                    <p class="mt-4" style="font-family: 'Anek Malayalam', sans-serif; font-weight: bold;"><strong>Body Text - Anek Malayalam Bold:</strong> This is a sample paragraph using the Anek Malayalam font. The text should render consistently across Chrome and Firefox browsers. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                    
                    <p style="font-family: 'Anek Malayalam', sans-serif; font-style: italic;"><em>Italic Text - Anek Malayalam Italic:</em> This is italic text using the Anek Malayalam font. It should display properly in both browsers.</p>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h2>Browser Information</h2>
                </div>
                <div class="card-body">
                    <p><strong>Current Browser:</strong> <?php echo $_SERVER['HTTP_USER_AGENT']; ?></p>
                    <p><strong>Font Loading Status:</strong> <span id="font-status">Checking...</span></p>
                    <p><strong>Font Rendering:</strong> <span id="rendering-status">Checking...</span></p>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h2>Testing Instructions</h2>
                </div>
                <div class="card-body">
                    <ol>
                        <li>Open this page in Chrome and Firefox</li>
                        <li>Compare the font rendering between browsers</li>
                        <li>Check if fonts load properly (no fallback fonts)</li>
                        <li>Verify font weights display correctly</li>
                        <li>Test on different screen resolutions</li>
                    </ol>
                    
                    <div class="alert alert-info mt-3">
                        <strong>Note:</strong> If you see differences between browsers, the font-fix.css file should help normalize the rendering. Clear your browser cache and refresh the page to see the changes.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Font loading detection
document.addEventListener('DOMContentLoaded', function() {
    // Check if fonts are loaded
    if (document.fonts && document.fonts.ready) {
        document.fonts.ready.then(function() {
            document.getElementById('font-status').textContent = 'Fonts loaded successfully';
            document.getElementById('font-status').style.color = 'green';
        });
    } else {
        document.getElementById('font-status').textContent = 'Font loading check not available';
    }
    
    // Check font rendering support
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    ctx.font = '16px Anek Malayalam';
    
    if (ctx.font.includes('Anek Malayalam')) {
        document.getElementById('rendering-status').textContent = 'Custom fonts supported';
        document.getElementById('rendering-status').style.color = 'green';
    } else {
        document.getElementById('rendering-status').textContent = 'Using fallback fonts';
        document.getElementById('rendering-status').style.color = 'orange';
    }
});
</script>

<style>
/* Additional test styles */
.card {
    border: 1px solid #ddd;
    border-radius: 8px;
    margin-bottom: 20px;
}

.card-header {
    background-color: #f8f9fa;
    padding: 15px;
    border-bottom: 1px solid #ddd;
}

.card-body {
    padding: 20px;
}

.alert {
    padding: 15px;
    border-radius: 4px;
}

.alert-info {
    background-color: #d1ecf1;
    border: 1px solid #bee5eb;
    color: #0c5460;
}
</style>

<?php get_footer(); ?>
