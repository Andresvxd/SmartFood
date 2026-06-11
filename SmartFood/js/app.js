function handleLogin() {

  const user =
  document.getElementById('loginUser')
  .value.trim();

  const pass =
  document.getElementById('loginPass')
  .value.trim();

  if (!user || !pass) {

    showLoginAlert(
      'Por favor completa todos los campos.'
    );

    return;
  }

  fetch("backend/login.php", {

    method: "POST",

    headers: {
      "Content-Type": "application/json"
    },

    body: JSON.stringify({

      usuario: user,
      password: pass

    })

  })

  .then(response => response.json())

  .then(data => {

    if (data.success) {

      localStorage.setItem(
        "usuario",
        JSON.stringify(data.user)
      );

      showToast(
        '✅ Bienvenido, ' +
        data.user.nombre,
        'success'
      );

      setTimeout(() => {

        window.location.href =
        data.redirect;

      }, 700);

    } else {

      showLoginAlert(
        data.message
      );
    }

  })

  .catch(error => {

    console.error(error);

    showLoginAlert(
      'Error de conexión con el servidor.'
    );
  });
}

function showLoginAlert(msg) {

  const el =
  document.getElementById('loginAlert');

  if (!el) return;

  el.textContent = msg;

  el.classList.remove('d-none');

  setTimeout(() => {

    el.classList.add('d-none');

  }, 4000);
}

// ENTER LOGIN
document.addEventListener(
'DOMContentLoaded', () => {

  const inputs =
  document.querySelectorAll(
  '#loginUser, #loginPass'
  );

  inputs.forEach(inp =>

    inp.addEventListener(
    'keydown', e => {

      if (e.key === 'Enter') {

        handleLogin();
      }
    })
  );
});

// ── PROTEGER PÁGINAS ─────────────────────────
function requireAuth() {

  const user =
  JSON.parse(
    localStorage.getItem("usuario")
  );

  if (!user) {

    window.location.href =
    "index.html";

    return null;
  }

  return user;
}

// ── PROTEGER ADMIN ─────────────────────────
function requireAdmin() {

  const user =
  JSON.parse(
    localStorage.getItem("usuario")
  );

  if (!user) {

    window.location.href =
    "../index.html";

    return;
  }

  if (user.rol !== "admin") {

    window.location.href =
    "../menu.html";
  }
}

// ── LOGOUT ─────────────────────────
function logout() {

  localStorage.removeItem(
    "usuario"
  );

  sessionStorage.clear();

  window.location.replace(
    "index.html"
  );
}

// ── EVITAR ATRÁS ─────────────────────────
window.history.forward();

window.onpageshow = function(event) {

  if (event.persisted) {

    window.location.reload();
  }
};

// ── SIDEBAR ACTIVO ─────────────────────────
function setActiveNav() {

  const page =
  window.location.pathname
  .split('/')
  .pop();

  document
  .querySelectorAll('.sf-nav-link')

  .forEach(link => {

    if (
      link.getAttribute('href')
      === page
    ) {

      link.classList.add(
        'active'
      );
    }
  });
}

// ── USUARIO SIDEBAR ─────────────────────────
function renderSidebarUser() {

  const user =
  JSON.parse(
    localStorage.getItem("usuario")
  );

  if (!user) return;

  const nameEl =
  document.getElementById(
  'sidebarUserName'
  );

  const roleEl =
  document.getElementById(
  'sidebarUserRole'
  );

  const avatarEl =
  document.getElementById(
  'sidebarAvatar'
  );

  if (nameEl)
    nameEl.textContent =
    user.nombre;

  if (roleEl)
    roleEl.textContent =
    user.rol.charAt(0)
    .toUpperCase()
    + user.rol.slice(1);

  if (avatarEl)
    avatarEl.textContent =
    user.nombre.charAt(0)
    .toUpperCase();
}

// ── TOAST ─────────────────────────
function showToast(
  msg,
  type = 'success'
) {

  let t =
  document.getElementById(
  'sfToast'
  );

  if (!t) {

    t =
    document.createElement('div');

    t.id = 'sfToast';

    document.body.appendChild(t);
  }

  t.textContent = msg;

  t.className =
  type === 'error'
  ? 'error'
  : '';

  t.style.display = 'block';

  t.style.animation = 'none';

  void t.offsetWidth;

  t.style.animation =
  'slideUp 0.3s ease';

  clearTimeout(t._timer);

  t._timer =
  setTimeout(() => {

    t.style.display = 'none';

  }, 3500);
}

// ── FORMATO PRECIO ─────────────────────────
function formatPrice(n) {

  return '$ ' +
  Number(n)
  .toLocaleString('es-CO');
}

// ── FECHA HOY ─────────────────────────
function todayStr() {

  return new Date()
  .toISOString()
  .split('T')[0];
}

// ── SIDEBAR MOBILE ─────────────────────────
function toggleSidebar() {

  document
  .querySelector('.sf-sidebar')
  ?.classList.toggle('open');
}