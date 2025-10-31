// scripts.js — simples e funcional
let lastId = 0;
const chatBox = document.getElementById('chat-box');
const msgInput = document.getElementById('msg');
const fileInput = document.getElementById('file');
const form = document.getElementById('form-message');

function escHTML(s) {
  return s.replace(/[&<>]/g, (c) => ({'&':'&amp;','<':'&lt;','>':'&gt;'}[c]));
}

function renderMessage(m) {
  const wrap = document.createElement('div');
  wrap.classList.add('msg', m.username === CURRENT_USER ? 'mine' : 'other');

  const header = document.createElement('div');
  header.classList.add('meta');
  const time = new Date(m.created_at || Date.now()).toLocaleTimeString('pt-BR',{hour:'2-digit',minute:'2-digit'});
  header.textContent = `${m.username} • ${time}`;
  wrap.appendChild(header);

  if (m.message) {
    const text = document.createElement('div');
    text.classList.add('text');
    text.innerHTML = escHTML(m.message).replace(/\n/g, '<br>');
    wrap.appendChild(text);
  }

  if (m.file_path) {
    const file = document.createElement('div');
    file.classList.add('file');
    const a = document.createElement('a');
    a.href = m.file_path;
    a.target = "_blank";
    a.textContent = "📎 Arquivo enviado";
    file.appendChild(a);
    wrap.appendChild(file);
  }

  chatBox.appendChild(wrap);
  chatBox.scrollTop = chatBox.scrollHeight;
}

async function fetchMessages() {
  const res = await fetch('fetch_messages.php?since=' + lastId);
  const data = await res.json();
  if (data.ok) {
    for (const m of data.messages) {
      renderMessage(m);
      lastId = Math.max(lastId, m.id);
    }
  }
}

form.addEventListener('submit', async (e) => {
  e.preventDefault();
  const msg = msgInput.value.trim();
  const file = fileInput.files[0] || null;
  if (!msg && !file) return;

  const formData = new FormData();
  formData.append('message', msg);
  if (file) formData.append('file', file);

  msgInput.value = '';
  fileInput.value = '';

  const res = await fetch('send_message.php', { method: 'POST', body: formData });
  const data = await res.json();
  if (data.ok && data.message) {
    renderMessage(data.message);
    lastId = data.message.id;
  }
});

fetchMessages();
setInterval(fetchMessages, 3000);

