# Başkan için WordPress kurulum ve kullanım rehberi

Bu sürüm HTML dosyası yüklemek yerine WordPress panelinden kurulur. Tema tasarımı; eklenti ürünleri ve özel teklif kayıtlarını yönetir. Ön yüz dilleri İngilizce, İspanyolca ve Rusçadır. Türkçe dil seçeneği kaldırılmıştır; yönetim ekranlarındaki Türkçe açıklamalar size yöneliktir.

## Kurulum

Mevcut WordPress sitenizin dosya ve veritabanı yedeğini alın. `public_html` veya `/yeni/` içindeki dosyaları silmeyin.

1. `https://wlfagriparts.com/wp-admin/` üzerinden kendi WordPress hesabınıza girin.
2. **Eklentiler → Yeni eklenti ekle → Eklenti yükle:** `wlf-agri-core.zip` dosyasını yükleyip etkinleştirin.
3. **Görünüm → Temalar → Yeni tema ekle → Tema yükle:** `wlf-agri-theme.zip` dosyasını yükleyip etkinleştirin.
4. **Görünüm → WLF Başlangıç** sayfasını açın. Başlangıç sayfalarını oluşturun. Yeni WLF Home sayfasını ana sayfa yapmak istiyorsanız ilgili kutuyu işaretleyin. İşlem mevcut sayfaları silmez veya metinlerini değiştirmez. Aynı kurulum düğmesine tekrar basmak sayfaları çoğaltmaz.

ZIP dosyalarını bilgisayarınızda açmadan yükleyin. Tema ZIP'i Eklentiler bölümüne, eklenti ZIP'i Temalar bölümüne yüklenmez. WordPress sürümü ana alan adınızda çalışır; `/yeni/` önceki bağımsız HTML denemesidir ve WordPress panelinden yönetilmez.

## Tema güncellemesi 1.0.1

Güncel `wlf-agri-theme.zip` dosyasını **Görünüm → Temalar → Yeni Tema Ekle → Tema Yükle** bölümünden yükleyip **Mevcut temayı yüklenenle değiştir** seçeneğini kullanın. Bu bir tema güncellemesidir. Aynı tema klasörü korunduğu için WordPress sayfa içerikleri, ürünler, teklif kayıtları ve Özelleştir alanındaki numara/logo ayarları korunur. Özel olarak tema dosyalarını elle düzenlediyseniz önce bunların yedeğini alın; tema dosyaları güncellenir. Hosting/WordPress önbelleğini temizleyip sayfayı yenileyin.

Bu sürümde WhatsApp simgesinin içindeki telefon küçültülüp ortalandı; mevcut nabız animasyonu ve hareket azaltma desteği korunur. Şeffaf footer logosunun arkasındaki beyaz CSS kutusu kaldırıldı. Atanmış alt menüdeki tanınan eski site içi sayfa bağlantıları ön yüzde mevcut WLF sayfalarının adreslerine çözülür; dış bağlantılar ve diğer özel bağlantılar korunur. Menü kayıtları veritabanında yeniden yazılmaz. Özel alt menü atanmamışsa, yayımlanmış SSS ve sipariş / ihracat sayfaları otomatik listelenir.

## Ürün ekleme, düzenleme ve kaldırma

**WLF Ürünler → Yeni ürün ekle** bölümünü kullanın. Başlık ve ana açıklama İngilizcedir. WLF kodu, OEM, kategori, aksam, marka/model, İspanyolca/Rusça açıklamalar ve teknik özellikler için ayrı alanlar vardır. Ürün fotoğrafını **Öne çıkan görsel** bölümünden seçin. Teknik özelliklere yeni satır ekleyip istemediklerinizi kaldırabilirsiniz. Çeviri boşsa İngilizce gösterilir.

**Yayımla** ile ürün katalogda görünür. Hosting veya WordPress önbelleği kullanıyorsanız ürün değişikliğinden sonra ilgili sayfaların önbelleğini temizleyin. **Taslak** veya **Çöp kutusu** katalogdan kaldırır. Fiyat, alışveriş sepeti veya ödeme ekranı yoktur.

Örnek ürünler otomatik yayımlanmaz. İsterseniz **WLF Ürünler → Örnek ürünler** bölümünden 12 taslak örnek ekleyebilirsiniz. OEM, teknik özellik, görsel ve model eşleşmeleri örnektir; gerçek bilgilerle doğrulayıp değiştirmeden ticari veri olarak yayımlamayın.

## Sayfa tasarımını değiştirme

**Sayfalar → WLF Home / WLF About us / WLF Contact / … → Düzenle** bölümünde metinler, başlıklar, düğmeler ve görseller normal WordPress bloklarıdır. **Liste görünümü** üzerinden bölümleri taşıyabilir, silebilir veya ekleyebilirsiniz. İngilizce metni blok üzerinde düzenleyin. Metin veya düğme bloğunu seçince sağdaki **WLF — Dil çevirileri** alanından İspanyolca ve Rusçasını girin.

