</main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>
    <!-- Custom JS -->
    <script>
        // Notification functionality
        $(document).ready(function() {
            // Toggle notification dropdown
            $('#notificationBell').click(function(e) {
                e.stopPropagation();
                $('#notificationMenu').toggleClass('show');
                loadNotifications();
            });
            
            // Close dropdown when clicking outside
            $(document).click(function() {
                $('#notificationMenu').removeClass('show');
            });
            
            // Load notifications
            function loadNotifications() {
                $.ajax({
                    url: 'index.php?controller=thongbao&action=loadNotifications',
                    type: 'GET',
                    success: function(response) {
                        $('#notificationList').html(response);
                    },
                    error: function() {
                        $('#notificationList').html(
                            '<div class="text-center p-3 text-danger">' +
                            'Lỗi tải thông báo' +
                            '</div>'
                        );
                    }
                });
            }
            
            // Mark notification as read
            $(document).on('click', '.notification-item', function() {
                const maThongBao = $(this).data('id');
                const url = $(this).data('url');
                
                // Mark as read via AJAX
                $.ajax({
                    url: 'index.php?controller=thongbao&action=markAsRead',
                    type: 'POST',
                    data: { maThongBao: maThongBao },
                    success: function() {
                        // Redirect to notification detail
                        window.location.href = url;
                    }
                });
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