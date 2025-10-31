<?php
require __DIR__ . '/db.php';
require __DIR__ . '/utils.php';
// Sessão já iniciada em utils

// Definição de nome do usuário (sessão simples)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['set_nome'])) {
    $nome = sanitize_text($_POST['nome'] ?? '');
    if ($nome !== '' && mb_strlen($nome) <= 100) {
        $_SESSION['nome'] = $nome;
        header('Location: index.php');
        exit;
    }
}

$nomeSessao = $_SESSION['nome'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Chat Complexo</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Mukta+Vaani:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="estilos.css" />
  <style>
    :root { --primary: #6366f1; --secondary: #22d3ee; }
    body { font-family: 'Mukta Vaani', system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, Noto Sans, Helvetica, Arial; }
  </style>
</head>
<body>
  <header class="site-header">
    <div class="container header-inner">
      <div class="logo">Chat em Tempo Real</div>
      <nav>
        <ul class="nav-list">
          <li><a href="#chatbox">Mensagens</a></li>
          <li><a href="#enviar">Enviar</a></li>
          <li><button id="toggleTheme" class="btn outline sm">Tema</button></li>
        </ul>
      </nav>
    </div>
  </header>

  <section class="hero">
    <div class="container hero-inner">
      <div class="hero-text">
        <h1>Bem-vindo ao <span class="mark">Chat Complexo</span></h1>
        <p class="subtitle">Atualização em tempo quase real (SSE com fallback), CSRF, rate limiting, paginação e mais.</p>
        <div class="actions">
          <a class="btn primary" href="#chatbox">Ver mensagens</a>
          <a class="btn outline" href="#enviar">Enviar mensagem</a>
        </div>
      </div>
      <div class="hero-photo">
        <img src="https://source.unsplash.com/collection/190727/300x300" alt="Ilustração" />
      </div>
    </div>
  </section>

  <main class="container">
    <section id="perfil" class="section">
      <h2>Perfil</h2>
      <?php if ($nomeSessao === ''): ?>
        <form method="POST" class="about-card" style="max-width:520px">
          <label>Seu nome</label>
          <input type="text" name="nome" placeholder="Digite seu nome" required />
          <input type="hidden" name="set_nome" value="1" />
          <button class="btn primary" type="submit">Salvar</button>
          <p class="subtitle">Seu nome será usado ao enviar mensagens.</p>
        </form>
      <?php else: ?>
        <div class="about-card" style="max-width:520px">
          <h3>Olá, <?php echo esc_html($nomeSessao); ?> 👋</h3>
          <p class="subtitle">Pronto para conversar! Você pode alterar seu nome limpando o cache de sessão.</p>
        </div>
      <?php endif; ?>
    </section>

    <section id="chatbox" class="section">
      <h2>Mensagens</h2>
      <div id="caixa-chat" class="cards">
        <div id="chat" class="card">
          <div class="card-body">
            <p class="subtitle">Carregando mensagens...</p>
          </div>
        </div>
      </div>
      <div class="actions" style="margin-top:16px">
        <button id="btnMais" class="btn outline sm">Carregar mais</button>
        <span id="status" class="subtitle"></span>
      </div>
    </section>

    <section id="enviar" class="section">
      <h2>Enviar mensagem</h2>
      <form id="formEnviar" class="contact-form" autocomplete="off">
        <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>" />
        <div class="grid two">
          <label>
            Nome
            <input type="text" name="nome" placeholder="Seu nome" value="<?php echo esc_html($nomeSessao); ?>" required />
          </label>
          <label>
            Mensagem
            <input type="text" name="mensagem" placeholder="Sua mensagem" required />
          </label>
        </div>
        <div class="actions">
          <button class="btn primary" type="submit">Enviar</button>
          <button class="btn outline" type="reset">Limpar</button>
        </div>
        <p id="enviarStatus" class="subtitle"></p>
      </form>
    </section>
  </main>

  <footer class="site-footer">
    <div class="container footer-inner">
      <p>Chat Complexo • SSE • CSRF • Rate limit • Segurança</p>
      <ul class="social">
        <li><a href="#">GitHub</a></li>
        <li><a href="#">Docs</a></li>
      </ul>
    </div>
  </footer>

  <script>
    // Tema claro/escuro
    (function(){
      const html = document.documentElement;
      const saved = localStorage.getItem('theme');
      if (saved) html.setAttribute('data-theme', saved);
      document.getElementById('toggleTheme').addEventListener('click', () => {
        const current = html.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
        html.setAttribute('data-theme', current);
        localStorage.setItem('theme', current);
      });
    })();

    const chatEl = document.getElementById('chat');
    const statusEl = document.getElementById('status');
    const btnMais = document.getElementById('btnMais');
    let lastId = 0;
    let hasSSE = false;

    function renderMessages(messages, append = true) {
      if (append) {
        if (!chatEl.querySelector('.card-body')) {
          chatEl.innerHTML = '<div class="card-body"></div>';
        }
        const body = chatEl.querySelector('.card-body');
        messages.forEach(m => {
          const item = document.createElement('div');
          item.className = 'item';
          item.innerHTML = `<div class="item-head"><strong>${escapeHtml(m.nome)}</strong><span class="periodo">${m.data || ''}</span></div><p>${escapeHtml(m.mensagem)}</p>`;
          body.prepend(item); // ordem do mais recente em cima
        });
      } else {
        chatEl.innerHTML = '<div class="card-body"></div>';
        renderMessages(messages, true);
      }
      // Atualiza lastId
      messages.forEach(m => { lastId = Math.max(lastId, Number(m.id || 0)); });
    }

    function escapeHtml(text) {
      const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
      return String(text).replace(/[&<>"']/g, s => map[s]);
    }

    async function fetchMessages(since = 0) {
      const url = since > 0 ? `chat.php?sinceId=${since}` : `chat.php?limit=50`;
      const res = await fetch(url);
      const data = await res.json();
      const msgs = Array.isArray(data.messages) ? data.messages : [];
      if (since > 0) {
        renderMessages(msgs, true);
      } else {
        renderMessages(msgs.reverse(), false); // inicial em ordem antiga
      }
    }

    function initSSE() {
      try {
        const es = new EventSource(`sse.php?lastId=${lastId}`);
        es.addEventListener('message', (ev) => {
          hasSSE = true;
          const m = JSON.parse(ev.data);
          renderMessages([m], true);
          statusEl.textContent = 'Conectado (SSE)';
        });
        es.addEventListener('ping', () => { statusEl.textContent = 'Ativo (SSE)'; });
        es.onerror = () => { statusEl.textContent = 'SSE indisponível, usando fallback'; hasSSE = false; };
      } catch (e) {
        hasSSE = false;
      }
    }

    // Fallback polling
    setInterval(() => { if (!hasSSE && lastId > 0) fetchMessages(lastId); }, 1500);

    // Carregar inicial
    fetchMessages(0).then(() => initSSE());

    btnMais.addEventListener('click', () => fetchMessages(0));

    // Envio de mensagem via fetch
    const form = document.getElementById('formEnviar');
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const fd = new FormData(form);
      const res = await fetch('post_message.php', { method: 'POST', body: fd });
      const json = await res.json().catch(() => ({}));
      if (json.success) {
        document.getElementById('enviarStatus').textContent = 'Mensagem enviada!';
        form.mensagem.value = '';
        fetchMessages(lastId);
        // beep opcional
        try { new Audio('beep.mp3').play(); } catch {}
      } else {
        document.getElementById('enviarStatus').textContent = json.error || 'Erro ao enviar.';
      }
    });
  </script>
</body>
</html>