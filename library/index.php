<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>KITABGHAR | Digital Library</title>

   <!-- SEO Meta Tags -->
   <meta name="description"
      content="Find and download Computer Science books. Access restricted to polytechnic students with ID.">
   <meta name="keywords" content="books, computer science, ebooks, download books">
   <meta name="author" content="Kitabghar">

   <!-- Favicon -->
   <link rel="icon" href="../img/kitabghar.png" type="image/x-icon">

   <!-- Tailwind CSS -->
   <script src="https://cdn.tailwindcss.com"></script>

   <!-- Fonts & Icons -->
   <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
      rel="stylesheet">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

   <!-- AOS Animation -->
   <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">

   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

   <script>
      tailwind.config = {
         darkMode: 'class',
         theme: {
            extend: {
               fontFamily: {
                  sans: ['"Plus Jakarta Sans"', 'sans-serif'],
               },
               animation: {
                  'fade-in-up': 'fadeInUp 0.8s ease-out forwards',
                  'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
               },
               keyframes: {
                  fadeInUp: {
                     '0%': { opacity: '0', transform: 'translateY(20px)' },
                     '100%': { opacity: '1', transform: 'translateY(0)' },
                  }
               }
            }
         }
      }
   </script>

   <style>
      /* Custom Scrollbar */
      ::-webkit-scrollbar {
         width: 8px;
      }

      ::-webkit-scrollbar-track {
         background: #f1f5f9;
      }

      ::-webkit-scrollbar-thumb {
         background: #cbd5e1;
         border-radius: 4px;
      }

      ::-webkit-scrollbar-thumb:hover {
         background: #94a3b8;
      }

      .book-card:hover {
         transform: translateY(-5px);
      }
   </style>
</head>

