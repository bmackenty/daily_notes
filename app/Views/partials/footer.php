

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-auto">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-center">
                    <h5>About</h5>
                    <p>
                    Daily Learning Notes give you a quick, clear summary of what we covered in class each day. 
                    They’re here to help you review important ideas, remember what you learned, and stay on 
                    track — even if you missed a lesson. Each note connects to our learning goals, key concepts, 
                    and extra resources so you can study in a way that works best for you.
                    </p>
                </div>
                <div class="col-md-6 text-center">
                    <h5>Contact</h5>
                    <p>Email: bmackenty@gmail.com</p>
                </div>
                
            </div>
            <hr class="mt-4 mb-3">
            <div class="text-center">
                <p class="mb-0">&copy; <?= date('Y') ?> Bill MacKenty. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Your existing scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    </script>
    <script src="/public/js/teacher-profiles.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/rowreorder/1.4.1/js/dataTables.rowReorder.min.js"></script>
</body>
</html> 