Katalog, öne çıkan ürünler, arama, teklif formu ve iletişim bilgileri işlevsel kısa kod bloklarıdır: `[wlf_catalogue]`, `[wlf_featured]`, `[wlf_home_search]`, `[wlf_enquiry]`, `[wlf_contact_details]`. İşlevi korumak için kısa kodu değiştirmeyin. Her kısa kodu bir sayfada yalnızca bir kez kullanın; çevresindeki bölümleri düzenleyebilirsiniz.

## İsteğe bağlı hazır SSS ve sipariş / ihracat sayfaları

`downloads/wlf-hazir-sayfalar.zip` iki ek sayfa için ayrı bir kurulum eklentisidir. **Eklentiler → Yeni Ekle → Eklenti Yükle** üzerinden ZIP'i açmadan yükleyip etkinleştirin. **WLF Frequently Asked Questions** (8 soru) ve **WLF Order & Export Process** (6 adım) taslakları oluşturulur. İngilizce, İspanyolca ve Rusça metinler normal WordPress bloklarıyla hazırdır. Eklentiler ekranındaki bağlantılardan önizleyip **Yayımla** ile yayımlayabilirsiniz; ardından **Görünüm → Menüler** üzerinden menüye ekleyin.

Mevcut sayfalar ve elle başlattığınız SSS taslağı korunur. Yeniden etkinleştirme sayfaları çoğaltmaz veya yaptığınız düzenlemeleri değiştirmez. Oluşturulan sayfalar eklentiyi kapatmak veya silmekle silinmez. Sayfalara özel boşluk ve yazı stilleri için eklentiyi etkin bırakın. Yeniden üretim: `python3 tools/package_additional_pages.py`.

## Telefon ve küçük hareketli WhatsApp düğmesi

**Görünüm → Özelleştir → WLF — İletişim ve marka** bölümünde işletme telefonunuzu ve WhatsApp numaranızı ülke koduyla girin. **WhatsApp düğmesini göster** kutusunu açıp **Yayımla** düğmesine basın. İki numara farklı olabilir. Boş veya geçersiz numarada ilgili bağlantı gizlenir. Gerçek numara pakete yazılmamıştır; bu alanları siz doldurursunuz. Hareket azaltma tercihinde düğmenin animasyonu durur.

Aynı bölümde satış e-postasını, logoları ve alt bölüm metinlerini düzenleyebilirsiniz. Sekme simgesi **Site kimliği → Site simgesi** üzerinden değiştirilir. Üst menü bağlantıları **Görünüm → Menüler** bölümünden yönetilir. Menü atanmamışsa üç dildeki WLF bağlantıları kullanılır.

## Teklif talepleri

Form gönderimi WordPress sunucusuna yapılır. Başarılı kayıtlar **WLF Teklif talepleri** bölümünde, yalnızca yöneticilere açık olarak saklanır. E-posta bildirimi de satış adresine gönderilmeye çalışılır. Sunucunun e-postayı kabul etmesi alıcının posta kutusuna teslim edildiğini kanıtlamaz; canlı kurulumdan sonra deneme talebi gönderip paneli ve posta kutusunu kontrol edin. Gerekirse Veridyen'in sağladığı SMTP ayarlarını güvenilir bir WordPress SMTP eklentisine girin; şifreleri sohbete göndermeyin.

Teklif sayfası nonce içerdiği için önbelleğe alınmamalıdır; tema bunun için önbellek dışı başlıkları gönderir. Hosting panelinde ayrıca tam sayfa önbelleği kullanıyorsanız bu sayfayı hariç tutun. Kayıt başarılıysa ziyaretçiye bir WLF referansı gösterilir. Ağ veya kayıt hatasında başarı mesajı verilmez. İsteklerde doğrulama, WordPress nonce kontrolü, görünmez spam alanı ve IP başına beş istek/on dakika sınırı bulunur. Özel kayıtları ihtiyaç kalmadığında panelden silebilirsiniz. Ürünler ve teklifler tema/eklenti devre dışı bırakılınca silinmez.

## Doğrulananlar ve sınırlar

Gerçek yerel WordPress 7.1.3 / PHP 8.3 / MariaDB 11.4 üzerinde tema ve eklenti etkinleştirildi. Ürün oluşturma, düzenleme, yayımlama, kaldırma; üç dil; arama/filtreler; özel teklif kaydı; telefon/WhatsApp ayarları; yerel sayfa bağlantıları; blok editöründen kaydedilen metin ve çevirilerin ön yüzde görünmesi test edildi. Beş sayfada geçersiz Gutenberg bloğu bulunmadı. Mobil ve masaüstü kontrolleri geçti. Test sırasında e-postalar yerelde yakalandı; gerçek mesaj gönderilmedi.

Veridyen hesabınıza doğrudan kurulum yapılmadı. Canlı kurulum, yedek, alan adı/HTTPS durumu ve gerçek e-posta teslimi sizin hosting ortamınızda ayrıca doğrulanmalıdır. Kullanıcı/SMTP/veritabanı şifresi paketlere dahil edilmez.
