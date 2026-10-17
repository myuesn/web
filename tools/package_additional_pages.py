"""Build an additive WordPress installer for two editable EN/ES/RU draft pages."""
from html import escape
from pathlib import Path
from zipfile import ZipFile, ZipInfo, ZIP_DEFLATED
import json

ROOT = Path(__file__).resolve().parents[1]
PLUGIN = ROOT / 'wordpress/wlf-ready-pages'


def block(name, html, attrs=None):
    encoded = (' ' + json.dumps(attrs, ensure_ascii=False, separators=(',', ':')).replace('&', '\\u0026').replace('<', '\\u003c').replace('>', '\\u003e')) if attrs else ''
    return f'<!-- wp:{name}{encoded} -->{html}<!-- /wp:{name} -->\n'


def text(values, heading=False, level=2, css=''):
    assert len(values) == 3 and all(values)
    attrs = {'wlfEs': values[1], 'wlfRu': values[2]}
    if css:
        attrs['className'] = css
    if heading and level != 2:
        attrs['level'] = level
    tag = f'h{level}' if heading else 'p'
    classes = ('wp-block-heading ' if heading else '') + css
    class_attr = f' class="{classes.strip()}"' if classes.strip() else ''
    return block('heading' if heading else 'paragraph', f'<{tag}{class_attr}>{escape(values[0])}</{tag}>', attrs)


def group(css, content):
    return block('group', f'<div class="wp-block-group {css}">{content}</div>', {'className': css})


