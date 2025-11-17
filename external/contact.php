<?php include 'parts/shared/html-header.php'; ?>
<?php include 'parts/shared/header.php'; ?>

<?php
// Handle form submission
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['submit_contact'])
    && isset($_POST['contact_nonce'])
    && wp_verify_nonce($_POST['contact_nonce'], 'contact_form_nonce')
) {
    $name = sanitize_text_field($_POST['contact-name']);
    $phone = sanitize_text_field($_POST['contact-phone']);
    $email = sanitize_email($_POST['contact-email']);
    $message = sanitize_textarea_field($_POST['contact-message']);
    $website = sanitize_text_field($_POST['contact-website']); // Honeypot field
    
    // Check honeypot field (if filled, it's likely spam)
    if (!empty($website)) {
        // Silently ignore spam submissions
        $success_message = 'Thank you for your message. We will get back to you soon!';
    } else {
        // Basic validation
        $errors = array();
        if (empty($name)) $errors[] = 'Name is required';
        if (empty($email)) $errors[] = 'Email is required';
        if (!is_email($email)) $errors[] = 'Please enter a valid email address';
        if (empty($message)) $errors[] = 'Message is required';
    
    if (empty($errors)) {
        // Send email
        $to = get_option('admin_email');
        $subject = 'New Contact Form Submission from ' . $name;
        $body = "Name: $name\n";
        $body .= "Phone: $phone\n";
        $body .= "Email: $email\n\n";
        $body .= "Message:\n$message";
        $headers = array('Content-Type: text/plain; charset=UTF-8');
        
        if (wp_mail($to, $subject, $body, $headers)) {
            $success_message = 'Thank you for your message. We will get back to you soon!';
        } else {
            $error_message = 'Sorry, there was an error sending your message. Please try again.';
        }
    } else {
        $error_message = implode('<br>', $errors);
    }
    }
}
?>

<section class="contact-form section-gap bg-grey-light-three ">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="axil-contact-form-block m-b-xs-30">
                    <div class="section-title d-block">
                        <h2 class="h3 axil-title">
                            Send Us a Message
                        </h2>
                    </div>
                    <!-- End of .section-title -->

                    <?php if (isset($success_message)): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo $success_message; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>

                    <div class="axil-contact-form-wrapper p-t-xs-10">
                        <form class="axil-contact-form row no-gutters" method="post" action="">
                            <?php wp_nonce_field('contact_form_nonce', 'contact_nonce'); ?>
                            
                            <!-- Honeypot field to prevent spam -->
                            <div class="form-group col-12" style="display: none;">
                                <label for="contact-website">Website</label>
                                <input type="text" name="contact-website" id="contact-website" tabindex="-1" autocomplete="off">
                            </div>

                            <div class="form-group col-12">
                                <label for="contact-name">Name *</label>
                                <input type="text" name="contact-name" id="contact-name" value="<?php echo isset($_POST['contact-name']) ? esc_attr($_POST['contact-name']) : ''; ?>" required>
                            </div>

                            <div class="form-group col-12">
                                <label for="contact-phone">Phone</label>
                                <input type="text" name="contact-phone" id="contact-phone" value="<?php echo isset($_POST['contact-phone']) ? esc_attr($_POST['contact-phone']) : ''; ?>">
                            </div>

                            <div class="form-group col-12">
                                <label for="contact-email">Email *</label>
                                <input type="email" name="contact-email" id="contact-email" value="<?php echo isset($_POST['contact-email']) ? esc_attr($_POST['contact-email']) : ''; ?>" required>
                            </div>

                            <div class="form-group col-12">
                                <label for="contact-message">Message *</label>
                                <textarea name="contact-message" id="contact-message" required><?php echo isset($_POST['contact-message']) ? esc_textarea($_POST['contact-message']) : ''; ?></textarea>
                            </div>

                            <div class="col-12">
                                <button type="submit" name="submit_contact" class="btn btn-primary m-t-xs-0 m-t-lg-20">SUBMIT</button>
                            </div>
                        </form>
                        <!-- End of .axil-contact-form -->
                    </div>
                    <!-- End of .axil-contact-form-wrapper -->
                </div>
                <!-- End of .axil-contact-form-block -->
            </div>
            <!-- End of .col-lg-6 -->

            <div class="col-lg-5">
                <div class="axil-contact-info-wrapper p-l-md-45 m-b-xs-30">
                    <div class="axil-contact-info-inner">
                        <h2 class="h4 m-b-xs-10">
                            Contact Information
                        </h2>
                        <div class="axil-contact-info">
                            <address class="address">
                                <p class="mid m-b-xs-30">Byline Gulf FZE,<br>Dubai, UAE</p>

                                <div class="h5 m-b-xs-10">For inquiries and collaborations, contact us.</div>
                                <div>
                                    <a class="tel" href="tel:+971521142984"><i class="fas fa-phone"></i>+971 521142984</a>
                                </div>
                                <div>
                                    <a class="tel" href="tel:+971581786840"><i class="fas fa-phone"></i>+971 581786840</a>
                                </div>
                                <div>
                                    <a class="tel" href="mailto:bylinegulf@gmail.com"><i class="fas fa-envelope"></i>bylinegulf@gmail.com</a>
                                </div>
                            </address>
                            <div class="contact-social-share m-t-xs-30">
                                <div class="axil-social-title h5">Follow Us</div>
                                <ul class="social-share social-share__with-bg">
                                    <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                    <li><a href="#"><i class="fab fa-x-twitter"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End of .row -->
    </div>
    <!-- End of .container -->
