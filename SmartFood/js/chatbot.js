const Chatbot = {
  open: false,
  recomendandoMode: false,
  recomendandoPerfil: null,

  greetings: [
    '¡Hola! Soy el asistente de SmartFood 🍽️ Puedo ayudarte a:\n• Ver el menú\n• Agregar productos al carrito\n• Recomendarte platos\n• Consultar precios\n• Aplicar descuentos\n\n¿Qué deseas hacer?'
  ],

  init() {
    Cart.load();
    this.addMsg('bot', this.greetings[0]);
  },

  addMsg(from, text) {
    const msgs = document.getElementById('chatMessages');
    if (!msgs) return;
    const div = document.createElement('div');
    div.className = `chat-msg ${from}`;
    div.innerHTML = text.replace(/\n/g, '<br>').replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
    msgs.appendChild(div);
    msgs.scrollTop = msgs.scrollHeight;
  },

  getEmoji(categoria) {
    const map = {
      'Pizzas':      '🍕',
      'Perros':      '🌭',
      'Salchipapas': '🍟',
      'Bebidas':     '🥤',
      'Hamburguesas':'🍔',
      'Postres':     '🍰',
    };
    return map[categoria] || '🍽️';
  },

  process(input) {
    const txt = input.toLowerCase().trim();
    const menu = typeof allProducts !== 'undefined' ? allProducts : [];

    // ── AGREGAR AL CARRITO ──
    if (/(agrega|agregar|añade|añadir|pon)\b/.test(txt) && !/(recomiéndame|recomienda|sugiere|sugieres)/.test(txt)) {
      const numMatch = txt.match(/\b(\d+|un|una|dos|tres|cuatro|cinco)\b/);
      const numMap = { un:1, una:1, dos:2, tres:3, cuatro:4, cinco:5 };
      let qty = 1;
      if (numMatch) qty = isNaN(numMatch[1]) ? (numMap[numMatch[1]] || 1) : parseInt(numMatch[1]);

      const found = menu.find(p =>
        txt.includes(p.nombre.toLowerCase()) ||
        p.nombre.toLowerCase().split(' ').some(w => w.length > 3 && txt.includes(w))
      );

      if (found) {
        for (let i = 0; i < qty; i++) Cart.add(found);
        Cart.render('cartItemsList');
        if (typeof Cart.updateBadge === 'function') Cart.updateBadge();
        return `✅ Listo! Agregué **${qty}x ${found.nombre}** al carrito.\n💰 Subtotal: ${formatPrice(found.precio * qty)}\n\n¿Deseas algo más?`;
      }
      // No mencionó producto específico — mostrar menú para que elija
      const lista = menu.filter(p => p.disponible)
        .map(p => `  ${Chatbot.getEmoji(p.categoria)} agrega ${p.nombre}`)
        .join('\n');
      return `¿Qué producto deseas agregar? 🍽️\n\nEscribe por ejemplo:\n${lista}`;
    }

    // ── VER MENÚ ──
    if (/(menú|menu|productos|que hay|qué hay|carta|platos)/.test(txt)) {
      const cats = [...new Set(menu.map(p => p.categoria))];
      let res = '📋 **Nuestro Menú:**\n\n';
      cats.forEach(cat => {
        res += `**${cat}:**\n`;
        menu.filter(p => p.categoria === cat && p.disponible).forEach(p => {
          res += `  ${this.getEmoji(p.categoria)} ${p.nombre} – ${formatPrice(p.precio)}\n`;
        });
        res += '\n';
      });
      res += '💬 Puedes decirme: *"Agrega 2 pizzas"*';
      return res;
    }

    // ── VACIAR CARRITO (va antes de ver carrito) ──
    if (/(vaciar|limpiar|borrar|eliminar).*(carrito|pedido)/.test(txt)) {
      Cart.clear();
      Cart.render('cartItemsList');
      if (typeof Cart.updateBadge === 'function') Cart.updateBadge();
      return '🗑️ Carrito vaciado. ¡Empecemos de nuevo!';
    }

    // ── VER CARRITO ──
    if (/(carrito|mi pedido|que tengo|ver pedido)/.test(txt)) {
      if (Cart.items.length === 0) return '🛒 Tu carrito está vacío.\n¿Qué te gustaría pedir?';
      let res = '🛒 **Tu carrito:**\n\n';
      Cart.items.forEach(i => {
        res += `${this.getEmoji(i.categoria)} ${i.cantidad}x ${i.nombre} – ${formatPrice(i.precio * i.cantidad)}\n`;
      });
      res += `\n💰 **Total: ${formatPrice(Cart.total())}**\n\n¿Confirmas el pedido? Di *"pagar"* para ir al pago.`;
      return res;
    }

    // ── PAGAR ──
    if (/(pagar|pago|proceder|checkout|finalizar|confirmar)/.test(txt)) {
      if (Cart.items.length === 0) return '🛒 Tu carrito está vacío. Primero agrega algunos productos.';
      setTimeout(() => { window.location.href = 'pago.html'; }, 1000);
      return '💳 ¡Perfecto! Te llevo al módulo de pago...';
    }

    // ── PRECIO ──
    if (/(precio|cuánto|cuanto|vale|cuesta)/.test(txt)) {
      const found = menu.find(p =>
        txt.includes(p.nombre.toLowerCase()) ||
        p.nombre.toLowerCase().split(' ').some(w => w.length > 3 && txt.includes(w))
      );
      if (found) return `💰 **${found.nombre}** cuesta ${formatPrice(found.precio)}.\n¿La agrego al carrito?`;
      return `Dime el nombre del producto. Ej: *"precio pizza hawaiana"*`;
    }

    // ── RECOMENDACIONES ──
    if (/(recomiéndame|recomienda|sugiere|sugieres|qué (me )?(recomiendas|pido)|no sé qué pedir|qué hay bueno)/.test(txt)) {
      // Activar modo recomendación con IA — marcar estado y hacer pregunta
      Chatbot.recomendandoMode = true;
      return '¡Claro! 😊 Para recomendarte mejor...\n\n¿Cómo te sientes ahora mismo?\n\n🍕 Tengo mucha hambre\n🥤 Quiero algo ligero\n🍰 Se me antoja algo dulce\n🌶️ Quiero algo contundente\n🤷 Sorpréndeme';
    }

    // ── RESPUESTA A MODO RECOMENDACIÓN ──
    if (Chatbot.recomendandoMode) {
      Chatbot.recomendandoMode = false;
      Chatbot.recomendandoPerfil = txt;
      return null; // pasar a IA con contexto de recomendación
    }

    // ── DESCUENTOS ──
    if (/(descuento|promo|oferta|código|cupon)/.test(txt)) {
      return '🏷️ **Códigos de descuento disponibles:**\n\n• **SMART10** → 10% descuento\n• **NUEVO20** → 20% para nuevos clientes\n• **COMBO15** → 15% en combos\n\nAplícalos en el módulo de pago.';
    }

    // ── HORARIO ──
    if (/(horario|abierto|cerrado|hora)/.test(txt)) {
      return '🕐 **Horario SmartFood:**\n\nLunes a Viernes: 11:00 AM – 10:00 PM\nSábados: 10:00 AM – 11:00 PM\nDomingos: 12:00 PM – 9:00 PM\n\n¡Siempre listos para atenderte!';
    }

    // ── AYUDA ──
    if (/(ayuda|help|comandos|opciones)/.test(txt)) {
      return '🆘 **¿En qué puedo ayudarte?**\n\n• *"ver menú"* – Lista de productos\n• *"agrega 2 pizzas"* – Agregar al carrito\n• *"ver carrito"* – Ver tu pedido\n• *"recomiéndame algo"* – Sugerencias\n• *"precio salchipapa"* – Consultar precio\n• *"descuentos"* – Ver promociones\n• *"pagar"* – Ir al pago\n• *"vaciar carrito"* – Limpiar pedido';
    }

    // ── SALUDOS ──
    if (/(hola|buenas|hey|saludos|buenos días|buenas tardes|buenas noches)/.test(txt)) {
      return '¡Hola! 👋 Bienvenido a SmartFood.\n¿En qué te puedo ayudar hoy?\n\nDi *"ver menú"* o *"recomiéndame algo"* para empezar.';
    }

    // ── FALLBACK: IA REAL ──
    return null; // señal para usar IA
  }
};

