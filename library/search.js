function searchBooks() {
    let query = $("#search").val().trim();

    if (query === "") return; // ✅ No alert, just ignore empty searches

    $("#results").html(""); // ✅ Clear previous results
    $("#loader").show(); // ✅ Show loader while searching

    $.ajax({
        url: `https://www.googleapis.com/books/v1/volumes?q=${encodeURIComponent(query)}&maxResults=10`,
        type: "GET",
        success: function (data) {
            $("#loader").hide(); // ✅ Hide loader when done

            if (!data.items || data.items.length === 0) {
                $("#results").html("<p>No books found.</p>"); // ✅ No emoji, simple message
                return;
            }

            data.items.forEach(book => {
                let title = book.volumeInfo.title || "Unknown Title";
                let authors = book.volumeInfo.authors ? book.volumeInfo.authors.join(", ") : "Unknown Author";
                let link = book.volumeInfo.previewLink || "#";

                // ✅ Ensure book cover is always available
                let thumbnail = book.volumeInfo.imageLinks && book.volumeInfo.imageLinks.thumbnail
                    ? book.volumeInfo.imageLinks.thumbnail
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
        },
        error: function () {
            $("#loader").hide(); // ✅ Hide loader if error occurs
            $("#results").html("<p>Error fetching data. Try again later.</p>"); // ✅ No alert, clean UI
        }
    });
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
