        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Notification bell functionality
        document.querySelector('.notification-bell').addEventListener('click', function() {
            alert('Bạn có 3 thông báo mới!');
        });
        
        // Auto-hide alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
        
        // Active menu item highlighting
        document.addEventListener('DOMContentLoaded', function() {
            const currentPage = window.location.href;
            const menuItems = document.querySelectorAll('.nav-link');
            
            menuItems.forEach(item => {
                if (item.href === currentPage) {
                    item.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>