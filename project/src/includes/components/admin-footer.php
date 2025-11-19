            </main>
        </div>
    </div>

    <script src="../assets/js/modal.js"></script>
    <script src="../assets/js/notifications.js"></script>
    <script>
        const sidebarToggle = document.getElementById('sidebarToggle');
        const adminSidebar = document.getElementById('adminSidebar');

        if (sidebarToggle && adminSidebar) {
            sidebarToggle.addEventListener('click', () => {
                adminSidebar.classList.toggle('show');
            });

            document.addEventListener('click', (e) => {
                if (!adminSidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                    adminSidebar.classList.remove('show');
                }
            });
        }
    </script>
</body>
</html>

