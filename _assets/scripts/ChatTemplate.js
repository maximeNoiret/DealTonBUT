const chatArea = document.getElementById('chat-messages');
const idConv = document.querySelector('input[name="id_conv"]').value;

function refreshChat() {
    fetch(`/chat/updates?id_conv=${idConv}`)
        .then(response => response.json())
        .then(data => {
            if (data.html && chatArea.innerHTML !== data.html) {
                chatArea.innerHTML = data.html;
                chatArea.scrollTop = chatArea.scrollHeight;
            }
        })
        .catch(err => console.error("Erreur de mise à jour :", err));
}
setInterval(refreshChat, 2000);