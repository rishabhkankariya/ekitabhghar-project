// Syllabus Data with PDF File Paths
const syllabusData = {
    "1 Year": {
        "1 Semester": [
            { name: "Mathematics I", pdf: "pdfs/mathematics_1_Paper.pdf" },
            { name: "Chemistry", pdf: "pdfs/Chemistry_Paper.pdf" },
            { name: "Communication Skills & English", pdf: "pdfs/Communication_Skills_Paper.pdf" },
            { name: "Physics 1", pdf: "pdfs/Applied_Physics_1_Paper.pdf" },
        ],
        "2 Semester": [
            { name: "Mathematics II", pdf: "pdfs/mathematics_2_Paper.pdf" },
            { name: "Introduction To IT System", pdf: "pdfs/Intro_to_IT_System_Paper.pdf" },
            { name: "FEEE", pdf: "pdfs/FEEE_Paper.pdf" },
            { name: "Physics 2", pdf: "pdfs/Applied_Physics_2_Paper.pdf" },
            { name: "Engineering Mechanics", pdf: "pdfs/.pdf" },
        ],
    },
    "2 Year": {
        "3 Semester": [
            { name: "Scripting Languages", pdf: "pdfs/.pdf" },
            { name: "Data Structures", pdf: "pdfs/.pdf" },
            { name: "Algorithm", pdf: "pdfs/.pdf" },
            { name: "Computer Programming", pdf: "pdfs/Programming_in_C_paper.pdf" },
            { name: "Computer System Organisation", pdf: "pdfs/.pdf" },
        ],
        "4 Semester": [
            { name: "Computer Networks", pdf: "pdfs/Computer_Network_Paper.pdf" },
            { name: "Operating Systems", pdf: "pdfs/Operating_System_Paper.pdf" },
            { name: "Introduction to DBMS", pdf: "pdfs/DBMS_Paper.pdf" },
            { name: "Web Technologies", pdf: "pdfs/Web-tech_Paper.pdf" },
            { name: "SSAD/Software Engineering", pdf: "pdfs/SSAD_Paper.pdf" },
        ],
    },
    "3 Year": {
        "5 Semester": [
            { name: "Operation Research", pdf: "pdfs/.pdf" },
            { name: "Intro. To E-governance", pdf: "pdfs/E-governance_Paper.pdf" },
            { name: "Internet of Things", pdf: "pdfs/.pdf" },
            { name: "Information Security ", pdf: "pdfs/Information_Security_Paper.pdf" },
            { name: "MultiMedia Technologies", pdf: "pdfs/.pdf" },
            { name: "AD.Computer Networks", pdf: "pdfs/.pdf" },
            { name: "Data Sciences", pdf: "pdfs/.pdf" },
            { name: "Renewable Energy tech.", pdf: "pdfs/Renewable_Energy_Paper.pdf" },
        ],
        "6 Semester": [
            { name: "Entrepreneurship & Start-up", pdf: "pdfs/Entrepreneurship_StartUps.pdf.pdf" },
            { name: "Mobile Computing", pdf: "pdfs/Mobile_Computing_Paper.pdf" },
            { name: "Network Forensics", pdf: "pdfs/.pdf" },
            { name: "Software Testing", pdf: "pdfs/Software_Testing_Paper.pdf" },
            { name: "Free & Open Source Software", pdf: "pdfs/.pdf" },
            { name: "Disaster Management", pdf: "pdfs/.pdf" },
            { name: "Project Management", pdf: "pdfs/.pdf" },
            { name: "Artifical Intelligence", pdf: "pdfs/artificial_Intelligence_Paper.pdf" },
            { name: "Engg.Eco & Accountancy", pdf: "pdfs/.pdf" },
            { name: "Indian Constitution", pdf: "pdfs/.pdf" },
        ],
    },
};

// Function to Open PDF in a New Tab
function openPDF(pdfPath) {
    window.open(pdfPath, '_blank');
}

// Render Syllabus Function
function renderSyllabus(data) {
    const yearsContainer = document.getElementById("yearsContainer");

    for (const year in data) {
        // Create Year Block
        const yearBlock = document.createElement("div");
        yearBlock.classList.add("year");

        // Add Year Heading
        const yearHeading = document.createElement("h2");
        yearHeading.innerText = year;
        yearBlock.appendChild(yearHeading);

        // Add Semesters
        for (const semester in data[year]) {
            const semesterBlock = document.createElement("div");
            semesterBlock.classList.add("semester");

            // Add Semester Heading
            const semesterHeading = document.createElement("h3");
            semesterHeading.innerText = semester;
            semesterBlock.appendChild(semesterHeading);

            // Add Subjects
            data[year][semester].forEach((subject) => {
                const subjectDiv = document.createElement("div");
                subjectDiv.classList.add("subject");
                subjectDiv.innerText = subject.name;

                // Make Subject Clickable to Open PDF in a New Tab
                subjectDiv.addEventListener("click", () => openPDF(subject.pdf));

                semesterBlock.appendChild(subjectDiv);
            });

            yearBlock.appendChild(semesterBlock);
        }

        yearsContainer.appendChild(yearBlock);
    }
}

// Initialize the Syllabus
renderSyllabus(syllabusData);