FAQ = [
    (
        ('How can I request a quotation?', '¿Cómo puedo solicitar un presupuesto?', 'Как запросить коммерческое предложение?'),
        ('Select the parts you need and click “Request a Quote”. Include the quantity, machine make and model, and destination country. If you cannot find a part, send its OEM or reference number through the enquiry form.',
         'Seleccione los repuestos que necesita y pulse «Solicitar presupuesto». Indique la cantidad, la marca y el modelo de la máquina y el país de destino. Si no encuentra una pieza, envíe su número OEM o de referencia mediante el formulario.',
         'Выберите нужные запчасти и нажмите «Запросить предложение». Укажите количество, марку и модель техники и страну назначения. Если нужной детали нет в каталоге, отправьте её OEM-номер или артикул через форму запроса.'),
    ),
    (
        ('Are prices displayed on the website?', '¿Se muestran los precios en la web?', 'Указаны ли цены на сайте?'),
        ('Prices are provided by quotation. Send your part references, required quantities and destination country so we can review your request and prepare an offer.',
         'Los precios se facilitan mediante presupuesto. Envíe las referencias de los repuestos, las cantidades y el país de destino para que podamos estudiar su solicitud y preparar una oferta.',
         'Цены предоставляются в коммерческом предложении. Отправьте артикулы, необходимое количество и страну назначения, чтобы мы могли рассмотреть запрос и подготовить предложение.'),
    ),
    (
        ('How can I check whether a part fits my machine?', '¿Cómo puedo comprobar la compatibilidad de una pieza?', 'Как проверить совместимость детали с моей техникой?'),
        ('Include the machine make, model, model year and OEM or reference number in your enquiry. Photos, dimensions and technical specifications can help us review compatibility. A matching reference alone does not guarantee suitability; confirm the details before ordering.',
         'Incluya la marca, el modelo, el año y el número OEM o de referencia. Las fotos, las medidas y las especificaciones técnicas pueden ayudar a revisar la compatibilidad. La coincidencia de una referencia no garantiza por sí sola la compatibilidad; confirme los datos antes de realizar el pedido.',
         'Укажите марку, модель, год выпуска техники и OEM-номер или артикул. Фотографии, размеры и технические характеристики помогут проверить совместимость. Совпадение артикула само по себе не гарантирует совместимость; уточните данные до заказа.'),
    ),
    (
        ('What if the part I need is not listed?', '¿Qué hago si la pieza que necesito no aparece?', 'Что делать, если нужной детали нет в каталоге?'),
        ('Send an enquiry with the part description, OEM or reference number, machine details and quantity. We will review the request; a sourcing enquiry does not confirm availability.',
         'Envíe una solicitud con la descripción de la pieza, el número OEM o de referencia, los datos de la máquina y la cantidad. Revisaremos su solicitud; una consulta de suministro no confirma la disponibilidad.',
         'Отправьте запрос с описанием детали, OEM-номером или артикулом, данными техники и количеством. Мы рассмотрим запрос; отправка запроса не подтверждает наличие.'),
    ),
    (
        ('Is there a minimum order quantity?', '¿Existe una cantidad mínima de pedido?', 'Есть ли минимальное количество для заказа?'),
        ('Minimum order quantities may vary by part and sourcing requirements. Include your required quantity in the enquiry; applicable quantities will be confirmed in the quotation.',
         'Las cantidades mínimas pueden variar según la pieza y las condiciones de suministro. Indique la cantidad que necesita; las cantidades aplicables se confirmarán en el presupuesto.',
         'Минимальное количество может зависеть от детали и условий поставки. Укажите необходимое количество в запросе; применимые условия будут подтверждены в предложении.'),
    ),
    (
        ('How are availability and delivery times confirmed?', '¿Cómo se confirman la disponibilidad y los plazos de entrega?', 'Как подтверждаются наличие и сроки поставки?'),
        ('Availability and lead times are reviewed for each enquiry. Share the destination country and any required delivery date. Dispatch schedules, transit times and shipping costs must be confirmed before you place an order.',
         'La disponibilidad y los plazos se revisan para cada solicitud. Indique el país de destino y la fecha de entrega deseada. El calendario de expedición, el tránsito y los gastos de transporte deben confirmarse antes de realizar el pedido.',
         'Наличие и сроки рассматриваются по каждому запросу. Укажите страну назначения и желаемую дату получения. График отгрузки, срок перевозки и стоимость доставки необходимо согласовать до заказа.'),
    ),
    (
        ('What are the payment terms?', '¿Cuáles son las condiciones de pago?', 'Каковы условия оплаты?'),
        ('Payment terms and currency are specified in the quotation or pro forma invoice for each order. Review and agree the commercial terms before making a payment.',
         'Las condiciones de pago y la moneda se indican en el presupuesto o la factura proforma de cada pedido. Revise y acuerde las condiciones comerciales antes de efectuar un pago.',
         'Условия оплаты и валюта указываются в предложении или счёте-проформе для каждого заказа. До оплаты ознакомьтесь с коммерческими условиями и согласуйте их.'),
    ),
    (
        ('How are packaging and export documents arranged?', '¿Cómo se acuerdan el embalaje y los documentos de exportación?', 'Как согласуются упаковка и экспортные документы?'),
        ('Share any packaging, labelling or documentation requirements with your enquiry. Required documents, responsibilities, costs and applicable delivery terms are agreed for each order before dispatch.',
         'Indique en su solicitud los requisitos de embalaje, etiquetado o documentación. Los documentos necesarios, las responsabilidades, los costes y las condiciones de entrega se acuerdan para cada pedido antes de la expedición.',
         'Укажите в запросе требования к упаковке, маркировке и документам. Необходимые документы, обязанности сторон, расходы и условия поставки согласуются для каждого заказа до отгрузки.'),
    ),
]

