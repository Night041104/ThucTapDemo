</div> <footer class="bg-white py-3 mt-auto border-top">
            <div class="container-fluid text-center">
                <span class="text-muted small">Copyright &copy; FPT Admin Dashboard 2025</span>
            </div>
        </footer>

    </div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const body = document.body;
            const toggleBtn = document.getElementById('sidebarToggle');
            const sidebarState = localStorage.getItem('sidebar_state');

            // 1. Khôi phục trạng thái từ LocalStorage
            if (window.innerWidth > 768) { // Chỉ áp dụng trên PC
                if (sidebarState === 'collapsed') {
                    body.classList.add('sidebar-toggled');
                }
            }

            // 2. Xử lý sự kiện click Toggle
            toggleBtn.addEventListener('click', function() {
                if (window.innerWidth > 768) {
                    // Trên PC: Thu nhỏ / Mở rộng
                    body.classList.toggle('sidebar-toggled');
                    
                    // Lưu trạng thái
                    if (body.classList.contains('sidebar-toggled')) {
                        localStorage.setItem('sidebar_state', 'collapsed');
                    } else {
                        localStorage.setItem('sidebar_state', 'expanded');
                    }
                } else {
                    // Trên Mobile: Hiện / Ẩn Sidebar
                    body.classList.toggle('sidebar-mobile-open');
                }
            });

            // 3. Tự động đóng sidebar mobile khi click ra ngoài
            document.addEventListener('click', function(event) {
                const sidebar = document.getElementById('sidebar');
                if (window.innerWidth <= 768 && 
                    body.classList.contains('sidebar-mobile-open') && 
                    !sidebar.contains(event.target) && 
                    !toggleBtn.contains(event.target)) {
                    body.classList.remove('sidebar-mobile-open');
                }
            });
        });
    </script>
</body>
</html>