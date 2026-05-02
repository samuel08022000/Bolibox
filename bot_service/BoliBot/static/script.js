function addMessage(text, sender, image=null) {
    const chat = document.getElementById("chat");

    const div = document.createElement("div");
    div.classList.add("msg", sender);

    div.innerText = text;

    if (image) {
        const img = document.createElement("img");
        img.src = image;
        div.appendChild(img);
    }

    chat.appendChild(div);
    chat.scrollTop = chat.scrollHeight;
}

function sendMessage() {
    const input = document.getElementById("message");
    const text = input.value;

    if (!text) return;

    addMessage(text, "user");

    fetch("http://127.0.0.1:5000/chat", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ message: text })
    })
    .then(res => res.json())
    .then(data => {
    removeTyping();
    addMessage(data.response, "bot");
});

    input.value = "";
}

/* ENTER */
document.getElementById("message").addEventListener("keypress", function(e) {
    if (e.key === "Enter") sendMessage();
});

/* MENSAJE INICIAL */
window.onload = () => {
    addMessage("👋 Hola, puedes escribir un producto o pegar un link 🔗", "bot");
}