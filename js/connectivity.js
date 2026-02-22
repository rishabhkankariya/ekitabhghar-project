/**
 * Connectivity Handler for Kitabghar
 * Handles Offline/Online states with UI notifications.
 */
document.addEventListener('DOMContentLoaded', () => {
    // 1. Create Offline Banner 
    const offlineDiv = document.createElement('div');
    offlineDiv.id = 'connectivity-status';
    offlineDiv.className = 'fixed top-0 left-0 w-full z-[10000] transform transition-transform duration-500 translate-y-[-100%]';
    // Using inline styles/classes assuming Tailwind is present. If not, fallback styles.
    offlineDiv.innerHTML = `
        <div style="background-color: #1a1a1a; color: white;" class="shadow-2xl border-b-2 border-red-500 flex items-center justify-center p-3 gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#ef4444" class="bi bi-wifi-off animate-pulse" viewBox="0 0 16 16">
              <path d="M10.706 3.294A12.545 12.545 0 0 0 8 3C5.259 3 2.723 3.882.663 5.379a.485.485 0 0 0-.048.736.518.518 0 0 0 .668.05A11.448 11.448 0 0 1 8 4c2.507 0 4.866.869 6.717 2.165a.518.518 0 0 0 .668-.05.485.485 0 0 0-.048-.736A12.546 12.546 0 0 0 10.706 3.294zM8 6a8.54 8.54 0 0 0-6.29 2.766.485.485 0 0 0-.063.708.518.518 0 0 0 .736.063 7.502 7.502 0 0 1 11.234 0 .518.518 0 0 0 .736-.063.485.485 0 0 0-.063-.708A8.54 8.54 0 0 0 8 6zm3.336 4.336A5.5 5.5 0 0 0 8 8c-1.286 0-2.476.435-3.335 1.054a.485.485 0 0 0-.08.711.518.518 0 0 0 .723.076A4.466 4.466 0 0 1 8 9c1.026 0 1.97.344 2.692.911a.518.518 0 0 0 .723-.076.485.485 0 0 0-.08-.711zM7 11.5c0-.663.224-1.272.671-1.761a.526.526 0 0 0-.056-.765.488.488 0 0 0-.745.05A3.49 3.49 0 0 0 5 12.5c0 .358.077.697.195 1.015l5.29-6.909c-.29-.12-.596-.21-.91-.256a.52.52 0 0 0-.575.498v.02c.005.27.214.502.505.518l.005.002A2.5 2.5 0 0 1 8 10c-.39 0-.756.096-1.08.261l-5.646-7.39a.5.5 0 0 0-.766.64l12.986 17a.5.5 0 0 0 .77-.63l-.707-.925A3.48 3.48 0 0 0 11 12.5a3.49 3.49 0 0 0-.671-2.054l-.794-1.04A2.49 2.49 0 0 1 10.5 12a2.5 2.5 0 0 1-1.353 2.22l-.995-1.303a1.498 1.498 0 0 0 .848-.417zM7.5 14.5c0 .12.02.235.056.344l1.192-1.56a1.5 1.5 0 0 0-1.248 1.216z"/>
            </svg>
            <span class="font-bold tracking-widest text-xs uppercase" style="font-family: sans-serif;">No Internet Connection</span>
        </div>
    `;
    document.body.appendChild(offlineDiv);

    // 2. Create Online Toast
    const onlineDiv = document.createElement('div');
    onlineDiv.id = 'connectivity-online';
    onlineDiv.className = 'fixed top-24 right-4 z-[10000] transform transition-all duration-500 translate-x-32 opacity-0';
    onlineDiv.innerHTML = `
        <div style="background-color: white; border-left: 4px solid #22c55e;" class="shadow-2xl rounded-r px-5 py-3 flex items-center gap-4">
            <div class="bg-green-100 p-2 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#16a34a" class="bi bi-wifi" viewBox="0 0 16 16">
                  <path d="M15.384 6.115a.485.485 0 0 0-.047-.736A12.444 12.444 0 0 0 8 3C5.259 3 2.723 3.882.663 5.379a.485.485 0 0 0-.048.736.518.518 0 0 0 .668.05A11.448 11.448 0 0 1 8 4c2.507 0 4.866.869 6.717 2.165a.518.518 0 0 0 .668-.05.485.485 0 0 0 .05-.736z"/>
                  <path d="M13.336 4.336A11.448 11.448 0 0 1 8 4c-2.507 0-4.866.869-6.717 2.165A12.545 12.545 0 0 1 8 3c2.759 0 5.295.882 7.336 2.379.74.538 1.01 1.077.048.736z" fill="none"/>
                  <path d="M11.234 8.766a.485.485 0 0 0-.063-.708 8.54 8.54 0 0 0-6.29-2.766.485.485 0 0 0-.063.708.518.518 0 0 0 .736.063 7.502 7.502 0 0 1 11.234 0 .518.518 0 0 0 .736-.063z"/>
                  <path d="M9.692 9.911a.485.485 0 0 0-.08-.711 5.5 5.5 0 0 0-3.335-1.054.485.485 0 0 0-.08.711.518.518 0 0 0 .723.076 4.466 4.466 0 0 1 5.398 0 .518.518 0 0 0 .723-.076z"/>
                  <path d="M8 11.5a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                </svg>
            </div>
            <div>
                <h4 class="font-bold text-xs uppercase text-gray-800" style="font-family: sans-serif;">Back Online</h4>
                <p class="text-[10px] text-gray-500">Connection restored.</p>
            </div>
        </div>
    `;
    document.body.appendChild(onlineDiv);

    function updateStatus() {
        if (!navigator.onLine) {
            // Show Offline Banner
            offlineDiv.classList.remove('translate-y-[-100%]');
        } else {
            // Hide Offline Banner
            offlineDiv.classList.add('translate-y-[-100%]');
        }
    }

    window.addEventListener('online', () => {
        updateStatus();
        // Show Online Toast
        onlineDiv.classList.remove('translate-x-32', 'opacity-0');

        // Hide Toast after 3s
        setTimeout(() => {
            onlineDiv.classList.add('translate-x-32', 'opacity-0');
        }, 3000);
    });

    window.addEventListener('offline', () => {
        updateStatus();
    });

    // Check on Load
    updateStatus();
});