PROCESS = [
    (
        ('01 / Prepare your enquiry', '01 / Prepare su solicitud', '01 / Подготовьте запрос'),
        ('List the parts you need, including OEM or reference numbers, quantities, machine make and model, and destination country. Add photos or dimensions when the reference is unclear.',
         'Prepare una lista de piezas con sus números OEM o de referencia, cantidades, marca y modelo de la máquina y país de destino. Añada fotos o medidas si la referencia no está clara.',
         'Составьте список запчастей с OEM-номерами или артикулами, количеством, маркой и моделью техники и страной назначения. Если артикул неизвестен, добавьте фотографии или размеры.'),
    ),
    (
        ('02 / Review the part details', '02 / Revise los datos de las piezas', '02 / Уточните данные деталей'),
        ('Part references, technical details and sourcing options are reviewed before an offer is prepared. Additional machine information may be requested to clarify compatibility. Catalogue listings do not replace a compatibility check.',
         'Las referencias, los datos técnicos y las opciones de suministro se revisan antes de preparar una oferta. Podemos solicitar más información de la máquina para aclarar la compatibilidad. El catálogo no sustituye la comprobación de compatibilidad.',
         'Перед подготовкой предложения рассматриваются артикулы, технические данные и варианты поставки. Для уточнения совместимости могут потребоваться дополнительные сведения о технике. Наличие позиции в каталоге не заменяет проверку совместимости.'),
    ),
    (
        ('03 / Review your quotation', '03 / Revise su presupuesto', '03 / Рассмотрите предложение'),
        ('Check the part descriptions, quantities, prices, currency, availability, lead times and quotation validity. Packaging, transport costs, delivery terms and payment conditions should be clear before you proceed.',
         'Compruebe las descripciones, cantidades, precios, moneda, disponibilidad, plazos y validez del presupuesto. El embalaje, los gastos de transporte, las condiciones de entrega y de pago deben quedar claros antes de continuar.',
         'Проверьте описание деталей, количество, цены, валюту, наличие, сроки и срок действия предложения. До продолжения необходимо уточнить упаковку, транспортные расходы, условия поставки и оплаты.'),
    ),
    (
        ('04 / Confirm the order', '04 / Confirme el pedido', '04 / Подтвердите заказ'),
        ('Confirm the agreed items and commercial terms in writing. Check the company, billing and delivery details on the order confirmation or pro forma invoice. An enquiry alone does not confirm an order or reserve stock.',
         'Confirme por escrito los artículos y las condiciones comerciales acordadas. Compruebe los datos de la empresa, facturación y entrega en la confirmación o factura proforma. Una solicitud por sí sola no confirma un pedido ni reserva existencias.',
         'Письменно подтвердите согласованные позиции и коммерческие условия. Проверьте реквизиты компании, данные для выставления счёта и доставки в подтверждении заказа или счёте-проформе. Сам запрос не подтверждает заказ и не резервирует товар.'),
    ),
    (
        ('05 / Agree dispatch and documentation', '05 / Acuerde la expedición y la documentación', '05 / Согласуйте отгрузку и документы'),
        ('Agree the packaging, transport arrangements, delivery terms and documents required for the destination. Customs duties, taxes, insurance and clearance responsibilities must be clarified for the specific shipment; they are not automatically included.',
         'Acuerde el embalaje, el transporte, las condiciones de entrega y los documentos necesarios para el destino. Los aranceles, impuestos, seguros y responsabilidades de despacho deben aclararse para cada envío; no se incluyen automáticamente.',
         'Согласуйте упаковку, перевозку, условия поставки и документы для страны назначения. Пошлины, налоги, страхование и ответственность за таможенное оформление необходимо уточнить для конкретной отправки; они не включаются автоматически.'),
    ),
    (
        ('06 / Check your delivery', '06 / Compruebe la entrega', '06 / Проверьте полученный товар'),
        ('On receipt, check the package condition, quantities and part references against the shipment documents. If you notice a discrepancy or damage, document it with photos and contact us with your order reference. Any return or warranty terms are those agreed for the order.',
         'Al recibir la mercancía, compruebe el estado del embalaje, las cantidades y las referencias con los documentos del envío. Si detecta diferencias o daños, haga fotos y contacte con nosotros indicando la referencia del pedido. Las devoluciones y garantías se rigen por las condiciones acordadas para el pedido.',
         'При получении проверьте состояние упаковки, количество и артикулы по отгрузочным документам. При расхождениях или повреждениях сделайте фотографии и свяжитесь с нами, указав номер заказа. Условия возврата и гарантии определяются договорённостями по заказу.'),
    ),
]


def page(title, slug, eyebrow, heading, intro, sections):
    hero = group('page-intro', group('container', text(eyebrow, css='eyebrow') + text(heading, heading=True, level=1) + text(intro)))
    body = ''.join(group('faq-item', text(question, heading=True) + text(answer)) for question, answer in sections)
    content = hero + group('section', group('container wlf-ready-content', body))
    content += group('section', group('container', text(('Ready to discuss your parts list?', '¿Quiere consultar su lista de repuestos?', 'Готовы обсудить список запчастей?'), heading=True) + text(('Use “Request a Quote” in the main menu, or contact our sales team using the details below.', 'Utilice «Solicitar presupuesto» en el menú principal o contacte con nuestro equipo de ventas mediante los datos siguientes.', 'Выберите «Запросить предложение» в главном меню или свяжитесь с отделом продаж по указанным ниже контактам.')) + block('shortcode', '[wlf_contact_details]')))
    return {'title': title, 'slug': slug, 'content': content}


