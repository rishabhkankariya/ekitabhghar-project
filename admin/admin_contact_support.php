<?php
require '../php/connection.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Admin Contact Support</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-gray-100">

  <!-- Toast Container -->
  <div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-2"></div>

  <div class="max-w-6xl mx-auto py-8 px-4">
    <h1 class="text-3xl font-bold text-indigo-600 text-center mb-6">Admin Contact Support</h1>

    <div class="flex justify-start mb-6">
      <a href="adminpanel.php"
        class="bg-indigo-600 text-white py-2 px-4 rounded-lg shadow-md hover:bg-indigo-700 transition">
        <i class="bi bi-arrow-left-circle me-2"></i>Back to Home
      </a>
    </div>

    <div id="messageList" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <!-- Messages will be dynamically loaded here -->
    </div>
  </div>

  <!-- Reply Modal -->
  <div id="replyModal" class="fixed inset-0 hidden bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg w-full max-w-md p-6 shadow-lg relative">
      <h2 class="text-xl font-semibold text-gray-800 mb-2">Send Reply</h2>
      <div class="text-sm text-gray-600 mb-2">
        To: <span id="recipientName" class="font-medium"></span> &lt;<span id="recipientEmail"></span>&gt;
      </div>

      <!-- Subject input -->
      <input type="text" id="modalSubject" placeholder="Enter subject..."
        class="w-full p-2 mb-3 border border-gray-300 rounded focus:ring-2 focus:ring-indigo-400">

      <!-- Message input -->
      <textarea id="modalMessage" rows="5" placeholder="Type your message..."
        class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-indigo-400"></textarea>

      <div class="flex justify-end gap-3 mt-4">
        <button onclick="closeModal()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 text-sm">Cancel</button>
        <button onclick="sendEmail()"
          class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-sm">Send</button>
      </div>
      <button onclick="closeModal()"
        class="absolute top-2 right-3 text-xl text-gray-400 hover:text-black">&times;</button>
    </div>
  </div>

  <script>
    let currentEmail = '';

    function openModal(email, name) {
      document.getElementById('recipientEmail').textContent = email;
      document.getElementById('recipientName').textContent = name;
      document.getElementById('modalMessage').value = '';
      document.getElementById('modalSubject').value = '';
      currentEmail = email;
      document.getElementById('replyModal').classList.remove('hidden');
    }

    function closeModal() {
      document.getElementById('replyModal').classList.add('hidden');
    }

    function showToast(message, success = true) {
      const toast = document.createElement('div');
      toast.className = `text-white px-4 py-2 rounded shadow-md animate-bounce ${success ? 'bg-green-500' : 'bg-red-500'}`;
      toast.textContent = message;
      document.getElementById('toastContainer').appendChild(toast);
      setTimeout(() => toast.remove(), 3000);
    }

    function sendEmail() {
      const message = document.getElementById('modalMessage').value.trim();
      const subject = document.getElementById('modalSubject').value.trim();

      if (!subject || !message) {
        showToast('Both subject and message are required', false);
        return;
      }

      fetch('backend/send_email.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          to_email: currentEmail,
          subject: subject,
          message: message
        })
      })
        .then(res => res.json())
        .then(data => {
          if (data.status === 'success') {
            showToast('Email sent successfully');
            closeModal();
          } else {
            showToast(data.message || 'Failed to send', false);
          }
        })
        .catch(() => showToast('Server error occurred', false));
    }

    // Fetch messages on page load
    function loadMessages() {
      fetch('backend/fetch_message.php')
        .then(response => response.json())
        .then(messages => {
          const messageList = document.getElementById('messageList');
          messageList.innerHTML = '';  // Clear existing messages
          messages.forEach(message => {
            const messageElement = document.createElement('div');
            messageElement.className = 'bg-white rounded-lg shadow-md p-5';
            messageElement.id = `message_${message.id}`;
            messageElement.innerHTML = `
            <h2 class="text-lg font-semibold text-indigo-700 mb-2">${message.name}</h2>
            <p class="text-sm text-gray-600 mb-2"><i class="bi bi-envelope-fill"></i> ${message.email}</p>
            <p class="text-gray-700 mb-3">${message.message}</p>
            <div class="text-xs text-gray-500 mb-3"><i class="bi bi-clock"></i> ${message.submitted_at}</div>
            <div class="flex justify-between items-center">
              <button onclick="openModal('${message.email}', '${message.name}')" class="bg-indigo-500 text-white px-3 py-1 rounded hover:bg-indigo-600 text-sm">
                <i class="bi bi-reply-fill"></i> Reply
              </button>
              <button onclick="deleteMessage(${message.id})" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-sm">
                <i class="bi bi-trash-fill"></i> Delete
              </button>
            </div>
          `;
            messageList.appendChild(messageElement);
          });
        })
        .catch(() => showToast('Failed to load messages', false));
    }

    // Function to delete a message with AJAX and show toast message
    function deleteMessage(messageId) {
      // Show a confirmation before deleting
      if (confirm('Are you sure you want to delete this message?')) {
        // Prepare the data to send
        let data = new FormData();
        data.append('id', messageId);

        // Perform the AJAX request
        fetch('backend/delete_message.php', {
          method: 'POST',
          body: data
        })
          .then(response => response.json())  // Parse the JSON response
          .then(data => {
            // Check if the response was successful
            if (data.status === 'success') {
              // Show a success toast message
              showToast('Message deleted successfully', 'success');
              // Optionally, remove the message from the UI
              document.getElementById('message_' + messageId).remove();

              // Reload the page after 2 seconds
              setTimeout(function () {
                location.reload();  // This will refresh the page after 2 seconds
              }, 2000); // 2000ms = 2 seconds
            } else {
              // Show an error toast message
              showToast('Failed to delete message. Please try again.', 'error');
            }
          })
          .catch(error => {
            // Show a generic error toast if something goes wrong with the request
            showToast('An error occurred. Please try again.', 'error');
          });
      }
    }


    // Load messages when the page loads
    document.addEventListener('DOMContentLoaded', loadMessages);
  </script>

</body>

</html>
