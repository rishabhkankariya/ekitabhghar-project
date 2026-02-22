<?php
session_start();
include '../../php/connection.php';

$semester = isset($_GET['semester']) ? $_GET['semester'] : 'firstsem';
$is_logged_in = isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true;

// Define a mapping for semester display names
$semester_names = [
    'firstsem' => '1st Semester',
    'secondsem' => '2nd Semester',
    'thirdsem' => '3rd Semester',
    'fourthsem' => '4th Semester',
    'fifthsem' => '5th Semester',
    'sixthsem' => '6th Semester'
];
$display_semester = $semester_names[$semester] ?? 'Semester Notes';

$query = "SELECT id, subject_name, image_url, notes_link FROM student_notes WHERE semester='$semester'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $imgPath = $row['image_url'] ? $row['image_url'] : 'images/default_cover.jpg';
        $notesLink = $row['notes_link'];

        echo '
        <div class="group relative bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-500 border border-slate-100 flex flex-col h-full" data-aos="fade-up">
            <!-- Distinct Badge -->
            <div class="absolute top-4 left-4 z-20 bg-orange-600/90 backdrop-blur-md px-3 py-1.5 rounded-full text-[10px] font-black text-white uppercase tracking-wider shadow-lg">
                ' . $display_semester . '
            </div>
            
            <!-- Book Cover Image Section -->
            <div class="relative flex-none aspect-[2/3] overflow-hidden bg-slate-100">
                <img src="' . $imgPath . '" alt="' . htmlspecialchars($row['subject_name']) . '"
                     class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105" />
                
                <!-- Dual Layer Gradient for Max Readability -->
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/40 to-transparent opacity-80 group-hover:opacity-95 transition-opacity duration-500"></div>
                <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-black via-black/20 to-transparent opacity-60"></div>
                
                <!-- Hover Center Button (using pointer-events-none on wrapper to allow clicks underneath) -->
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-30">
                    <button onclick=\'openBook(' . json_encode($notesLink) . ', ' . json_encode($imgPath) . ')\' 
                            class="bg-white text-slate-900 w-16 h-16 rounded-full flex items-center justify-center shadow-2xl transform scale-75 opacity-0 group-hover:scale-100 group-hover:opacity-100 group-hover:rotate-12 transition-all duration-500 pointer-events-auto border-4 border-orange-600">
                        <i class="bi bi-book-half text-2xl text-orange-600"></i>
                    </button>
                </div>
                
                <!-- Bottom Content (Title & Actions) - z-index high enough for buttons -->
                <div class="absolute bottom-0 left-0 right-0 p-5 z-40">
                    <h3 class="text-xl font-extrabold text-white leading-tight mb-4 line-clamp-2 drop-shadow-2xl font-outfit tracking-tight">
                        ' . htmlspecialchars($row['subject_name']) . '
                    </h3>
                    <div class="flex items-center gap-2 sm:gap-3">
                        <button onclick=\'openBook(' . json_encode($notesLink) . ', ' . json_encode($imgPath) . ')\' 
                                class="flex-1 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/30 text-white py-2.5 px-3 rounded-xl text-[11px] font-bold flex items-center justify-center gap-2 transition-all active:scale-95">
                            <i class="bi bi-eye-fill"></i> View
                        </button>
                        <button onclick=\'handleDownload(' . json_encode((string) $row['id']) . ', ' . ($is_logged_in ? 'true' : 'false') . ')\' 
                                class="flex-1 bg-orange-600 hover:bg-orange-500 text-white py-2.5 px-3 rounded-xl text-[11px] font-bold flex items-center justify-center gap-2 shadow-lg shadow-orange-600/40 transition-all active:scale-95">
                            <i class="bi bi-cloud-arrow-down-fill text-sm"></i> Save
                        </button>
                    </div>
                </div>
            </div>

            <!-- Meta Footer Info -->
            <div class="p-4 bg-slate-50/80 backdrop-blur-sm flex justify-between items-center border-t border-slate-100 mt-auto">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center border border-orange-100">
                        <i class="bi bi-file-earmark-pdf-fill text-orange-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase leading-none mb-1">Type</p>
                        <p class="text-[11px] font-bold text-slate-700 leading-none">PDF Note</p>
                    </div>
                </div>
                <div class="flex flex-col items-end">
                    <div class="flex items-center gap-1.5 mb-1">
                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Active</span>
                    </div>
                    <p class="text-[9px] font-bold text-slate-400">Kitabghar Verified</p>
                </div>
            </div>
        </div>



        ';
    }
} else {
    echo '
    <div class="col-span-full flex flex-col items-center justify-center py-20 text-gray-400" data-aos="fade-up">
        <i class="bi bi-journal-x text-6xl mb-4"></i>
        <p class="text-xl font-medium">No notes available for this semester yet.</p>
    </div>';
}
mysqli_close($conn);
?>
