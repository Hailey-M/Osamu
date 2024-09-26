document.addEventListener("DOMContentLoaded", function() {
  const chatBox = document.getElementById("chat-box");
  const userInput = document.getElementById("message");
  const messageForm = document.getElementById("message-form");
  const documentInput = document.getElementById("document");

  messageForm.addEventListener("submit", function(event) {
    event.preventDefault();

    const userMessage = userInput.value.trim();
    const documentFile = documentInput.files[0];

    if (userMessage === "" && !documentFile) return;

    displayMessage(userMessage || "Document uploaded", "user");
    userInput.value = "";

    sendMessage(userMessage, documentFile);
  });

  function sendMessage(message, documentFile) {
    const formData = new FormData();
    formData.append('message', message);
    if (documentFile) {
      formData.append('document', documentFile);
    }

    fetch("/process.php", {
      method: "POST",
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.error) {
        displayMessage(data.error, "bot");
      } else {
        displayMessage(data.response || data, "bot");
      }
    })
    .catch(error => {
      console.error("Error sending message:", error);
      displayMessage("An error occurred while processing the message.", "bot");
    });
  }

  function displayMessage(message, sender) {
    const messageElement = document.createElement("div");
    messageElement.textContent = message;
    messageElement.classList.add("chat-message", sender);
    chatBox.appendChild(messageElement);
    chatBox.scrollTop = chatBox.scrollHeight;
  }
});