pages = [
    page('WLF Frequently Asked Questions', 'wlf-faq', ('USEFUL ANSWERS', 'RESPUESTAS ÚTILES', 'ПОЛЕЗНЫЕ ОТВЕТЫ'),
         ('Frequently asked questions', 'Preguntas frecuentes', 'Часто задаваемые вопросы'),
         ('Practical information about quotations, part references and international B2B orders.', 'Información práctica sobre presupuestos, referencias de piezas y pedidos internacionales B2B.', 'Практическая информация о предложениях, артикулах и международных заказах B2B.'), FAQ),
    page('WLF Order & Export Process', 'wlf-order-export', ('FROM ENQUIRY TO DELIVERY', 'DE LA SOLICITUD A LA ENTREGA', 'ОТ ЗАПРОСА ДО ПОЛУЧЕНИЯ'),
         ('Order & export process', 'Proceso de pedido y exportación', 'Процесс заказа и экспорта'),
         ('A clear starting point for planning your agricultural parts order. Commercial and delivery terms are confirmed individually.', 'Un punto de partida claro para planificar su pedido de repuestos agrícolas. Las condiciones comerciales y de entrega se confirman individualmente.', 'Понятная основа для планирования заказа сельхоззапчастей. Коммерческие условия и условия поставки подтверждаются индивидуально.'), PROCESS),
]
(PLUGIN / 'pages.json').write_text(json.dumps(pages, ensure_ascii=False, indent=2) + '\n', encoding='utf-8')
(PLUGIN / 'README.txt').write_text('WLF Hazır Sayfalar\n\nWordPress → Plugins / Eklentiler → Add New / Yeni Ekle → Upload Plugin / Eklenti Yükle.\nwlf-hazir-sayfalar.zip dosyasını açmadan yükleyin ve etkinleştirin.\n\nİki yeni sayfa taslak olarak eklenir:\n- WLF Frequently Asked Questions (8 soru ve cevap)\n- WLF Order & Export Process (6 adım)\n\nİngilizce, İspanyolca ve Rusça metinler hazırdır. Tasarım ve dil seçimi kurulu WLF Agri temasını kullanır.\nEklentiler ekranındaki bağlantılardan sayfaları önizleyip Publish / Yayımla ile yayımlayın.\nMevcut sayfalar, ürünler, logolar ve ayarlar değiştirilmez. Daha önce elle açtığınız SSS taslağı da korunur.\nYeniden etkinleştirme sayfaları çoğaltmaz ve düzenlemelerinizi değiştirmez.\nEklentiyi devre dışı bırakmak veya silmek sayfaları silmez. Metinler WordPress bloklarıyla düzenlenir.\nYayımlanan sayfaları Görünüm → Menüler bölümünden istediğiniz menüye ekleyebilirsiniz.\n', encoding='utf-8')
target = ROOT / 'downloads/wlf-hazir-sayfalar.zip'
PLUGIN.chmod(0o755)
with ZipFile(target, 'w', compression=ZIP_DEFLATED) as archive:
    directory = ZipInfo('wlf-ready-pages/', date_time=(2026, 10, 17, 0, 0, 0))
    directory.create_system = 3
    directory.external_attr = (0o40755 << 16) | 0x10
    archive.writestr(directory, b'')
    for path in sorted(PLUGIN.iterdir()):
        if not path.is_file():
            continue
        assert not path.is_symlink()
        path.chmod(0o644)
        info = ZipInfo('wlf-ready-pages/' + path.name, date_time=(2026, 10, 17, 0, 0, 0))
        info.create_system = 3
        info.external_attr = 0o100644 << 16
        info.compress_type = ZIP_DEFLATED
        archive.writestr(info, path.read_bytes())
with ZipFile(target) as archive:
    assert archive.testzip() is None
    for path in PLUGIN.iterdir():
        if path.is_file():
            assert archive.read('wlf-ready-pages/' + path.name) == path.read_bytes()
print(f'{target}: {target.stat().st_size} bytes; two editable trilingual draft pages; ZIP integrity verified')
