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
  let language = ['en','tr'].includes(params.get('lang')) ? params.get('lang') : read('wlf-language', 'en');
  if (!['en','tr'].includes(language)) language = 'en';
  save('wlf-language', language);
  const cleanQuote = value => Array.isArray(value) ? value.filter((item, index, all) => item && products.some(p => p.id === item.id) && Number.isInteger(item.quantity) && item.quantity > 0 && item.quantity <= 1000000 && all.findIndex(i => i?.id === item.id) === index) : [];
  let quote = cleanQuote(read('wlf-enquiry-v2', []));
  const L = (en, tr) => language === 'tr' ? tr : en;
  const name = product => product.name[language];
  const normal = value => String(value).toLowerCase().replace(/ı/g, 'i').normalize('NFD').replace(/[\u0300-\u036f]/g, '');
  const compact = value => normal(value).replace(/[^a-z0-9]/g, '');
  const labels = {
    category: {tractor:['Tractor parts','Traktör Parçaları'],harvester:['Harvester parts','Biçerdöver Parçaları'],tillage:['Soil working parts','Toprak İşleme Parçaları']},
    group: {hydraulic:['Hydraulics','Hidrolik Sistem'],transmission:['Transmission & drive','Şanzıman ve Aktarma'],engine:['Engine & fuel','Motor ve Yakıt'],axle:['Axle & bearings','Aks ve Rulmanlar'],cutting:['Cutting & harvesting','Kesim ve Hasat'],soil:['Soil working','Toprak İşleme']},
    sourcing: {enquiry:['Confirm availability','Stok Teyidi İste'], 'made-to-order':['Special sourcing enquiry','Özel Tedarik Talebi']}
  };
  const label = (group, value) => group === 'brand' ? value : L(...labels[group][value]);
  const filterConfig = [
    {name:'category',title:['Category','Kategori'],options:Object.keys(labels.category)},
    {name:'brand',title:['Machine brand reference','Makine Markası Referansı'],options:['John Deere','New Holland','Case IH','Massey Ferguson','Fendt']},
    {name:'group',title:['Component group','Aksam Grubu'],options:Object.keys(labels.group)},
    {name:'sourcing',title:['Sourcing enquiry','Tedarik Talebi'],options:Object.keys(labels.sourcing)}
  ];
  let search = params.get('q')?.slice(0, 120) || '';
  let sort = ['featured','name','code'].includes(params.get('sort')) ? params.get('sort') : 'featured';
  const filters = Object.fromEntries(filterConfig.map(group => [group.name, new Set(params.getAll(group.name).filter(v => group.options.includes(v)))]));
  let openProduct = null;
  let detailTrigger = null;
  let toastTimer;

  function translateStatic() {
    document.documentElement.lang = language;
    $$('[data-en][data-tr]').forEach(el => { el.textContent = el.dataset[language]; });
    $$('[data-placeholder-en]').forEach(el => { el.placeholder = language === 'tr' ? el.dataset.placeholderTr : el.dataset.placeholderEn; });
    $$('[data-alt-en]').forEach(el => { el.alt = language === 'tr' ? el.dataset.altTr : el.dataset.altEn; });
    const titles = {home:['Agricultural Machinery Parts','Tarım Makineleri Yedek Parçaları'],products:['Parts Catalogue','Yedek Parça Kataloğu'],about:['About Us','Hakkımızda'],quote:['Request a Quote','Teklif İste'],contact:['Contact','İletişim']};
    document.title = L(...titles[page]) + ' | WLF Agri Parts';
    const languageButton = $('#language-switch');
    languageButton.innerHTML = language.toUpperCase() + ' <span aria-hidden="true">⌄</span>';
    languageButton.setAttribute('aria-label', L('Türkçeye geç', 'Switch to English'));
    $('.main-nav').setAttribute('aria-label', L('Main navigation','Ana menü'));
    $('.mobile-menu-toggle').setAttribute('aria-label', L('Toggle menu','Menüyü aç veya kapat'));
    $('[data-close-dialog]')?.setAttribute('aria-label', L('Close product details','Ürün detayını kapat'));
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
      el.setAttribute('aria-label', (selected ? L('In your enquiry: ','Teklif listenizde: ') : L('Add to enquiry: ','Teklif listesine ekle: ')) + name(product));
      el.setAttribute('aria-pressed', String(selected));
      el.innerHTML = '<span>' + (selected ? L('In your enquiry','Teklif Listenizde') : L('Add to enquiry','Teklif Listesine Ekle')) + '</span><span aria-hidden="true">' + (selected ? '✓' : '+') + '</span>';
    });
  }

  function showToast(message) {
    const toast = $('#toast');
    toast.innerHTML = '<span>'+escape(message)+'</span> <a href="quote.html">'+L('View enquiry','Talebi Gör')+' →</a>';
    toast.classList.add('is-visible');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => toast.classList.remove('is-visible'), 5500);
  }

  function addProduct(id) {
    const product = products.find(p => p.id === id);
    if (!product) return;
    if (quote.some(item => item.id === id)) {
      showToast(L('This part is already in your enquiry.','Bu parça zaten teklif listenizde.'));
      return;
    }
    quote.push({id,quantity:1});
    const stored = save('wlf-enquiry-v2', quote);
    renderBadge();
    showToast(stored ? L(name(product)+' added to your enquiry.',name(product)+' teklif listenize eklendi.') : L('Added for this page. Browser storage is unavailable.','Bu sayfa için eklendi. Tarayıcı depolama alanına erişilemiyor.'));
  }

  function card(product) {
    return `<article class="product-card">
      <div class="product-visual"><a href="products.html?part=${product.id}" data-detail="${product.id}" aria-label="${escape(L('View ','İncele: ')+name(product))}"><img src="${product.image}" alt="${escape(name(product))}" width="320" height="240" loading="lazy"></a><span class="product-category">${escape(label('category',product.category))}</span></div>
      <div class="product-body"><div class="product-reference"><span>${product.code}</span><small>OEM ${product.oem}</small></div><h3><a href="products.html?part=${product.id}" data-detail="${product.id}">${escape(name(product))}</a></h3><div class="product-models">${product.brands.map(brand=>`<span>${escape(brand)}</span>`).join('')}</div><div class="product-bottom"><span class="source-label ${product.sourcing==='made-to-order'?'special':''}"><i aria-hidden="true"></i>${escape(label('sourcing',product.sourcing))}</span></div><div class="product-actions"><a class="text-link" href="products.html?part=${product.id}" data-detail="${product.id}">${L('View details','Detayları İncele')} <span aria-hidden="true">→</span></a><button type="button" class="button button-card" data-add="${product.id}"></button></div></div></article>`;
  }

  function renderFeatured() {
    const container = $('#featured-products');
    if (!container) return;
    const featured = ['hydraulic-gear-pump','dry-clutch-disc','harvester-knife-section','front-axle-bearing'];
    container.innerHTML = featured.map(id=>products.find(p=>p.id===id)).filter(Boolean).map(p=>card(p)).join('');
    renderBadge();
  }

  function renderFilters() {
    const container = $('#filter-groups');
    if (!container) return;
    container.innerHTML = filterConfig.map(group => `<fieldset class="filter-group"><legend>${L(...group.title)}</legend>${group.options.map(value => {
      const count = products.filter(p=>group.name==='brand' ? p.brands.includes(value) : p[group.name]===value).length;
      return `<label class="filter-option"><input type="checkbox" name="${group.name}" value="${escape(value)}" ${filters[group.name].has(value)?'checked':''}><span>${escape(label(group.name,value))}</span><small>${count}</small></label>`;
    }).join('')}</fieldset>`).join('');
  }

  function matchesQuery(product) {
    if (!search.trim()) return true;
    const fields = [product.code,product.oem,product.name.en,product.name.tr,product.description.en,product.description.tr,...product.brands,...product.models];
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
    const query = new URLSearchParams();
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
    $('#result-title').textContent = language==='tr' ? `${result.length} parça bulundu` : `${result.length} ${result.length===1?'part':'parts'} found`;
    $('#catalog-grid').innerHTML = result.map(p=>card(p)).join('');
    $('#catalog-grid').hidden = result.length===0;
    $('#empty-state').hidden = result.length!==0;
    const chips=[];
    if (search) chips.push(`<button class="filter-chip" type="button" data-clear-search>${escape(search)} <span aria-hidden="true">×</span><span class="sr-only">${L('Remove search','Aramayı kaldır')}</span></button>`);
    filterConfig.forEach(group=>filters[group.name].forEach(value=>chips.push(`<button class="filter-chip" type="button" data-remove-filter="${group.name}" data-value="${escape(value)}">${escape(label(group.name,value))} <span aria-hidden="true">×</span><span class="sr-only">${L('Remove filter','Filtreyi kaldır')}</span></button>`)));
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
    return `<div class="detail-layout"><div class="detail-image"><img src="${product.image}" alt="${escape(name(product))}" width="440" height="420"></div><div class="detail-copy"><div class="product-reference"><span>${product.code}</span><small>OEM ${product.oem}</small></div><h2 id="detail-title">${escape(name(product))}</h2><div class="product-models">${product.brands.map(b=>`<span>${escape(b)}</span>`).join('')}</div><p>${escape(product.description[language])}</p><table class="spec-table"><caption class="sr-only">${L('Illustrative specifications','Örnek teknik bilgiler')}</caption><tbody>${product.specs.map(spec=>`<tr><th scope="row">${escape(spec.label[language])}</th><td>${escape(spec.value[language])}</td></tr>`).join('')}</tbody></table><div class="detail-models"><h3>${L('Example model references','Örnek Model Referansları')}</h3>${product.models.map(m=>`<span>${escape(m)}</span>`).join('')}</div><div class="detail-actions"><button type="button" class="button" data-add="${product.id}"></button><a href="quote.html" class="text-link">${L('View enquiry','Talebi Gör')} →</a></div></div></div><p class="detail-disclaimer">${L('SAMPLE PRODUCT · References, specifications, images and model associations are illustrative. They are not a guarantee of stock, origin or fit. Confirm the exact reference and compatibility before ordering.','ÖRNEK ÜRÜN · Referanslar, teknik bilgiler, görseller ve model eşleşmeleri temsildir. Stok, menşe veya uyumluluk garantisi değildir. Sipariş öncesi kesin referansı ve uyumluluğu teyit edin.')}</p>`;
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
      container.innerHTML=`<div class="quote-empty"><span aria-hidden="true">+</span><h3>${L('Your next parts list starts here.','Yeni parça listeniz burada başlıyor.')}</h3><p>${L('Add parts from the catalogue, or describe a custom requirement below.','Katalogdan parçalar ekleyin veya aşağıda özel ihtiyacınızı açıklayın.')}</p><a class="text-link" href="products.html">${L('Explore the catalogue','Kataloğu Keşfet')} →</a></div>`;
      return;
    }
    container.innerHTML=quote.map(item=>{
      const product=products.find(p=>p.id===item.id);
      return `<div class="quote-row"><img src="${product.image}" width="80" height="80" alt=""><div class="quote-row-info"><span>${product.code}</span><a href="products.html?part=${product.id}">${escape(name(product))}</a><small>OEM ${product.oem}</small></div><label class="quantity-field"><span>${L('Quantity','Miktar')}</span><input type="number" min="1" max="1000000" step="1" required value="${item.quantity}" data-quantity="${product.id}" aria-label="${escape(L('Quantity for ','Miktar: ')+name(product))}"></label><button type="button" class="remove-item" data-remove="${product.id}" aria-label="${escape(L('Remove ','Listeden çıkar: ')+name(product))}">×</button></div>`;
    }).join('');
  }

  function buildEnquiry() {
    const form=new FormData($('#quote-form'));
    const lines=['WLF AGRI PARTS — QUOTATION ENQUIRY','',`Company: ${form.get('company')}`,`Contact: ${form.get('contact')}`,`Email: ${form.get('email')}`,`Phone: ${form.get('phone')||'—'}`,`Destination country: ${form.get('country')}`,`Machine model: ${form.get('machine')||'—'}`,'','PARTS LIST'];
    if (!quote.length) lines.push('Custom enquiry — see requirements below.');
    quote.forEach((item,index)=>{const p=products.find(p=>p.id===item.id);lines.push(`${index+1}. ${p.code} | ${p.name.en} | OEM reference: ${p.oem} | Quantity: ${item.quantity}`);});
    lines.push('','REQUIREMENTS',form.get('details'),'','Catalogue references are illustrative. Please confirm exact fit, availability and commercial terms.');
    return lines.join('\n');
  }

  function downloadEnquiry(body) {
    const url=URL.createObjectURL(new Blob([body],{type:'text/plain;charset=utf-8'}));
    const link=document.createElement('a');link.href=url;link.download='WLF-Agri-Parts-enquiry.txt';
    document.body.append(link);link.click();link.remove();setTimeout(()=>URL.revokeObjectURL(url),1000);
    $('#form-status').textContent=L('Your enquiry file is ready. Attach it to an email to sales@wlfagriparts.com. The file has not been sent automatically.','Talep dosyanız hazır. sales@wlfagriparts.com adresine göndereceğiniz e-postaya ekleyin. Dosya otomatik olarak gönderilmedi.');
  }

  $('#language-switch').addEventListener('click',()=>{
    language=language==='en'?'tr':'en';save('wlf-language',language);
    const url = new URL(location.href);url.searchParams.delete('lang');history.replaceState(null,'',url.pathname+url.search+url.hash);
    translateStatic();
    renderFeatured();renderFilters();renderCatalogue(false);renderQuote();
    if (openProduct) { $('#product-detail').innerHTML=detailMarkup(openProduct);renderBadge(); }
    $('#toast').classList.remove('is-visible');
  });
  $('.mobile-menu-toggle').addEventListener('click',()=>{
    const toggle=$('.mobile-menu-toggle');const open=toggle.getAttribute('aria-expanded')!=='true';
    toggle.setAttribute('aria-expanded',String(open));$('.main-nav').classList.toggle('is-open',open);
  });
  document.addEventListener('keydown',event=>{if(event.key==='Escape'){$('.mobile-menu-toggle').setAttribute('aria-expanded','false');$('.main-nav').classList.remove('is-open');}});
  document.addEventListener('click',event=>{
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
  $('#quote-form')?.addEventListener('submit',event=>{
    event.preventDefault();
    for(const input of $$('[data-quantity]')){if(!input.reportValidity())return;const item=quote.find(i=>i.id===input.dataset.quantity);item.quantity=Number(input.value);}
    for(const input of $$('#quote-form [required]')){if(!input.value.trim()){input.setCustomValidity(L('Please enter this information.','Lütfen bu bilgiyi girin.'));input.reportValidity();input.addEventListener('input',()=>input.setCustomValidity(''),{once:true});return;}}
    save('wlf-enquiry-v2',quote);
    const body=buildEnquiry();
    if(event.submitter?.id==='download-request'){downloadEnquiry(body);return;}
    const url='mailto:sales@wlfagriparts.com?subject='+encodeURIComponent('Agricultural parts quotation — '+new FormData($('#quote-form')).get('company'))+'&body='+encodeURIComponent(body);
    if(url.length>1800){downloadEnquiry(body);$('#form-status').textContent=L('This enquiry is too long for a reliable email link, so it was downloaded instead. Attach the file to an email to sales@wlfagriparts.com.','Bu talep e-posta bağlantısı için uzun olduğundan dosya olarak indirildi. Dosyayı sales@wlfagriparts.com adresine göndereceğiniz e-postaya ekleyin.');return;}
    $('#form-status').textContent=L('An email draft is being opened. Review and send it in your email app. If it does not open, download your enquiry and email it to sales@wlfagriparts.com.','E-posta taslağı açılıyor. Posta uygulamanızda inceleyip gönderin. Açılmazsa talebinizi indirip sales@wlfagriparts.com adresine gönderin.');
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
