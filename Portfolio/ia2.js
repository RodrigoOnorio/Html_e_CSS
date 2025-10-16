// Exemplo simples para adicionar um efeito de rolagem suave (scroll suave)

document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();

        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});

// Você pode adicionar mais interatividade aqui, como validação de formulário, animações, etc.
