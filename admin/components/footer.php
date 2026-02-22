<?php
// admin/components/footer.php
?>
</div> <!-- Close .flex (Sidebar + Main) -->

<!-- Toast Container -->
<div id="toast"
    class="fixed top-24 right-6 min-w-[300px] max-w-[400px] p-4 rounded-2xl shadow-2xl transition-all duration-500 transform translate-x-[150%] opacity-0 z-[9999] flex items-center justify-between pointer-events-auto">
    <div class="flex items-center gap-3">
        <div id="toast-icon" class="w-8 h-8 rounded-full flex items-center justify-center text-white bg-white/20"></div>
        <p id="toast-message" class="text-sm font-medium text-white"></p>
    </div>
    <button onclick="closeToast()" class="text-white/60 hover:text-white"><i class="bi bi-x-lg"></i></button>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init({ duration: 800, once: true });

    // Sidebar logic
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
        });
    }

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', (e) => {
        if (window.innerWidth < 1024 && !sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
            sidebar.classList.add('-translate-x-full');
        }
    });

    // Session handling
    let inactivityTime = 180;
    let countdownDisplay = document.getElementById("session-countdown");

    function updateCountdown() {
        if (inactivityTime >= 0 && countdownDisplay) {
            countdownDisplay.innerText = inactivityTime + 's';
            inactivityTime--;
        } else if (inactivityTime < 0) {
            window.location.href = "php/logout.php";
        }
    }
    setInterval(updateCountdown, 1000);

    function resetTimer() {
        inactivityTime = 180;
    }
    ['mousemove', 'keydown', 'click', 'scroll'].forEach(evt => document.addEventListener(evt, resetTimer));

    // Toast handling
    const toastData = <?php echo json_encode($toast ?? $_SESSION['toast'] ?? null); ?>;
    <?php unset($_SESSION['toast']); ?>

    function showToast(message, type = 'info') {
        const toast = document.getElementById('toast');
        const toastMsg = document.getElementById('toast-message');
        const toastIcon = document.getElementById('toast-icon');

        toastMsg.innerText = message;

        if (type === 'success') {
            toast.style.background = 'linear-gradient(135deg, #10b981, #059669)';
            toastIcon.innerHTML = '<i class="bi bi-check2-circle"></i>';
        } else if (type === 'error') {
            toast.style.background = 'linear-gradient(135deg, #ef4444, #dc2626)';
            toastIcon.innerHTML = '<i class="bi bi-exclamation-triangle"></i>';
        } else {
            toast.style.background = 'linear-gradient(135deg, #3b82f6, #2563eb)';
            toastIcon.innerHTML = '<i class="bi bi-info-circle"></i>';
        }

        toast.classList.remove('translate-x-[150%]', 'opacity-0');
        toast.classList.add('translate-x-0', 'opacity-100');

        setTimeout(closeToast, 5000);
    }

    function closeToast() {
        const toast = document.getElementById('toast');
        toast.classList.add('translate-x-[150%]', 'opacity-0');
        toast.classList.remove('translate-x-0', 'opacity-100');
    }

    if (toastData) {
        showToast(toastData.message, toastData.type);
    }
</script>
</body>

</html>
