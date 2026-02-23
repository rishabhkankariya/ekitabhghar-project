async function searchBooks() {
    const query = $("#search").val().trim();
    if (query === "") return;

    $("#results").html("");
    $("#loader").show();

    try {
        const url = `https://www.googleapis.com/books/v1/volumes?q=${encodeURIComponent(query)}&maxResults=10`;
        const response = await fetch(url);

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        $("#loader").hide();

        if (!data.items || data.items.length === 0) {
            $("#results").html("<p>No books found.</p>");
            return;
        }

        data.items.forEach(book => {
            const volumeInfo = book.volumeInfo;
            const title = volumeInfo.title || "Unknown Title";
            const authors = volumeInfo.authors ? volumeInfo.authors.join(", ") : "Unknown Author";
            const link = volumeInfo.previewLink || "#";
            const thumbnail = volumeInfo.imageLinks && volumeInfo.imageLinks.thumbnail
                ? volumeInfo.imageLinks.thumbnail
                : "placeholder.jpg";

            $("#results").append(`
                <div class="book">
                    <img src="${thumbnail}" alt="Book Cover" onerror="this.onerror=null;this.src='placeholder.jpg';">
                    <h3>${title}</h3>
                    <p><strong>Author:</strong> ${authors}</p>
                    <button class="download-btn" data-book-url="${link}">Select & Pay</button>
                </div>
            `);
        });
    } catch (error) {
        $("#loader").hide();
        console.error("Search Error:", error);

        let errorMsg = "Error fetching data. ";
        if (window.location.protocol === "file:") {
            errorMsg += "Please use localhost (XAMPP).";
        } else {
            errorMsg += "Check your connection or console for details.";
        }
        $("#results").html(`<p>${errorMsg}</p>`);
    }
}

// ✅ Use Event Delegation for dynamically added buttons
$(document).on("click", ".download-btn", function () {
    let book_url = $(this).data("book-url");

    $.ajax({
        url: "store_book.php",
        type: "POST",
        data: { book_url: book_url },
        success: function (response) {
            if (response === "success") {
                window.location.href = "payment.php"; // ✅ Redirect to payment after selecting book
            }
        }
    });
});
