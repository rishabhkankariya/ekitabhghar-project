/**
 * E-Kitabghar Global Loading Overlay
 * Dynamically injects a beautiful backdrop-blurred spinner and overlay to block clicks during slow processes.
 */

(function() {
    // Inject HTML & CSS when the DOM is loaded
    function injectLoader() {
        if (document.getElementById('globalLoadingOverlay')) return;

        const overlayHtml = `
        <div id="globalLoadingOverlay" style="display: none; position: fixed; inset: 0; bg-backdrop: blur(12px); background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); z-index: 9999999; justify-content: center; align-items: center; flex-direction: column; transition: all 0.3s ease;">
            <div style="background: white; padding: 40px 30px; border-radius: 28px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); text-align: center; max-width: 360px; width: 85%; transform: scale(0.9); animation: globalOverlayScale 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; border: 1px solid rgba(255,255,255,0.8);">
                <div style="position: relative; width: 64px; height: 64px; margin: 0 auto 24px;">
                    <!-- Outer Ring -->
                    <div style="box-sizing: border-box; position: absolute; width: 100%; height: 100%; border: 6px solid #e2e8f0; border-radius: 50%;"></div>
                    <!-- Spinning Segment -->
                    <div style="box-sizing: border-box; position: absolute; width: 100%; height: 100%; border: 6px solid transparent; border-top-color: #4f46e5; border-radius: 50%; animation: globalSpin 1s cubic-bezier(0.5, 0.1, 0.4, 0.9) infinite;"></div>
                </div>
                <h3 style="margin: 0 0 10px; color: #0f172a; font-size: 20px; font-weight: 800; font-family: system-ui, -apple-system, sans-serif; letter-spacing: -0.5px;">Please Wait</h3>
                <p style="margin: 0; color: #64748b; font-size: 14px; line-height: 1.6; font-family: system-ui, -apple-system, sans-serif; font-weight: 500;" id="globalLoadingOverlayMsg">Processing your request...</p>
            </div>
        </div>
        <style>
        @keyframes globalSpin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        @keyframes globalOverlayScale {
            to { transform: scale(1); }
        }
        </style>
        `;

        const container = document.createElement('div');
        container.innerHTML = overlayHtml;
        document.body.appendChild(container);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', injectLoader);
    } else {
        injectLoader();
    }
})();

/**
 * Display the global loading overlay with a custom message.
 * @param {string} msg Message to show on the overlay.
 */
function showGlobalLoader(msg = "Processing your request...") {
    const overlay = document.getElementById('globalLoadingOverlay');
    const msgEl = document.getElementById('globalLoadingOverlayMsg');
    if (overlay) {
        if (msgEl) msgEl.textContent = msg;
        overlay.style.display = 'flex';
    }
}

/**
 * Hide the global loading overlay.
 */
function hideGlobalLoader() {
    const overlay = document.getElementById('globalLoadingOverlay');
    if (overlay) {
        overlay.style.display = 'none';
    }
}
