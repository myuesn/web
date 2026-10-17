'use strict';

(() => {
  const products = window.WLF_PRODUCTS || [];
  const $ = (selector, root = document) => root.querySelector(selector);
  const $$ = (selector, root = document) => [...root.querySelectorAll(selector)];
  const escape = value => String(value).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  const read = (key, fallback) => { try { return JSON.parse(localStorage.getItem(key)) ?? fallback; } catch { return fallback; } };
  const save = (key, value) => { try { localStorage.setItem(key, JSON.stringify(value)); return true; } catch { return false; } };
  const params = new URLSearchParams(location.search);
  const page = document.body.dataset.page;
  let language = ['en','es','ru'].includes(params.get('lang')) ? params.get('lang') : read('wlf-language', 'en');
  if (!['en','es','ru'].includes(language)) language = 'en';
  save('wlf-language', language);
  const cleanQuote = value => Array.isArray(value) ? value.filter((item, index, all) => item && products.some(p => p.id === item.id) && Number.isInteger(item.quantity) && item.quantity > 0 && item.quantity <= 1000000 && all.findIndex(i => i?.id === item.id) === index) : [];
  let quote = cleanQuote(read('wlf-enquiry-v2', []));
  const messages = {
  "Tractor parts": {
    "es": "Piezas de tractor",
    "ru": "Запчасти для тракторов"
  },
  "Harvester parts": {
    "es": "Piezas de cosechadora",
    "ru": "Запчасти для комбайнов"
  },
  "Soil working parts": {
    "es": "Piezas para labranza",
    "ru": "Запчасти для почвообработки"
  },
  "Hydraulics": {
    "es": "Sistema hidráulico",
    "ru": "Гидравлика"
  },
  "Transmission & drive": {
    "es": "Transmisión y accionamiento",
    "ru": "Трансмиссия и привод"
  },
  "Engine & fuel": {
    "es": "Motor y combustible",
    "ru": "Двигатель и топливная система"
  },
  "Axle & bearings": {
    "es": "Ejes y rodamientos",
    "ru": "Оси и подшипники"
  },
  "Cutting & harvesting": {
    "es": "Corte y cosecha",
    "ru": "Режущие и уборочные узлы"
  },
  "Soil working": {
    "es": "Labranza",
    "ru": "Почвообработка"
  },
  "Confirm availability": {
    "es": "Consultar disponibilidad",
    "ru": "Уточнить наличие"
  },
  "Special sourcing enquiry": {
    "es": "Consulta de suministro especial",
    "ru": "Запрос специальной поставки"
  },
  "Category": {
    "es": "Categoría",
    "ru": "Категория"
  },
  "Machine brand reference": {
    "es": "Marca de la máquina",
    "ru": "Марка машины"
  },
  "Component group": {
    "es": "Grupo de componentes",
    "ru": "Группа узлов"
  },
  "Sourcing enquiry": {
    "es": "Consulta de suministro",
    "ru": "Запрос поставки"
  },
  "Agricultural Machinery Parts": {
    "es": "Repuestos para maquinaria agrícola",
    "ru": "Запчасти для сельхозтехники"
  },
  "Parts Catalogue": {
    "es": "Catálogo de repuestos",
    "ru": "Каталог запчастей"
  },
  "About Us": {
    "es": "Quiénes somos",
    "ru": "О компании"
  },
  "Request a Quote": {
    "es": "Solicitar presupuesto",
    "ru": "Запросить предложение"
  },
  "Contact": {
    "es": "Contacto",
    "ru": "Контакты"
  },
  "Select language": {
    "es": "Seleccionar idioma",
    "ru": "Выбрать язык"
  },
  "Add parts from the catalogue, or describe a custom requirement below.": {
    "es": "Añada piezas del catálogo o describa su necesidad a continuación.",
    "ru": "Добавьте запчасти из каталога или опишите свой запрос ниже."
  },
  "Add to enquiry": {
    "es": "Añadir a la consulta",
    "ru": "Добавить в запрос"
  },
  "Add to enquiry: ": {
    "es": "Añadir a la consulta: ",
    "ru": "Добавить в запрос: "
  },
  "Added for this page. Browser storage is unavailable.": {
    "es": "Añadido para esta página. El almacenamiento del navegador no está disponible.",
    "ru": "Добавлено для этой страницы. Хранилище браузера недоступно."
  },
  "An email draft is being opened. Review and send it in your email app. If it does not open, download your enquiry and email it to sales@wlfagriparts.com.": {
    "es": "Se está abriendo un borrador de correo. Revíselo y envíelo desde su aplicación de correo. Si no se abre, descargue la consulta y envíela a sales@wlfagriparts.com.",
    "ru": "Открывается черновик письма. Проверьте и отправьте его в почтовой программе. Если он не открылся, скачайте запрос и отправьте на sales@wlfagriparts.com."
  },
  "Close product details": {
    "es": "Cerrar detalles del producto",
    "ru": "Закрыть сведения о товаре"
  },
  "Example model references": {
    "es": "Referencias de modelos de ejemplo",
    "ru": "Примеры моделей"
  },
  "Explore the catalogue": {
    "es": "Explorar el catálogo",
    "ru": "Открыть каталог"
  },
  "Illustrative specifications": {
    "es": "Especificaciones de ejemplo",
    "ru": "Пример технических характеристик"
  },
  "In your enquiry": {
    "es": "En su consulta",
    "ru": "В вашем запросе"
  },
  "In your enquiry: ": {
    "es": "En su consulta: ",
    "ru": "В вашем запросе: "
  },
  "Main navigation": {
    "es": "Navegación principal",
    "ru": "Главная навигация"
  },
  "Please enter this information.": {
    "es": "Introduzca esta información.",
    "ru": "Укажите эти данные."
  },
  "Quantity": {
    "es": "Cantidad",
    "ru": "Количество"
  },
  "Quantity for ": {
    "es": "Cantidad de ",
    "ru": "Количество: "
  },
  "Remove ": {
    "es": "Eliminar ",
    "ru": "Удалить: "
  },
  "Remove filter": {
    "es": "Quitar filtro",
    "ru": "Убрать фильтр"
  },
  "Remove search": {
    "es": "Borrar búsqueda",
    "ru": "Очистить поиск"
  },
  "SAMPLE PRODUCT · References, specifications, images and model associations are illustrative. They are not a guarantee of stock, origin or fit. Confirm the exact reference and compatibility before ordering.": {
    "es": "PRODUCTO DE EJEMPLO · Las referencias, especificaciones, imágenes y asociaciones de modelos son ilustrativas. No garantizan existencias, origen ni compatibilidad. Confirme la referencia exacta y la compatibilidad antes de realizar un pedido.",
    "ru": "ПРИМЕР ТОВАРА · Артикулы, характеристики, изображения и привязки к моделям приведены для примера. Они не гарантируют наличие, происхождение или совместимость. Перед заказом уточните точный артикул и совместимость."
  },
  "This enquiry is too long for a reliable email link, so it was downloaded instead. Attach the file to an email to sales@wlfagriparts.com.": {
    "es": "La consulta es demasiado extensa para un enlace de correo fiable y se ha descargado como archivo. Adjúntelo a un correo dirigido a sales@wlfagriparts.com.",
    "ru": "Запрос слишком длинный для почтовой ссылки, поэтому он скачан в файл. Прикрепите его к письму на sales@wlfagriparts.com."
  },
  "This part is already in your enquiry.": {
    "es": "Esta pieza ya está en su consulta.",
    "ru": "Эта запчасть уже есть в вашем запросе."
  },
  "Toggle menu": {
    "es": "Abrir o cerrar menú",
    "ru": "Открыть или закрыть меню"
  },
  "View ": {
    "es": "Ver ",
    "ru": "Посмотреть: "
  },
  "View details": {
    "es": "Ver detalles",
    "ru": "Подробнее"
  },
  "View enquiry": {
    "es": "Ver consulta",
    "ru": "Открыть запрос"
  },
  "Your enquiry file is ready. Attach it to an email to sales@wlfagriparts.com. The file has not been sent automatically.": {
    "es": "El archivo de consulta está listo. Adjúntelo a un correo dirigido a sales@wlfagriparts.com. No se ha enviado automáticamente.",
    "ru": "Файл запроса готов. Прикрепите его к письму на sales@wlfagriparts.com. Файл не отправлен автоматически."
  },
  "Your next parts list starts here.": {
    "es": "Su próxima lista de repuestos empieza aquí.",
    "ru": "Здесь начинается ваш новый список запчастей."
  },
  "{part} added to your enquiry.": {
    "es": "{part}: añadido a su consulta.",
    "ru": "{part}: добавлено в запрос."
  },
  "Parts found: {count}": {
    "es": "Piezas encontradas: {count}",
    "ru": "Найдено деталей: {count}"
  },
  "WLF AGRI PARTS — QUOTATION ENQUIRY": {
    "es": "WLF AGRI PARTS — SOLICITUD DE PRESUPUESTO",
    "ru": "WLF AGRI PARTS — ЗАПРОС ПРЕДЛОЖЕНИЯ"
  },
  "Company": {
    "es": "Empresa",
    "ru": "Компания"
  },
  "Email": {
    "es": "Correo electrónico",
    "ru": "Электронная почта"
  },
  "Phone": {
    "es": "Teléfono",
    "ru": "Телефон"
  },
  "Destination country": {
    "es": "País de destino",
    "ru": "Страна назначения"
  },
  "Machine model": {
    "es": "Modelo de máquina",
    "ru": "Модель машины"
  },
  "PARTS LIST": {
    "es": "LISTA DE REPUESTOS",
    "ru": "СПИСОК ЗАПЧАСТЕЙ"
  },
  "Custom enquiry — see requirements below.": {
    "es": "Consulta personalizada: consulte los requisitos a continuación.",
    "ru": "Индивидуальный запрос — см. требования ниже."
  },
  "OEM reference": {
    "es": "Referencia OEM",
    "ru": "Артикул OEM"
  },
  "REQUIREMENTS": {
    "es": "REQUISITOS",
    "ru": "ТРЕБОВАНИЯ"
  },
  "Catalogue references are illustrative. Please confirm exact fit, availability and commercial terms.": {
    "es": "Las referencias del catálogo son ilustrativas. Confirme la compatibilidad exacta, la disponibilidad y las condiciones comerciales.",
    "ru": "Артикулы в каталоге приведены для примера. Уточните совместимость, наличие и коммерческие условия."
  },
  "Agricultural parts quotation": {
    "es": "Presupuesto de repuestos agrícolas",
    "ru": "Предложение по сельхоззапчастям"
  },
  "Chat with us on WhatsApp": {
    "es": "Escríbanos por WhatsApp",
    "ru": "Напишите нам в WhatsApp"
  },
  "Hello, I would like to enquire about agricultural spare parts.": {
    "es": "Hola, quisiera consultar sobre repuestos de maquinaria agrícola.",
    "ru": "Здравствуйте! Меня интересуют запчасти для сельхозтехники."
  },
  "Your enquiry has been recorded. Reference: {reference}": {
    "es": "Su consulta se ha registrado. Referencia: {reference}",
    "ru": "Ваш запрос зарегистрирован. Номер: {reference}"
  },
  "We could not record your enquiry. Please try again or email us.": {
    "es": "No hemos podido registrar su consulta. Inténtelo de nuevo o envíenos un correo.",
    "ru": "Не удалось зарегистрировать запрос. Повторите попытку или напишите нам по электронной почте."
  },
  "Submitting…": {
    "es": "Enviando…",
    "ru": "Отправка…"
  },
  "Submit enquiry": {
    "es": "Enviar consulta",
    "ru": "Отправить запрос"
  },
  "Please wait before sending another enquiry.": {
    "es": "Espere antes de enviar otra consulta.",
    "ru": "Подождите перед отправкой следующего запроса."
  }
};
  messages['Please confirm the exact reference, compatibility and availability before ordering.'] = {es:'Confirme la referencia exacta, la compatibilidad y la disponibilidad antes de realizar un pedido.',ru:'Перед заказом уточните точный артикул, совместимость и наличие.'};
  const t = (key, values = {}) => (messages[key]?.[language] || key).replace(/\{(\w+)\}/g, (match, key) => values[key] ?? match);
  const localized = value => typeof value === "string" ? value : value?.[language] || value?.en || "";
  const name = product => localized(product.name);
  const normal = value => String(value).toLowerCase().replace(/ı/g, 'i').normalize('NFD').replace(/[\u0300-\u036f]/g, '');
  const compact = value => normal(value).replace(/[^a-z0-9]/g, '');
  const labels = {
    category: {tractor:'Tractor parts',harvester:'Harvester parts',tillage:'Soil working parts'},
    group: {hydraulic:'Hydraulics',transmission:'Transmission & drive',engine:'Engine & fuel',axle:'Axle & bearings',cutting:'Cutting & harvesting',soil:'Soil working'},
    sourcing: {enquiry:'Confirm availability', 'made-to-order':'Special sourcing enquiry'}
  };
  const label = (group, value) => group === 'brand' ? value : t(labels[group][value]);
  const filterConfig = [
    {name:'category',title:'Category',options:Object.keys(labels.category)},
    {name:'brand',title:'Machine brand reference',options:[...new Set(['John Deere','New Holland','Case IH','Massey Ferguson','Fendt',...products.flatMap(product=>product.brands)])]},
    {name:'group',title:'Component group',options:Object.keys(labels.group)},
    {name:'sourcing',title:'Sourcing enquiry',options:Object.keys(labels.sourcing)}
  ];
  const route = page => window.WLF_SITE?.urls?.[page] || ({home:'index.html',products:'products.html',about:'about.html',quote:'quote.html',contact:'contact.html'}[page]);
  const detailURL = product => { const url = new URL(route('products'),location.href);url.searchParams.set('part',product.id);return url.href; };
  let search = params.get('q')?.slice(0, 120) || '';
  let sort = ['featured','name','code'].includes(params.get('sort')) ? params.get('sort') : 'featured';
  const filters = Object.fromEntries(filterConfig.map(group => [group.name, new Set(params.getAll(group.name).filter(v => group.options.includes(v)))]));
  let openProduct = null;
  let detailTrigger = null;
  let toastTimer;

  function setupContact() {
    const config=window.WLF_SITE || {};
    const phone=String(config.phone || '').trim();
    const phoneDigits=phone.replace(/\D/g,'');
    const validPhone=phoneDigits.length>=7 && phoneDigits.length<=15;
    const phoneLink=$('#company-phone');
    if(phoneLink){phoneLink.hidden=!validPhone;phoneLink.textContent=phone;phoneLink.href='tel:'+phone.replace(/[^+\d]/g,'');}
    const phoneCard=$('#contact-phone-card');
    if(phoneCard)phoneCard.hidden=!validPhone;
    if($('#phone-pending'))$('#phone-pending').hidden=true;
    $('.contact-grid')?.classList.toggle('has-phone',validPhone);
    const number=String(config.whatsapp || '').replace(/\D/g,'');
    const enabled=!!config.whatsappEnabled && number.length>=7 && number.length<=15;
    let widget=$('#whatsapp-button');
    if(enabled && !widget){
      widget=document.createElement('a');widget.id='whatsapp-button';widget.className='whatsapp-button';widget.target='_blank';widget.rel='noopener noreferrer';
      widget.innerHTML='<svg viewBox="0 0 32 32" fill="none" aria-hidden="true"><path d="M26 15.6a10 10 0 0 1-14.8 8.8L5 26l1.7-6A10 10 0 1 1 26 15.6Z" stroke="currentColor" stroke-width="1.8"/><path d="M12 10.3c-.4-.7-.7-.7-1-.7h-.6c-.3 0-.7.2-1 .6-.4.4-1.3 1.3-1.3 3.1s1.4 3.6 1.6 3.8c.2.3 2.6 4.1 6.5 5.5 3.2 1.2 3.9.9 4.6.8.7-.1 2.3-1 2.6-2 .3-.9.3-1.7.2-1.9-.1-.1-.4-.2-.9-.5l-2.6-1.2c-.4-.2-.7-.3-1 .2l-1.2 1.5c-.2.3-.4.3-.9.1-.5-.2-1.9-.7-3.5-2.1a13 13 0 0 1-2.4-3c-.2-.4 0-.6.2-.8l.7-.9c.3-.3.4-.5.5-.8.1-.3.1-.5 0-.8l-.9-2Z" fill="currentColor" transform="translate(6.4 5.3) scale(.6)"/></svg><span class="whatsapp-tooltip"></span>';
      document.body.append(widget);
    }
    if(widget){widget.hidden=!enabled;if(enabled){widget.href='https://wa.me/'+number+'?text='+encodeURIComponent(t('Hello, I would like to enquire about agricultural spare parts.'));widget.setAttribute('aria-label',t('Chat with us on WhatsApp'));widget.title=t('Chat with us on WhatsApp');$('.whatsapp-tooltip',widget)?.replaceChildren(document.createTextNode(t('Chat with us on WhatsApp')));}}
    document.body.classList.toggle('has-whatsapp',enabled);
    if(config.restUrl && $('#email-request'))$('#email-request').innerHTML='<span>'+t('Submit enquiry')+'</span><span aria-hidden="true">→</span>';
  }

  function translateStatic() {
    document.documentElement.lang = language;
    $$('[data-en][data-es][data-ru]').forEach(el => { el.textContent = el.dataset[language]; });
    for (const [data, attribute] of [['placeholder','placeholder'],['alt','alt'],['label','aria-label'],['content','content'],['title','title']]) {
      $$('[data-'+data+'-en]').forEach(el => el.setAttribute(attribute,el.getAttribute('data-'+data+'-'+language) || el.getAttribute('data-'+data+'-en')));
    }
    const titles = {home:'Agricultural Machinery Parts',products:'Parts Catalogue',about:'About Us',quote:'Request a Quote',contact:'Contact'};
    document.title = t(titles[page] || 'Agricultural Machinery Parts') + ' | WLF Agri Parts';
    const languageButton = $('#language-switch');
    if(languageButton){languageButton.innerHTML=language.toUpperCase()+' <span aria-hidden="true">⌄</span>';languageButton.setAttribute('aria-label',t('Select language'));}
    $$('[data-language]').forEach(el=>el.setAttribute('aria-pressed',String(el.dataset.language===language)));
    $('.main-nav')?.setAttribute('aria-label',t('Main navigation'));
    $('.mobile-menu-toggle')?.setAttribute('aria-label',t('Toggle menu'));
    $('[data-close-dialog]')?.setAttribute('aria-label',t('Close product details'));
    setupContact();
    $('#year').textContent = new Date().getFullYear();
    if ($('#form-status')) $('#form-status').textContent = '';
    renderBadge();
  }

  function renderBadge() {
    $$('.quote-badge').forEach(el => { el.textContent = quote.length; el.hidden = quote.length === 0; });
    $$('[data-add]').forEach(el => {
      const product = products.find(p => p.id === el.dataset.add);
      const selected = quote.some(item => item.id === el.dataset.add);
      el.classList.toggle('is-added', selected);
      el.setAttribute('aria-label', (selected ? t("In your enquiry: ") : t("Add to enquiry: ")) + name(product));
      el.setAttribute('aria-pressed', String(selected));
      el.innerHTML = '<span>' + (selected ? t("In your enquiry") : t("Add to enquiry")) + '</span><span aria-hidden="true">' + (selected ? '✓' : '+') + '</span>';
    });
  }

  function showToast(message) {
    const toast = $('#toast');
    toast.innerHTML = '<span>'+escape(message)+'</span> <a href="'+escape(route('quote'))+'">'+t("View enquiry")+' →</a>';
    toast.classList.add('is-visible');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => toast.classList.remove('is-visible'), 5500);
  }

  function addProduct(id) {
    const product = products.find(p => p.id === id);
    if (!product) return;
    if (quote.some(item => item.id === id)) {
      showToast(t("This part is already in your enquiry."));
      return;
    }
    quote.push({id,quantity:1});
    const stored = save('wlf-enquiry-v2', quote);
    renderBadge();
    showToast(stored ? t('{part} added to your enquiry.', {part:name(product)}) : t("Added for this page. Browser storage is unavailable."));
  }

  function card(product) {
    return `<article class="product-card">
      <div class="product-visual"><a href="${escape(detailURL(product))}" data-detail="${product.id}" aria-label="${escape(t("View ")+name(product))}"><img src="${product.image}" alt="${escape(name(product))}" width="320" height="240" loading="lazy"></a><span class="product-category">${escape(label('category',product.category))}</span></div>
      <div class="product-body"><div class="product-reference"><span>${product.code}</span><small>OEM ${product.oem}</small></div><h3><a href="${escape(detailURL(product))}" data-detail="${product.id}">${escape(name(product))}</a></h3><div class="product-models">${product.brands.map(brand=>`<span>${escape(brand)}</span>`).join('')}</div><div class="product-bottom"><span class="source-label ${product.sourcing==='made-to-order'?'special':''}"><i aria-hidden="true"></i>${escape(label('sourcing',product.sourcing))}</span></div><div class="product-actions"><a class="text-link" href="${escape(detailURL(product))}" data-detail="${product.id}">${t("View details")} <span aria-hidden="true">→</span></a><button type="button" class="button button-card" data-add="${product.id}"></button></div></div></article>`;
  }

  function renderFeatured() {
    const container = $('#featured-products');
    if (!container) return;
    const featured = ['hydraulic-gear-pump','dry-clutch-disc','harvester-knife-section','front-axle-bearing'];
    const matches=featured.map(id=>products.find(p=>p.id===id)).filter(Boolean);
    container.innerHTML=(matches.length?matches:products.slice(0,4)).map(p=>card(p)).join('');
    renderBadge();
  }

  function renderFilters() {
    const container = $('#filter-groups');
    if (!container) return;
    container.innerHTML = filterConfig.map(group => `<fieldset class="filter-group"><legend>${t(group.title)}</legend>${group.options.map(value => {
      const count = products.filter(p=>group.name==='brand' ? p.brands.includes(value) : p[group.name]===value).length;
      return `<label class="filter-option"><input type="checkbox" name="${group.name}" value="${escape(value)}" ${filters[group.name].has(value)?'checked':''}><span>${escape(label(group.name,value))}</span><small>${count}</small></label>`;
    }).join('')}</fieldset>`).join('');
  }

  function matchesQuery(product) {
    if (!search.trim()) return true;
    const fields = [product.code,product.oem,...Object.values(product.name),...Object.values(product.description),...product.brands,...product.models.flatMap(m=>typeof m==='string'?[m]:Object.values(m))];
    if ([product.code,product.oem].some(value=>compact(value).includes(compact(search)) && compact(search).length>0)) return true;
    const text = normal(fields.join(' '));
    return normal(search).trim().split(/\s+/).every(token=>text.includes(token));
  }

  function filteredProducts() {
    let result = products.filter(product=>matchesQuery(product) && filterConfig.every(group=>filters[group.name].size===0 || (group.name==='brand' ? product.brands.some(brand=>filters.brand.has(brand)) : filters[group.name].has(product[group.name]))));
    if (sort==='name') result.sort((a,b)=>name(a).localeCompare(name(b),language));
    if (sort==='code') result.sort((a,b)=>a.code.localeCompare(b.code));
    return result;
  }

  function syncURL() {
    const query = new URLSearchParams(location.search);
    ['q','category','brand','group','sourcing','sort','part'].forEach(key=>query.delete(key));
    if (search) query.set('q',search);
    filterConfig.forEach(group=>filters[group.name].forEach(value=>query.append(group.name,value)));
    if (sort!=='featured') query.set('sort',sort);
    if (openProduct) query.set('part',openProduct.id);
    const tail=query.toString();
    history.replaceState(null,'',location.pathname+(tail?'?'+tail:'')+location.hash);
  }

  function renderCatalogue(updateURL = true) {
    if (!$('#catalog-grid')) return;
    const result = filteredProducts();
    $('#result-title').textContent = t('Parts found: {count}',{count:result.length});
    $('#catalog-grid').innerHTML = result.map(p=>card(p)).join('');
    $('#catalog-grid').hidden = result.length===0;
    $('#empty-state').hidden = result.length!==0;
    const chips=[];
    if (search) chips.push(`<button class="filter-chip" type="button" data-clear-search>${escape(search)} <span aria-hidden="true">×</span><span class="sr-only">${t("Remove search")}</span></button>`);
    filterConfig.forEach(group=>filters[group.name].forEach(value=>chips.push(`<button class="filter-chip" type="button" data-remove-filter="${group.name}" data-value="${escape(value)}">${escape(label(group.name,value))} <span aria-hidden="true">×</span><span class="sr-only">${t("Remove filter")}</span></button>`)));
    $('#active-filters').innerHTML=chips.join('');
    $('#catalog-sort').value = sort;
    renderBadge();
    if (updateURL) syncURL();
  }

  function resetCatalogue() {
    search='';
    filterConfig.forEach(group=>filters[group.name].clear());
    $('#catalog-search').value='';
    renderFilters();renderCatalogue();
  }

  function detailMarkup(product) {
    return `<div class="detail-layout"><div class="detail-image"><img src="${product.image}" alt="${escape(name(product))}" width="440" height="420"></div><div class="detail-copy"><div class="product-reference"><span>${product.code}</span><small>OEM ${product.oem}</small></div><h2 id="detail-title">${escape(name(product))}</h2><div class="product-models">${product.brands.map(b=>`<span>${escape(b)}</span>`).join('')}</div><p>${escape(localized(product.description))}</p><table class="spec-table"><caption class="sr-only">${t("Illustrative specifications")}</caption><tbody>${product.specs.map(spec=>`<tr><th scope="row">${escape(localized(spec.label))}</th><td>${escape(localized(spec.value))}</td></tr>`).join('')}</tbody></table><div class="detail-models"><h3>${t("Example model references")}</h3>${product.models.map(m=>`<span>${escape(localized(m))}</span>`).join('')}</div><div class="detail-actions"><button type="button" class="button" data-add="${product.id}"></button><a href="${escape(route('quote'))}" class="text-link">${t("View enquiry")} →</a></div></div></div><p class="detail-disclaimer">${t(product.isSample===false ? "Please confirm the exact reference, compatibility and availability before ordering." : "SAMPLE PRODUCT · References, specifications, images and model associations are illustrative. They are not a guarantee of stock, origin or fit. Confirm the exact reference and compatibility before ordering.")}</p>`;
  }

  function showDetail(id, trigger) {
    const product = products.find(p=>p.id===id);
    const dialog = $('#product-dialog');
    if (!product || !dialog) return;
    openProduct=product;
    detailTrigger=trigger || null;
    $('#product-detail').innerHTML=detailMarkup(product);
    renderBadge();
    if (!dialog.open) dialog.showModal();
    document.body.classList.add('dialog-open');
    if (page==='products') syncURL();
  }

  function renderQuote() {
    const container = $('#quote-items');
    if (!container) return;
    if (quote.length===0) {
      container.innerHTML=`<div class="quote-empty"><span aria-hidden="true">+</span><h3>${t("Your next parts list starts here.")}</h3><p>${t("Add parts from the catalogue, or describe a custom requirement below.")}</p><a class="text-link" href="${escape(route('products'))}">${t("Explore the catalogue")} →</a></div>`;
      return;
    }
    container.innerHTML=quote.map(item=>{
      const product=products.find(p=>p.id===item.id);
      return `<div class="quote-row"><img src="${product.image}" width="80" height="80" alt=""><div class="quote-row-info"><span>${product.code}</span><a href="${escape(detailURL(product))}">${escape(name(product))}</a><small>OEM ${product.oem}</small></div><label class="quantity-field"><span>${t("Quantity")}</span><input type="number" min="1" max="1000000" step="1" required value="${item.quantity}" data-quantity="${product.id}" aria-label="${escape(t("Quantity for ")+name(product))}"></label><button type="button" class="remove-item" data-remove="${product.id}" aria-label="${escape(t("Remove ")+name(product))}">×</button></div>`;
    }).join('');
  }

  function buildEnquiry() {
    const form=new FormData($('#quote-form'));
    const lines=[t('WLF AGRI PARTS — QUOTATION ENQUIRY'),''];
    for(const [key,label] of [['company','Company'],['contact','Contact'],['email','Email'],['phone','Phone'],['country','Destination country'],['machine','Machine model']]) lines.push(t(label)+': '+(form.get(key)||'—'));
    lines.push('',t('PARTS LIST'));
    if(!quote.length)lines.push(t('Custom enquiry — see requirements below.'));
    quote.forEach((item,index)=>{const p=products.find(p=>p.id===item.id);lines.push(`${index+1}. ${p.code} | ${name(p)} | ${t('OEM reference')}: ${p.oem} | ${t('Quantity')}: ${item.quantity}`);});
    lines.push('',t('REQUIREMENTS'),form.get('details'),'',t('Catalogue references are illustrative. Please confirm exact fit, availability and commercial terms.'));
    return lines.join('\n');
  }

  function downloadEnquiry(body) {
    const url=URL.createObjectURL(new Blob([body],{type:'text/plain;charset=utf-8'}));
    const link=document.createElement('a');link.href=url;link.download='WLF-Agri-Parts-enquiry.txt';
    document.body.append(link);link.click();link.remove();setTimeout(()=>URL.revokeObjectURL(url),1000);
    $('#form-status').textContent=t("Your enquiry file is ready. Attach it to an email to sales@wlfagriparts.com. The file has not been sent automatically.");
  }

  $$('[data-language]').forEach(button=>button.addEventListener('click',()=>{
    language=button.dataset.language;save('wlf-language',language);
    const url=new URL(location.href);url.searchParams.delete('lang');history.replaceState(null,'',url.pathname+url.search+url.hash);
    for(const input of $$('[data-quantity]')){const item=quote.find(i=>i.id===input.dataset.quantity);if(item&&input.checkValidity())item.quantity=Number(input.value);}
    translateStatic();renderFeatured();renderFilters();renderCatalogue(false);renderQuote();
    if(openProduct){$('#product-detail').innerHTML=detailMarkup(openProduct);renderBadge();}
    $('#toast').classList.remove('is-visible');
    if($('.language-picker'))$('.language-picker').open=false;
    $('#language-switch')?.focus();
  }));
  $('.mobile-menu-toggle').addEventListener('click',()=>{
    const toggle=$('.mobile-menu-toggle');const open=toggle.getAttribute('aria-expanded')!=='true';
    toggle.setAttribute('aria-expanded',String(open));$('.main-nav').classList.toggle('is-open',open);
  });
  document.addEventListener('keydown',event=>{if(event.key==='Escape'){if($('.language-picker'))$('.language-picker').open=false;$('.mobile-menu-toggle').setAttribute('aria-expanded','false');$('.main-nav').classList.remove('is-open');}});
  document.addEventListener('click',event=>{
    if(!event.target.closest('.language-picker') && $('.language-picker'))$('.language-picker').open=false;
    const add=event.target.closest('[data-add]');
    if(add){addProduct(add.dataset.add);return;}
    const detail=event.target.closest('[data-detail]');
    if(detail && $('#product-dialog') && !event.ctrlKey && !event.metaKey && !event.shiftKey && !event.altKey){event.preventDefault();showDetail(detail.dataset.detail,detail);return;}
    const remove=event.target.closest('[data-remove]');
    if(remove){const index=quote.findIndex(i=>i.id===remove.dataset.remove);quote=quote.filter(i=>i.id!==remove.dataset.remove);save('wlf-enquiry-v2',quote);renderQuote();renderBadge();const remaining=$$('[data-remove]');(remaining[Math.min(index,remaining.length-1)]||$('.quote-selection a')).focus();return;}
    const removeFilter=event.target.closest('[data-remove-filter]');
    if(removeFilter){filters[removeFilter.dataset.removeFilter].delete(removeFilter.dataset.value);renderFilters();renderCatalogue();$('#result-title').setAttribute('tabindex','-1');$('#result-title').focus();return;}
    if(event.target.closest('[data-clear-search]')){search='';$('#catalog-search').value='';renderCatalogue();$('#catalog-search').focus();}
  });

  $('#catalog-search-form')?.addEventListener('submit',event=>{event.preventDefault();search=$('#catalog-search').value.trim();renderCatalogue();});
  $('#catalog-search')?.addEventListener('input',event=>{search=event.target.value;renderCatalogue();});
  $('#catalog-sort')?.addEventListener('change',event=>{sort=event.target.value;renderCatalogue();});
  $('#filter-form')?.addEventListener('submit',event=>event.preventDefault());
  $('#filter-form')?.addEventListener('change',event=>{
    const input=event.target;if(!filters[input.name])return;
    input.checked?filters[input.name].add(input.value):filters[input.name].delete(input.value);renderCatalogue();
  });
  $('#clear-filters')?.addEventListener('click',resetCatalogue);
  $('#reset-search')?.addEventListener('click',resetCatalogue);
  const dialog=$('#product-dialog');
  $('[data-close-dialog]')?.addEventListener('click',()=>dialog.close());
  dialog?.addEventListener('click',event=>{if(event.target===dialog){const r=dialog.getBoundingClientRect();if(event.clientX<r.left||event.clientX>r.right||event.clientY<r.top||event.clientY>r.bottom)dialog.close();}});
  dialog?.addEventListener('close',()=>{openProduct=null;document.body.classList.remove('dialog-open');if(page==='products')syncURL();if(detailTrigger?.isConnected)detailTrigger.focus();});
  $('#quote-items')?.addEventListener('change',event=>{
    const input=event.target;if(!input.dataset.quantity)return;
    if(!input.checkValidity()){input.reportValidity();return;}
    const item=quote.find(i=>i.id===input.dataset.quantity);if(item){item.quantity=Number(input.value);save('wlf-enquiry-v2',quote);}
  });
  let submitting=false;
  $('#quote-form')?.addEventListener('submit',async event=>{
    event.preventDefault();
    if(submitting)return;
    for(const input of $$('[data-quantity]')){if(!input.reportValidity())return;const item=quote.find(i=>i.id===input.dataset.quantity);item.quantity=Number(input.value);}
    for(const input of $$('#quote-form [required]')){if(!input.value.trim()){input.setCustomValidity(t("Please enter this information."));input.reportValidity();input.addEventListener('input',()=>input.setCustomValidity(''),{once:true});return;}}
    save('wlf-enquiry-v2',quote);
    const body=buildEnquiry();
    if(event.submitter?.id==='download-request'){downloadEnquiry(body);return;}
    if(window.WLF_SITE?.restUrl){
      const form=$('#quote-form');
      const values=new FormData(form);
      const button=$('#email-request');
      const download=$('#download-request');
      submitting=true;button.disabled=true;if(download)download.disabled=true;
      form.setAttribute('aria-busy','true');button.textContent=t('Submitting…');
      $('#form-status').textContent='';
      try{
        const payload=Object.fromEntries(['contact','company','email','country','phone','machine','details','website'].map(key=>[key,values.get(key)||'']));
        payload.items=quote.map(item=>({id:item.id,quantity:item.quantity}));payload.lang=language;
        const response=await fetch(window.WLF_SITE.restUrl,{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json','X-WP-Nonce':window.WLF_SITE.nonce||''},body:JSON.stringify(payload)});
        const result=await response.json();
        if(!response.ok||result.saved!==true){const error=new Error('Request not recorded');error.rateLimited=response.status===429;throw error;}
        $('#form-status').textContent=t('Your enquiry has been recorded. Reference: {reference}',{reference:result.reference});
      }catch(error){
        $('#form-status').textContent=error.rateLimited?t('Please wait before sending another enquiry.'):t('We could not record your enquiry. Please try again or email us.');
      }finally{
        submitting=false;button.disabled=false;if(download)download.disabled=false;form.removeAttribute('aria-busy');setupContact();
      }
      return;
    }
    const url='mailto:'+encodeURIComponent(window.WLF_SITE?.salesEmail || 'sales@wlfagriparts.com')+'?subject='+encodeURIComponent(t('Agricultural parts quotation')+' — '+new FormData($('#quote-form')).get('company'))+'&body='+encodeURIComponent(body);
    if(url.length>1800){downloadEnquiry(body);$('#form-status').textContent=t("This enquiry is too long for a reliable email link, so it was downloaded instead. Attach the file to an email to sales@wlfagriparts.com.");return;}
    $('#form-status').textContent=t("An email draft is being opened. Review and send it in your email app. If it does not open, download your enquiry and email it to sales@wlfagriparts.com.");
    const link=document.createElement('a');link.href=url;link.target='_blank';link.rel='noopener';document.body.append(link);link.click();link.remove();
  });

  translateStatic();renderFeatured();renderFilters();renderQuote();
  if($('#catalog-search'))$('#catalog-search').value=search;
  renderCatalogue(false);
  if(params.get('part'))showDetail(params.get('part'));
  const mq=matchMedia('(max-width: 767px)');
  const setFilterPanel=()=>{const panel=$('.filters-panel');if(panel)panel.open=!mq.matches;};
  setFilterPanel();mq.addEventListener('change',setFilterPanel);
  window.addEventListener('storage',event=>{
    if(event.key==='wlf-enquiry-v2'){
      quote=cleanQuote(read('wlf-enquiry-v2', []));
      renderQuote();renderBadge();
    }
  });
})();