<body
   class="font-sans antialiased text-slate-800 bg-slate-50 dark:bg-slate-950 dark:text-slate-200 transition-colors duration-300">

   <!-- ================= HEADER (Copied & Adapted) ================= -->

   <!-- Top Bar -->
   <div class="bg-white dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 py-2 hidden md:block">
      <div class="max-w-7xl mx-auto px-4 flex justify-between items-center text-sm">
         <!-- Back to Home -->
         <a href="../index.php" class="flex items-center gap-2 text-slate-500 hover:text-blue-600 transition-colors">
            <i class="fa-solid fa-arrow-left"></i> Back to Home
         </a>

         <div class="flex items-center gap-6">
            <div class="flex items-center gap-3 text-slate-500 dark:text-slate-400">
               <a href="#" class="hover:text-blue-600 transition-colors"><i class="fa-brands fa-instagram"></i></a>
               <a href="#" class="hover:text-blue-600 transition-colors"><i class="fa-brands fa-facebook"></i></a>
               <a href="#" class="hover:text-green-500 transition-colors"><i class="fa-brands fa-whatsapp"></i></a>
            </div>
         </div>
      </div>
   </div>

   <!-- Navbar -->
   <nav
      class="sticky top-0 z-[100] bg-white/90 dark:bg-slate-900/90 backdrop-blur-lg border-b border-slate-200 dark:border-slate-800 shadow-md">
      <div class="max-w-7xl mx-auto px-4">
         <div class="flex items-center justify-between h-16 md:h-20">

            <!-- Logo -->
            <a href="../index.php" class="flex items-center gap-3">
               <img src="../img/kitabghar.png" alt="Logo" class="h-10 w-auto rounded-lg">
               <span class="font-black text-xl tracking-tight text-slate-900 dark:text-white hidden sm:block">Kitabghar
                  <span class="text-blue-600">Library</span></span>
            </a>

            <!-- Desktop Nav -->
            <div class="hidden md:flex items-center gap-1">
               <a href="../index.php"
                  class="px-4 py-2 rounded-lg text-slate-600 dark:text-slate-300 font-medium hover:text-blue-600 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">Home</a>
               <a href="../about.html"
                  class="px-4 py-2 rounded-lg text-slate-600 dark:text-slate-300 font-medium hover:text-blue-600 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">About</a>
               <a href="../syllabus.html"
                  class="px-4 py-2 rounded-lg text-slate-600 dark:text-slate-300 font-medium hover:text-blue-600 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">Syllabus</a>
            </div>

            <!-- Mobile Menu Button -->
            <button onclick="toggleSidebar()"
               class="md:hidden p-2 text-slate-700 dark:text-white rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
               <i class="fa-solid fa-bars text-2xl"></i>
            </button>

         </div>
      </div>
   </nav>

   <!-- Sidebar (Mobile) -->
   <div id="sidebar"
      class="fixed inset-y-0 left-0 w-72 bg-white dark:bg-slate-900 shadow-2xl z-[999] transform -translate-x-full transition-transform duration-300 ease-out flex flex-col h-full">
      <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
         <img src="../img/kitabghar.png" alt="Logo" class="h-8 rounded-lg">
         <button onclick="toggleSidebar()"
            class="w-8 h-8 flex items-center justify-center bg-slate-100 dark:bg-slate-800 rounded-full text-slate-500 hover:text-red-500"><i
               class="bi bi-x-lg"></i></button>
      </div>
      <div class="flex-1 overflow-y-auto p-4 space-y-1">
         <a href="../index.php"
            class="block px-4 py-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium">Home</a>
         <a href="../about.html"
            class="block px-4 py-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium">About
            Us</a>
         <a href="../syllabus.html"
            class="block px-4 py-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium">Syllabus</a>
      </div>
   </div>
   <div id="overlay" onclick="toggleSidebar()"
      class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-[990] hidden opacity-0 transition-opacity duration-300">
   </div>

   <!-- ================= MAIN CONTENT ================= -->

   <!-- Hero Search Section -->
   <section class="relative py-20 bg-gradient-to-br from-blue-900 via-indigo-900 to-slate-900 overflow-hidden">
      <div class="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
      <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-blue-500 rounded-full blur-3xl opacity-20"></div>

      <div class="max-w-4xl mx-auto px-4 relative z-10 text-center text-white">
         <span
            class="inline-block px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-blue-200 text-sm font-bold tracking-wider mb-6 animate-fade-in-up">DIGITAL
            LIBRARY</span>
         <h1 class="text-4xl md:text-6xl font-black mb-6 leading-tight animate-fade-in-up"
            style="animation-delay: 0.1s">
            Find Your Next <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-300">Great
               Read</span>
         </h1>
         <p class="text-lg text-blue-100/80 mb-10 max-w-2xl mx-auto animate-fade-in-up" style="animation-delay: 0.2s">
            Access thousands of computer science books, references, and educational resources. Access is restricted to students of the polytechnic college with a valid ID.
         </p>

         <!-- Search Box -->
         <div class="relative max-w-2xl mx-auto animate-fade-in-up" style="animation-delay: 0.3s">
            <div class="flex items-center bg-white rounded-2xl p-2 shadow-2xl shadow-blue-500/20">
               <div class="pl-4 text-slate-400"><i class="fa-solid fa-search text-xl"></i></div>
               <input type="text" id="search" placeholder="Search by title, author, or topic..."
                  class="w-full px-4 py-3 text-slate-800 text-lg outline-none placeholder:text-slate-400 bg-transparent"
                  autocomplete="off" onkeypress="if(event.key === 'Enter') searchBooks()">
               <button onclick="searchBooks()"
                  class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-blue-500/30 active:scale-95">
                  Search
               </button>
            </div>
         </div>
      </div>
   </section>

   <!-- Results Section -->
   <section class="py-16 min-h-[500px]">
      <div class="max-w-7xl mx-auto px-4">

         <!-- Loader -->
         <div id="loader" class="hidden text-center py-20">
            <div
               class="inline-block w-16 h-16 border-4 border-blue-100 border-t-blue-600 rounded-full animate-spin mb-4">
            </div>
            <p class="text-slate-500 font-medium">Searching our vast library...</p>
         </div>

         <!-- Empty State -->
         <div id="empty-state" class="text-center py-20">
            <div
               class="w-24 h-24 bg-blue-50 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-6">
               <i class="bi bi-book text-4xl text-blue-300"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2">Ready to explore?</h3>
            <p class="text-slate-500 dark:text-slate-400">Enter a keyword above to start searching for books.</p>
         </div>

         <!-- Results Grid -->
         <div id="results" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8"></div>
      </div>
   </section>

   <!-- ================= FOOTER ================= -->
   <footer class="bg-slate-900 border-t border-slate-800 pt-16 pb-8">
      <div class="max-w-7xl mx-auto px-4 text-center">
         <img src="../img/kitabghar.png" alt="Logo"
            class="h-12 w-auto mx-auto mb-6 rounded-xl opacity-80 grayscale hover:grayscale-0 transition-all shadow-xl">
         <p class="text-slate-500 text-sm">©
            <?php echo date("Y"); ?> Kitabghar. All rights reserved.
         </p>
      </div>
   </footer>

   <!-- Scripts -->
   <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
   <script>
      AOS.init({ once: true });

      // Sidebar Toggle
      window.toggleSidebar = function () {
         const sidebar = document.getElementById("sidebar");
         const overlay = document.getElementById("overlay");
         sidebar.classList.toggle("-translate-x-full");

         if (!sidebar.classList.contains("-translate-x-full")) {
            overlay.classList.remove("hidden");
            setTimeout(() => overlay.classList.remove("opacity-0"), 10);
         } else {
            overlay.classList.add("opacity-0");
            setTimeout(() => overlay.classList.add("hidden"), 300);
         }
      }

      // Search Logic
      function searchBooks() {
         var query = document.getElementById("search").value.trim();
         if (query === "") return;

         document.getElementById("loader").classList.remove("hidden");
         document.getElementById("empty-state").classList.add("hidden");
         document.getElementById("results").innerHTML = "";

         $.ajax({
            url: `https://www.googleapis.com/books/v1/volumes?q=${query}&maxResults=12`,
            type: "GET",
            success: function (data) {
               document.getElementById("loader").classList.add("hidden");

               if (!data.items || data.items.length === 0) {
                  document.getElementById("results").innerHTML = `
                        <div class="col-span-full text-center py-10">
                            <p class="text-slate-500 text-lg">No books found matching "${query}".</p>
                        </div>`;
                  return;
               }

               data.items.forEach((book, index) => {
                  let volumeInfo = book.volumeInfo;
                  let title = volumeInfo.title || "Unknown Title";
                  let authors = volumeInfo.authors ? volumeInfo.authors.join(", ") : "Unknown Author";
                  let desc = volumeInfo.description ? volumeInfo.description.substring(0, 100) + "..." : "No description available.";
                  let link = volumeInfo.previewLink || "#";
                  let thumbnail = volumeInfo.imageLinks && volumeInfo.imageLinks.thumbnail
                     ? volumeInfo.imageLinks.thumbnail.replace('http:', 'https:')
                     : "https://via.placeholder.com/128x192.png?text=No+Cover";

                  let delay = index * 100;

                  document.getElementById("results").innerHTML += `
                          <div class="book-card group bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-100 dark:border-slate-800 shadow-sm hover:shadow-xl transition-all duration-300 flex gap-6" data-aos="fade-up" data-aos-delay="${delay}">
                              <div class="shrink-0 w-24 h-36 rounded-lg overflow-hidden shadow-md group-hover:shadow-lg transition-transform group-hover:scale-105">
                                  <img src="${thumbnail}" alt="Cover" class="w-full h-full object-cover">
                              </div>
                              <div class="flex flex-col flex-1">
                                  <h3 class="font-bold text-slate-900 dark:text-white text-lg leading-tight mb-1 line-clamp-2">${title}</h3>
                                  <p class="text-xs text-blue-600 font-semibold uppercase tracking-wider mb-2 line-clamp-1">${authors}</p>
                                  <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed mb-4 line-clamp-3 flex-1">${desc}</p>
                                  <button class="download-btn w-full py-2 bg-slate-100 dark:bg-slate-800 hover:bg-blue-600 dark:hover:bg-blue-600 text-slate-700 dark:text-slate-300 hover:text-white rounded-lg font-semibold text-sm transition-all" data-book-url="${link}">
                                      <i class="bi bi-book-half mr-2"></i> View Details
                                  </button>
                              </div>
                          </div>
                      `;
               });
            },
            error: function () {
               document.getElementById("loader").classList.add("hidden");
               alert("Error fetching data. Please try again.");
            }
         });
      }

      // Handle Selection
      $(document).on("click", ".download-btn", function () {
         let book_url = $(this).data("book-url");

         // For now, open directly or implement your store logic
         window.open(book_url, '_blank');

         // Original logic preserved if needed:
         /*
         $.ajax({
             url: "store_book.php",
             type: "POST",
             data: { book_url: book_url },
             success: function (response) {
                 if (response === "success") {
                     window.location.href = "payment.php";
                 }
             }
         });
         */
      });
   </script>
</body>

</html>