</section>
<!-- End of .contact-form -->

<section>
    <div class="container">
        <div class="section-title m-b-xs-20">
            <h2 class="axil-title">
                Our Location
            </h2>
        </div>
        <!-- End of .section-title -->

        <div class="axil-map-wrapper m-b-xs-30">
            <iframe
                src="https://maps.google.com/maps?q=Dubai%2C%20UAE&t=&z=12&ie=UTF8&iwloc=&output=embed"
                width="600" height="450" class="iframe-no-border" allowfullscreen="" loading="lazy"></iframe>
        </div>
        <!-- End of .axil-map-wrapper -->
    </div>
    <!-- End of .container -->
</section>
<!-- End of .our-location -->

<?php include 'parts/shared/footer.php'; ?>
<?php include 'parts/shared/html-footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.axil-contact-form');
    const submitBtn = form.querySelector('button[type="submit"]');
    
    // Form validation and enhancement
    form.addEventListener('submit', function(e) {
        const name = form.querySelector('#contact-name').value.trim();
        const email = form.querySelector('#contact-email').value.trim();
        const message = form.querySelector('#contact-message').value.trim();
        
        // Clear previous error states
        form.querySelectorAll('.form-group').forEach(group => {
            group.classList.remove('has-error');
        });
        
        let hasErrors = false;
        
        // Validate required fields
        if (!name) {
            form.querySelector('#contact-name').closest('.form-group').classList.add('has-error');
            hasErrors = true;
        }
        
        if (!email || !isValidEmail(email)) {
            form.querySelector('#contact-email').closest('.form-group').classList.add('has-error');
            hasErrors = true;
        }
        
        if (!message) {
            form.querySelector('#contact-message').closest('.form-group').classList.add('has-error');
            hasErrors = true;
        }
        
        if (hasErrors) {
            e.preventDefault();
            return false;
        }
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.textContent = 'Sending...';
    });
    
    // Email validation function
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    // Real-time validation
    form.querySelectorAll('input, textarea').forEach(field => {
        field.addEventListener('blur', function() {
            const value = this.value.trim();
            const group = this.closest('.form-group');
            
            if (this.hasAttribute('required') && !value) {
                group.classList.add('has-error');
            } else if (this.type === 'email' && value && !isValidEmail(value)) {
                group.classList.add('has-error');
            } else {
                group.classList.remove('has-error');
            }
        });
    });
});
</script>

<style>
.form-group.has-error input,
.form-group.has-error textarea {
    border-color: #dc3545;
    box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.1);
}

.form-group.has-error label {
    color: #dc3545;
}

button[type="submit"]:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>