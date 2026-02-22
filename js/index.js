// Google Translate Initialization
function googleTranslateElementInit() {
    new google.translate.TranslateElement(
        {
            pageLanguage: 'en', // Default language
            includedLanguages: 'hi,en,as,bn,gu,kn,ml,mr,or,pa,ta,te,ur,ks,sd,sat,sa,mai,bho,mlg,ne',
            layout: google.translate.TranslateElement.InlineLayout.SIMPLE
        },
        'google_translate_element'
    );
}
document.addEventListener("DOMContentLoaded", function () {
    const increaseBtn = document.getElementById("increase-font");
    const decreaseBtn = document.getElementById("decrease-font");
    const resetBtn = document.getElementById("reset-font");
    const elements = document.querySelectorAll("body, p, span, div, a, h1, h2, h3, h4, h5, h6");

    let minFactor = 0.8; // 80% of the original size
    let maxFactor = 1.4; // 140% of the original size
    let scaleFactor = 1; // Default scale (100% of original size)

    // Store original sizes
    let originalSizes = new Map();
    elements.forEach(el => {
        originalSizes.set(el, parseFloat(getComputedStyle(el).fontSize));
    });

    // Load stored scale factor
    let storedFactor = localStorage.getItem("scaleFactor");
    if (storedFactor) {
        scaleFactor = parseFloat(storedFactor);
        applyScale(scaleFactor);
    }

    function applyScale(factor) {
        elements.forEach(el => {
            let originalSize = originalSizes.get(el);
            el.style.fontSize = (originalSize * factor) + "px";
        });
        localStorage.setItem("scaleFactor", factor);
    }

    if (increaseBtn) {
        increaseBtn.addEventListener("click", function () {
            if (scaleFactor < maxFactor) {
                scaleFactor += 0.1;
                applyScale(scaleFactor);
            }
        });
    }

    if (decreaseBtn) {
        decreaseBtn.addEventListener("click", function () {
            if (scaleFactor > minFactor) {
                scaleFactor -= 0.1;
                applyScale(scaleFactor);
            }
        });
    }

    if (resetBtn) {
        resetBtn.addEventListener("click", function () {
            scaleFactor = 1;
            applyScale(scaleFactor);
        });
    }
});
// Sidebar Toggle with Overlay
function toggleSidebar() {
    let sidebar = document.getElementById("sidebar");
    let overlay = document.getElementById("overlay");

    if (sidebar && overlay) {
        sidebar.classList.toggle("active");
        overlay.classList.toggle("active");

        if (sidebar.classList.contains("active")) {
            overlay.style.display = "block";
            setTimeout(() => { overlay.style.opacity = "1"; }, 10);
        } else {
            overlay.style.opacity = "0";
            setTimeout(() => { overlay.style.display = "none"; }, 300);
        }
    }
}

// Toggle Dropdown Menu
function toggleDropdown() {
    let dropdown = document.querySelector(".ek-dropdown");
    if (dropdown) {
        dropdown.classList.toggle("active");
    }
}

// Page Fade-in Effect
document.addEventListener("DOMContentLoaded", function () {
    document.body.style.opacity = "0";
    setTimeout(() => {
        document.body.style.transition = "opacity 1.5s";
        document.body.style.opacity = "1";
    }, 10);
});
// Scroll to Top Button
document.addEventListener("DOMContentLoaded", function () {
    let scrollTopBtn = document.getElementById("scrollTopBtn");

    if (scrollTopBtn) {
        window.addEventListener("scroll", function () {
            if (window.scrollY > window.innerHeight / 2) {
                scrollTopBtn.style.display = "flex";
            } else {
                scrollTopBtn.style.display = "none";
            }
        });

        scrollTopBtn.addEventListener("click", function () {
            window.scrollTo({ top: 0, behavior: "smooth" });
        });
    }
});
$(document).ready(function () {
    setTimeout(function () {
        $(".main-header").addClass("animate-header");
        $(".logo-text").addClass("animate-text");
    }, 300); // Small delay for a smoother effect
});
document.addEventListener("DOMContentLoaded", function () {
    let header = document.querySelector(".header");
    if (header) {
        header.classList.add("header-animate"); // Add animation class on load
    }
});
