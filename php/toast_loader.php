<?php
session_start();
$toast = $_SESSION['library_login_toast'] ?? null;
unset($_SESSION['library_login_toast']);

if ($toast):
?>
<style>
    #libraryToast {
        position: fixed;
        top: 20px;
        right: 20px;
        background-color: <?= $toast['type'] === 'error' ? '#f44336' : '#4CAF50' ?>;
        color: white;
        padding: 15px 25px;
        border-radius: 6px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        font-size: 14px;
        z-index: 9999;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: opacity 0.5s ease;
    }

    .spinner-small {
        width: 16px;
        height: 16px;
        border: 2px solid white;
        border-top: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>

<div id="libraryToast">
    <?= htmlspecialchars($toast['message']) ?>
    <?php if ($toast['type'] === 'success'): ?>
        <div class="spinner-small"></div>
    <?php endif; ?>
</div>

<script>
    setTimeout(function () {
        const toast = document.getElementById('libraryToast');
        if (toast) {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 500);
        }
    }, 4000); // Hide after 4s
</script>
<?php endif; ?>
