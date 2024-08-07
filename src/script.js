const loginForm = document.getElementById('loginForm');
const registerForm = document.getElementById('registerForm');
const bookForm = document.getElementById('bookForm');
const logoutButton = document.getElementById('logoutButton');
const loggedInContent = document.getElementById('loggedInContent');
const showLoginButton = document.getElementById('showLoginButton');
const showRegisterButton = document.getElementById('showRegisterButton');
const authButtons = document.getElementById('authButtons');
const addBookButton = document.getElementById('addBookButton');

const loginModal = document.getElementById('loginModal');
const registerModal = document.getElementById('registerModal');
const bookModal = document.getElementById('bookModal');

const closeButtons = document.getElementsByClassName('close');

// Funções para abrir e fechar modais
function openModal(modal) {
    modal.style.display = 'block';
}

function closeModal(modal) {
    modal.style.display = 'none';
}

// Eventos para abrir modais
showLoginButton.onclick = () => openModal(loginModal);
showRegisterButton.onclick = () => openModal(registerModal);
addBookButton.onclick = () => {
    document.getElementById('bookModalTitle').innerHTML = '<i class="fas fa-plus-circle icon"></i> Adicionar Livro';
    bookForm.reset();
    openModal(bookModal);
};

// Eventos para fechar modais
for (let closeButton of closeButtons) {
    closeButton.onclick = function() {
        closeModal(this.closest('.modal'));
    }
}

window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        closeModal(event.target);
    }
}

async function showLoggedInContent() {
    loggedInContent.classList.remove('hidden');
    authButtons.classList.add('hidden');
    //esconde o botao de login e novo cadastro e não deixe abrir os modais
    showLoginButton.style.display = 'none';
    showRegisterButton.style.display = 'none';
    await loadBooks();
}

function showLoggedOutContent() {
    loggedInContent.classList.add('hidden');
    authButtons.classList.remove('hidden');
    showLoginButton.style.display = 'block';
    showRegisterButton.style.display = 'block';
}



async function checkAuth() {
    try {
        const response = await fetch('backend.php?action=checkAuth');
        const data = await response.json();
        if (data.authenticated) {
            await showLoggedInContent();
        } else {
            showLoggedOutContent();
        }
    } catch (error) {
        console.error('Erro ao verificar autenticação:', error);
    }
}

async function loadBooks() {
    try {
        const response = await fetch('backend.php?action=getBooks');
        const books = await response.json();
        const bookListBody = document.getElementById('bookListBody');
        bookListBody.innerHTML = '';
        books.forEach(book => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${book.id}</td>
                <td>${book.title}</td>
                <td>${book.author}</td>
                <td>
                    <div class="btn-group">
                        <button onclick="editBook(${book.id}, '${book.title}', '${book.author}')" class="btn-edit">
                            <i class="fas fa-edit icon"></i> Editar
                        </button>
                        <button onclick="deleteBook(${book.id})" class="btn-delete">
                            <i class="fas fa-trash icon"></i> Excluir
                        </button>
                    </div>
                </td>
            `;
            bookListBody.appendChild(row);
        });
    } catch (error) {
        console.error('Erro ao carregar livros:', error);
    }
}

loginForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(loginForm);
    formData.append('action', 'login');
    try {
        const response = await fetch('backend.php', { method: 'POST', body: formData });
        const data = await response.json();
        alert(data.message);
        if (data.success) {
            closeModal(loginModal);
            await showLoggedInContent();
        }
    } catch (error) {
        console.error('Erro ao fazer login:', error);
    }
});

registerForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(registerForm);
    formData.append('action', 'register');
    try {
        const response = await fetch('backend.php', { method: 'POST', body: formData });
        const data = await response.json();
        alert(data.message);
        if (data.success) {
            closeModal(registerModal);
            loginForm.username.value = registerForm.username.value;
            loginForm.password.value = registerForm.password.value;
            openModal(loginModal);
        }
    } catch (error) {
        alert('Usuario ja cadastrado', error);
    }
});

bookForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(bookForm);
    formData.append('action', 'addEditBook');
    try {
        const response = await fetch('backend.php', { method: 'POST', body: formData });
        const data = await response.json();
        alert(data.message);
        if (data.success) {
            closeModal(bookModal);
            bookForm.reset();
            await loadBooks();
        }
    } catch (error) {
        console.error('Erro ao salvar livro:', error);
    }
});

logoutButton.addEventListener('click', async function() {
    try {
        const response = await fetch('backend.php', { 
            method: 'POST', 
            body: new URLSearchParams({ 'action': 'logout' })
        });
        const data = await response.json();
        alert(data.message);
        showLoggedOutContent();
    } catch (error) {
        console.error('Erro ao fazer logout:', error);
    }
});

function editBook(id, title, author) {
    document.getElementById('bookModalTitle').innerHTML = '<i class="fas fa-edit icon"></i> Editar Livro';
    bookForm.id.value = id;
    bookForm.title.value = title;
    bookForm.author.value = author;
    openModal(bookModal);
}

async function deleteBook(id) {
    if (confirm('Tem certeza que deseja excluir este livro?')) {
        try {
            const response = await fetch('backend.php', { 
                method: 'POST', 
                body: new URLSearchParams({ 'action': 'deleteBook', 'id': id })
            });
            const data = await response.json();
            alert(data.message);
            if (data.success) {
                await loadBooks();
            }
        } catch (error) {
            console.error('Erro ao excluir livro:', error);
        }
    }
}

// Verificar autenticação ao carregar a página
checkAuth();