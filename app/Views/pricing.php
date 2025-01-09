<?php require ROOT_PATH . '/app/Views/partials/header.php'; ?>

<div class="container mt-5">
    <div class="text-center mb-5">
        <h1>Choose Your Plan</h1>
        <p class="lead">Select the perfect plan for your needs</p>
    </div>

    <div class="row row-cols-1 row-cols-md-2 mb-3 text-center">
        <!-- Self Hosted Plan -->
        <div class="col">
            <div class="card mb-4 rounded-3 shadow-sm">
                <div class="card-header py-3">
                    <h4 class="my-0 fw-normal">Self Hosted</h4>
                </div>
                <div class="card-body">
                    <h1 class="card-title">Free forever</h1>
                    <ul class="list-unstyled mt-3 mb-4">
                        <li>Unlimited users</li>
                        <li>Host on your own servers</li>
                        <li>Community forum access</li>
                        <li>Documentation access</li>
                        <li>Security updates</li>
                    </ul>
                    <button type="button" class="w-100 btn btn-lg btn-outline-primary" data-bs-toggle="modal" data-bs-target="#selfHostedModal">
                        Get started
                    </button>
                </div>
            </div>
        </div>

        <!-- Hosted Plan -->
        <div class="col">
            <div class="card mb-4 rounded-3 shadow-sm border-primary">
                <div class="card-header py-3 text-bg-primary border-primary">
                    <h4 class="my-0 fw-normal">Hosted</h4>
                </div>
                <div class="card-body">
                    <h1 class="card-title">USD 200<small class="text-muted fw-light">/month</small></h1>
                    <ul class="list-unstyled mt-3 mb-4">
                        <li>Unlimited users</li>
                        <li>Managed hosting</li>
                        <li>Automatic updates</li>
                        <li>Daily backups</li>
                        <li>SSL certificate included</li>
                    </ul>
                    <button type="button" class="w-100 btn btn-lg btn-primary" data-bs-toggle="modal" data-bs-target="#hostedModal">
                        Get started
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h3>Support Packages</h3>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Feature</th>
                                    <th>Basic</th>
                                    <th>Professional</th>
                                    <th>Enterprise</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Price (monthly)</td>
                                    <td>$50/month</td>
                                    <td>$200/month</td>
                                    <td>$500/month</td>
                                </tr>
                                <tr>
                                    <td>Response Time</td>
                                    <td>48 hours</td>
                                    <td>24 hours</td>
                                    <td>4 hours</td>
                                </tr>
                                <tr>
                                    <td>Critical Ticket Response</td>
                                    <td>24 hours</td>
                                    <td>8 hours</td>
                                    <td>1 hour</td>
                                </tr>
                                <tr>
                                    <td>Support Channels</td>
                                    <td>Email</td>
                                    <td>Email & Chat</td>
                                    <td>Email, Chat & Phone</td>
                                </tr>
                                <tr>
                                    <td>Training Sessions</td>
                                    <td>Documentation</td>
                                    <td>Monthly Webinar</td>
                                    <td>Custom Training</td>
                                </tr>
                                <tr>
                                    <td>Technical Support</td>
                                    <td>Basic</td>
                                    <td>Advanced</td>
                                    <td>Priority</td>
                                </tr>
                                <tr>
                                    <td>Custom Development</td>
                                    <td>❌</td>
                                    <td>5 hours/month</td>
                                    <td>20 hours/month</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Self Hosted Modal -->
<div class="modal fade" id="selfHostedModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Get Started with Self-Hosted Version</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6 class="mb-3">System Requirements</h6>
                <ul class="list-unstyled">
                    <li>✓ PHP 7.4 or higher</li>
                    <li>✓ MySQL 5.7 or higher</li>
                    <li>✓ Apache/Nginx web server</li>
                    <li>✓ Composer package manager</li>
                </ul>

                <h6 class="mb-3 mt-4">Installation Steps</h6>
                <ol>
                    <li>Clone the repository from GitHub</li>
                    <li>Run composer install</li>
                    <li>Configure your database settings</li>
                    <li>Run database migrations</li>
                </ol>

                <div class="alert alert-info mt-4">
                    <h6 class="mb-2">Open Source License</h6>
                    <p class="mb-0">This software is released under the MIT License. You are free to:</p>
                    <ul class="mb-0">
                        <li>Use the software for any purpose</li>
                        <li>Change the software to suit your needs</li>
                        <li>Share the software with anyone</li>
                        <li>Commercialize the software</li>
                    </ul>
                </div>

                <div class="text-center mt-4">
                    <a href="https://github.com/bmackenty/daily_notes" class="btn btn-primary" target="_blank">
                        <i class="bi bi-github me-2"></i>View on GitHub
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hosted Modal -->
<div class="modal fade" id="hostedModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Get Started with Hosted Version</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="/submit-hosted-inquiry" method="POST" id="hostedInquiryForm">
                    <div class="mb-3">
                        <label class="form-label">School/Institution Name</label>
                        <input type="text" name="institution" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Contact Person</label>
                        <input type="text" name="contact_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" name="phone" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Expected Number of Users</label>
                        <select name="users" class="form-select" required>
                            <option value="">Select range</option>
                            <option value="1-50">1-50 users</option>
                            <option value="51-200">51-200 users</option>
                            <option value="201-500">201-500 users</option>
                            <option value="500+">500+ users</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Additional Comments</label>
                        <textarea name="comments" class="form-control" rows="3"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit Inquiry</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('hostedInquiryForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Prepare email content
    const formData = new FormData(this);
    const subject = 'Daily Notes Hosted Version Inquiry';
    let body = 'New Hosted Version Inquiry:\n\n';
    body += `Institution: ${formData.get('institution')}\n`;
    body += `Contact Name: ${formData.get('contact_name')}\n`;
    body += `Email: ${formData.get('email')}\n`;
    body += `Phone: ${formData.get('phone')}\n`;
    body += `Expected Users: ${formData.get('users')}\n`;
    body += `Comments: ${formData.get('comments')}\n`;

    // Open default email client
    window.location.href = `mailto:bmackenty@gmail.com?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
    
    // Close modal and show message
    const modal = bootstrap.Modal.getInstance(document.getElementById('hostedModal'));
    modal.hide();
    
    // Reset form
    this.reset();
    
    // Show success message
    alert('Thank you for your inquiry. An email has been prepared in your default email client.');
});
</script>

<?php require ROOT_PATH . '/app/Views/partials/footer.php'; ?> 