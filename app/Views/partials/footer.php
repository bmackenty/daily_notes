<div class="footer mt-5 bg-dark text-light py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h5>About</h5>
                <p>Daily Notes is a system designed to help students manage and organize their daily meeting notes efficiently.</p>
            </div>
            <div class="col-md-4">
                <h5>Contact</h5>
                <p>Email: bmackenty@gmail.com</p>
                
            </div>
            <div class="col-md-4">
                <h5>Interested in using this in your classroom?</h5>
                <p>Choose from our flexible plans to suit your needs. <a href="/pricing" class="text-light">View plans</a>.</p>
            </div>
        </div>
        <hr class="mt-4 mb-3">
        <div class="text-center">
            <p class="mb-0">&copy; <?= date('Y') ?> Bill MacKenty. All rights reserved.</p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Initialize all tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
</script>
</body>
</html> 