// ── ENVIAR MENSAJE ──
async function sendChat() {
  const inp = document.getElementById('chatInput');
  if (!inp) return;
  const text = inp.value.trim();
  if (!text) return;
  inp.value = '';
  Chatbot.addMsg('user', text);

  // Indicador de escritura
  const msgs = document.getElementById('chatMessages');
  const typing = document.createElement('div');
  typing.className = 'chat-msg bot';
  typing.innerHTML = '<i>Escribiendo...</i>';
  typing.id = 'typingIndicator';
  msgs.appendChild(typing);
  msgs.scrollTop = msgs.scrollHeight;

  // Intentar respuesta local primero
  const localResponse = Chatbot.process(text);

  if (localResponse !== null) {
    setTimeout(() => {
      document.getElementById('typingIndicator')?.remove();
      Chatbot.addMsg('bot', localResponse);
    }, 600);
  } else {
    // Usar IA real para preguntas libres
    await responderConIA(text);
  }
}

// ── IA REAL (Claude API) ──
async function responderConIA(userMessage) {
  const menu = typeof allProducts !== 'undefined' ? allProducts : [];

  const menuResumen = menu.map(p =>
    `- ${p.nombre} (${p.categoria}): ${p.descripcion} — $${p.precio}`
  ).join('\n');

  const carritoResumen = Cart.items.length
    ? Cart.items.map(i => `${i.cantidad}x ${i.nombre}`).join(', ')
    : 'vacío';

  // Si viene del modo recomendación, usar prompt especial
  const esRecomendacion = !!Chatbot.recomendandoPerfil;
  const perfil = Chatbot.recomendandoPerfil || '';
  Chatbot.recomendandoPerfil = null;

  const systemPrompt = esRecomendacion
    ? `Eres el asistente de SmartFood, un restaurante colombiano moderno.
El cliente describió cómo se siente o qué antojo tiene: "${perfil}".
Basándote SOLO en el menú disponible, recomiéndale 1 o 2 productos de forma entusiasta y personal.
Explica brevemente por qué ese producto encaja con su estado de ánimo o antojo.
Al final dile exactamente qué escribir para agregarlo, por ejemplo: escribe "agrega Pizza Hawaiana".
Usa emojis. Máximo 6 líneas. Responde en español.

Menú disponible:
${menuResumen}`
    : `Eres el asistente virtual de SmartFood, un restaurante colombiano moderno.
Responde siempre en español, de forma amable, breve y con emojis.
Ayudas a los clientes con el menú, recomendaciones y dudas sobre el restaurante.
No puedes agregar productos al carrito directamente, pero dile al usuario qué escribir para hacerlo.

Menú disponible:
${menuResumen}

Carrito actual del cliente: ${carritoResumen}

Si el usuario pregunta algo que no tiene que ver con el restaurante, redirige amablemente la conversación al menú.
Respuestas cortas (máximo 4 líneas).`;

  try {
    const response = await fetch('https://api.anthropic.com/v1/messages', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        model: 'claude-sonnet-4-20250514',
        max_tokens: 1000,
        system: systemPrompt,
        messages: [{ role: 'user', content: userMessage }]
      })
    });

    const data = await response.json();
    document.getElementById('typingIndicator')?.remove();

    const reply = data?.content?.[0]?.text || '😕 No pude responder. Intenta con *"ayuda"* para ver los comandos.';
    Chatbot.addMsg('bot', reply);

  } catch (e) {
    document.getElementById('typingIndicator')?.remove();
    Chatbot.addMsg('bot', '😕 Error de conexión con la IA. Prueba con *"ayuda"* para ver los comandos disponibles.');
  }
}

// ── TOGGLE ──
function toggleChatbot() {
  const win = document.getElementById('chatbotWindow');
  if (!win) return;
  Chatbot.open = !Chatbot.open;
  if (Chatbot.open) {
    win.classList.add('open');
    if (!document.getElementById('chatMessages').hasChildNodes()) Chatbot.init();
    document.getElementById('chatInput')?.focus();
  } else {
    win.classList.remove('open');
  }
}