async function getUserChats(userId) {
    const url = `http://localhost/api/users/${userId}/chats/`;

    const response = await fetch(url);

    if (response.ok) {
        return await response.json();
    }

    return [];
}

function createChatCard(chatInfo) {
    const card = document.createElement('a');
    card.classList.add('link-chat', 'pb-3');
    card.href = `/FrontEnd/Messages.html?chatId=${chatInfo.id}`;

    const html = `
        <div class="col-12 link-chat-hover">
            <img class="messageAvatar-img" src="https://avatars.dicebear.com/api/jdenticon/${chatInfo.id}.svg" alt="${chatInfo.subject}">
            <h4 style="display: inline;">${chatInfo.subject}</h4><br>
        </div>
        <br>
    `;

    card.innerHTML = html;

    return card;
}

async function getCurrentSession() {
    const url = 'http://localhost/api/session/';

    const response = await fetch(url);

    if (response.ok) {
        return await response.json();
    }

    return null;
}

async function getChatMessages(chatId) {
    const url = `http://localhost/api/chats/${chatId}/messages/`;

    const response = await fetch(url);

    if (response.ok) {
        return await response.json();
    }

    return [];
}

function createMessageCard(message, userId) {
    const card = document.createElement('div');
    card.classList.add('row', 'mt-3', 'mb-3', 'd-flex');

    if (message.senderId == userId) {
        card.classList.add('flex-row-reverse');
    }
    else {
        card.classList.add('flex-row');
    }
    
    const html = `
        <div class="card w-25">
            <div class="card-header">
                <span>${message.senderName}</span> <br>
                <span class="text-muted">${message.date}</span>
            </div>
            <div class="card-body">
                <p class="card-text">
                    ${message.body}
                </p>
            </div>
        </div>
    `;

    card.innerHTML = html;

    return card;
}

async function sendMessage(message, chatId) {
    const url = `http://localhost/api/chats/${chatId}/messages/`;

    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(message)
    });

    return response.ok;
}

document.getElementById('messageForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;

    const chatId = form.dataset.chatId;

    if (!chatId) {
        return;
    }

    const session = await getCurrentSession();

    if (!session) {
        return;
    }

    const messageInput = document.getElementById('input-chatMessage');
    const messageText = messageInput.value.trim();

    if (messageText === '') {
        return;
    }

    const message = {
        senderId: session.id,
        body: messageText
    };

    const success = await sendMessage(message, chatId);

    if (!success) {
        return;
    }

    const messageContainer = document.getElementById('messages-container');
    
    message['senderName'] = `${session.name} ${session.lastname}`;
    message['date'] = '';

    const messageCard = createMessageCard(message, session.id);
    messageContainer.appendChild(messageCard);

    messageCard.scrollIntoView({behavior: "smooth"});

    messageInput.value = '';
});

document.addEventListener('DOMContentLoaded', async () => {
    const session = await getCurrentSession();

    if (!session) {
        return;
    }

    // Load chats
    const chatContainer = document.getElementById('chatsContainer');
    const chats = await getUserChats(session.id);

    for (const chat of chats) {
        const chatCard = createChatCard(chat);

        chatContainer.append(chatCard);
    }

    // Load chat messages
    const params = new URLSearchParams(window.location.search);

    const chatId = params.get('chatId');

    if (!chatId) {
        return;
    }

    document.getElementById('messageForm').dataset.chatId = chatId;

    // Remove greeting message
    document.getElementById('greeting').remove();

    const messageContainer = document.getElementById('messages-container');
    const messages = await getChatMessages(chatId);

    for (let i = 0; i < messages.length; i++) {
        const message = messages[i];
        const messageCard = createMessageCard(message, session.id);
        messageContainer.appendChild(messageCard);

        // if it is the last message
        if (i == messages.length - 1) {
            messageCard.scrollIntoView({behavior: "smooth"});
        }
    }
});