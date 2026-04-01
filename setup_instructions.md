# ThessNest — Kurulum ve Kullanım Rehberi (Complete Setup Guide)

Temayı sunucunuza yükleyip aktifleştirdikten sonra aşağıdaki **tek seferlik** adımları sırasıyla tamamlayarak platformu tamamen çalışır hale getirebilirsiniz.

---

## ⚡ 0. Ön Gereksinimler (Prerequisites)
- WordPress 6.0 veya üstü
- PHP 7.4 veya üstü
- MySQL 5.7+ / MariaDB 10.3+
- HTTPS aktif (SSL sertifikası gerekli)

---

## 🚀 1. Tek Tıkla Kurulum (One-Click Setup) ★ ÖNERİLEN

ThessNest teması, sizi sayfa oluşturma ve menü ayarlama derdinden kurtaracak otomatik bir kurulum aracıyla gelir.

1. WordPress admin panelinizin sol menüsünden **ThessNest Dashboard**'a tıklayın.
2. Sayfanın en altındaki **"🚀 One-Click Theme Setup (Optional)"** bölümünü bulun.
3. **Run Auto-Setup** butonuna tıklayın ve onaylayın.

> ✅ **BU İŞLEM ŞUNLARI OTOMATİK YAPAR:**
> - Kalıcı bağlantıları (Permalinks) günceller.
> - Gerekli tüm sayfaları (*Home, Dashboard, Add Listing, About Us, Contact*) uygun şablonlarıyla oluşturur.
> - "Home" sayfasını statik ana sayfa olarak ayarlar.
> - Ana Menüyü (Main Menu) oluşturur, sayfaları ekler ve Header'a yerleştirir.
> - İlan kategorilerini (Mahalleler, Özellikler, Hedef Kitleler) sisteme ekler.
> - İçini görebilmeniz için **4 adet örnek ilan (Dummy Property)** oluşturur.

**Eğer bu butonu kullandıysanız, aşağıdaki 2, 3, 4, 8 ve 12 numaralı adımları ATLAYABİLİRSİNİZ!** Sadece kontrol amaçlı göz atmanız yeterlidir.

---

## 2. Kalıcı Bağlantıları (Permalinks) Yenileme (Manuel Yöntem)
*(Eğer Tek Tıkla Kurulumu yaptıysanız bu adımı atlayabilirsiniz)*

Tema aktifleştirildiğinde Custom Post Type'lar (Properties, Messages, Bookings) otomatik kaydedilir. Ancak WordPress'in URL kurallarını tanıması için bağlantıları bir kez güncellemelisiniz.

1. **Settings (Ayarlar) > Permalinks (Kalıcı Bağlantılar)** sekmesine gidin.
2. **Post name (Yazı adı)** seçeneğini seçin.
3. Hiçbir şeyi değiştirmeden **Save Changes (Değişiklikleri Kaydet)** butonuna basın.

---

## 3. Statik Ana Sayfa Ayarı (Manuel Yöntem)
WordPress varsayılan olarak blog yazılarını ana sayfada gösterir. ThessNest'in özel `front-page.php` şablonunun çalışması için bunu değiştirmelisiniz.

1. **Pages (Sayfalar) > Add New (Yeni Ekle)** ile yeni bir sayfa oluşturun.
2. Sayfa başlığını **"Home"** veya **"Ana Sayfa"** yapın. İçerik kısmını boş bırakabilirsiniz (çünkü front-page.php kendi içeriğini üretir).
3. Sayfayı **Yayınla (Publish)** deyin.
4. **Settings (Ayarlar) > Reading (Okuma)** sekmesine gidin.
5. **"Your homepage displays"** kısmında **"A static page"** seçeneğini işaretleyin.
6. **Homepage** açılır menüsünden az önce oluşturduğunuz **"Home"** sayfasını seçin.
7. **Save Changes** butonuna basın.

> ✅ Bu adımdan sonra sitenizin ana sayfasında Hero, Featured Properties, How It Works ve CTA bölümleri görünecektir.

---

## 4. Navigasyon Menüsü Oluşturma (Manuel Yöntem)
Header'daki navigasyon çubuğunun çalışması için bir menü oluşturup atamalısınız.

### 3.1 Menüyü Oluşturma
1. WordPress yönetim panelinden **Appearance (Görünüm) > Menus (Menüler)** sekmesine gidin.
2. Sayfanın üst kısmında **"Create a new menu"** bağlantısını tıklayın.
3. **Menu Name** alanına **"Main Menu"** yazın.
4. Alttaki **Menu Settings** bölümünde **"Primary Menu (Currently set to: Header)"** kutucuğunu **işaretleyin**. Bu kutucuk işaretlenmezse menü header'da görünmez.
5. **Create Menu** butonuna basın. Menünüz oluşturuldu, şimdi öğe ekleme adımına geçin.

### 3.2 Menüye Sayfa Ekleme (Pages Paneli)
Menü oluşturulduktan sonra sol tarafta **"Add menu items"** panelleri açılacaktır.

1. Sol panelde **Pages (Sayfalar)** bölümünü açın.
2. **"View All"** sekmesine tıklayarak tüm sayfaları görün.
3. Aşağıdaki sayfaları seçin (checkbox'larını işaretleyin):
   - **Home** (Ana sayfa — Adım 2'de oluşturdunuz)
   - **About Us** (Hakkımızda — Adım 4.3'te oluşturacaksınız)
   - **Contact** (İletişim — Adım 4.4'te oluşturacaksınız)
4. **"Add to Menu"** butonuna basın. Seçtiğiniz sayfalar sağ taraftaki menü yapısına eklenecek.

> 💡 Henüz About Us ve Contact sayfalarını oluşturmadıysanız, önce Adım 4'ü tamamlayıp sonra bu adıma geri dönebilirsiniz. Şimdilik yalnızca **Home** sayfasını ekleyebilirsiniz.

### 3.3 Özel Bağlantı Ekleme — Our Solutions (Custom Links)
İlan listesi sayfası için özel bir bağlantı eklemeniz gerekir:

1. Sol panelde **Custom Links (Özel Bağlantılar)** bölümünü açın.
2. **URL** alanına şunu yazın: `/properties/`
3. **Link Text (Bağlantı Metni)** alanına şunu yazın: `Our Solutions`
4. **"Add to Menu"** butonuna basın.

### 3.4 Menü Sıralamasını Düzenleme
Menü öğelerini istediğiniz sırayla düzenlemek için sürükle-bırak yöntemini kullanın. Önerilen sıralama:

| Sıra | Menü Öğesi | Açıklama |
|------|------------|----------|
| 1 | Home | Ana sayfa |
| 2 | About Us | Hakkımızda |
| 3 | Our Solutions | İlan listesi (`/properties/`) |
| 4 | Contact | İletişim |

Her bir menü öğesini fare ile tutup yukarı/aşağı sürükleyerek sıralayabilirsiniz.

### 3.5 Menüyü Kaydetme
1. Tüm öğeleri ekleyip sıraladıktan sonra sayfanın sağ üst köşesindeki **"Save Menu"** butonuna basın.
2. Sitenizin ön yüzünü ziyaret ederek header'da menünün göründüğünü doğrulayın.

### 3.6 Footer Menüsü (Opsiyonel)
İsterseniz footer için de ayrı bir menü oluşturabilirsiniz:

1. Yine **Appearance > Menus** sayfasında **"Create a new menu"** bağlantısına tıklayın.
2. Menü adını **"Footer Menu"** yapın.
3. **Menu Settings** bölümünde **"Footer Menu"** kutucuğunu işaretleyin.
4. **Create Menu** butonuna basın.
5. İstediğiniz sayfa ve bağlantıları ekleyip **Save Menu** ile kaydedin.

> ⚠️ **Primary Menu kutucuğunu işaretlemeyi kesinlikle unutmayın!** Bu kutucuk işaretlenmezse header navigasyonu boş kalır ve ziyaretçiler sayfalar arası geçiş yapamaz.

---

## 5. Gerekli Sayfaları Oluşturma (Manuel Yöntem)

Aşağıdaki sayfaları **Pages > Add New** ile oluşturmanız gerekmektedir:

### 4.1 Dashboard (Kullanıcı Paneli)
1. Başlık: **"Dashboard"** (Slug mutlaka `dashboard` olmalı)
2. Sağ taraftaki **Template** ayarından → **Frontend Dashboard** şablonunu seçin.
3. Yayınlayın.

### 4.2 Add Listing (İlan Ekleme)
1. Başlık: **"Add Listing"** (Slug mutlaka `add-listing` olmalı)
2. **Template** → **Add Listing** şablonunu seçin.
3. Yayınlayın.

### 4.3 About Us (Hakkımızda)
1. Başlık: **"About Us"**
2. **Template** → **About Page** şablonunu seçin.
3. İçerik kısmına platform hakkında bilgileri yazın.
4. Yayınlayın.

### 4.4 Contact (İletişim) — (Opsiyonel)
1. **Pages > Add New** ile yeni bir sayfa oluşturun. Başlık: **"Contact"**
2. **Contact Form 7** veya **WPForms** eklentisini kurun (bkz. Adım 12).
3. Eklenti size bir shortcode verecek (örn: `[contact-form-7 id="123"]`). Bu shortcode'u sayfa içeriğine yapıştırın.
4. Sayfayı yayınlayın.

> ⚠️ **Dashboard ve Add Listing sayfalarının slug'ları (URL uzantısı) sırasıyla `dashboard` ve `add-listing` olmalıdır.** Header'daki butonlar bu adreslere yönlendirir.

---

## ⚠️ 6. Eski Tema Sayfalarını Temizleme (Önceki Temadan Geçiş)
Eğer daha önce başka bir tema (Starter Templates, Astra, OceanWP vb.) kullanıyorduysanız, o temanın oluşturduğu sayfalar WordPress veritabanında kalmaya devam eder. ThessNest aktifleştiğinde bu eski sayfalar **bozuk görünebilir** çünkü eski temanın veya page builder'ın (Elementor, WPBakery vb.) CSS/layout kodları artık yüklenmez.

**Yapmanız gerekenler:**
1. **Pages (Sayfalar)** listesine gidin.
2. ThessNest için yukarıda oluşturduğunuz sayfalar **dışındaki** eski sayfaları tespit edin (özellikle Contact, Home, About gibi eski tema sayfaları).
3. Eski sayfaları **çöp kutusuna taşıyın** veya taslak (draft) yapın.
4. Adım 3'teki menüyüzden **eski sayfa linklerini kaldırıp**, yeni oluşturduğunuz ThessNest sayfalarını ekleyin.

> 💡 **Kural:** ThessNest ile kullanacağınız her sayfa, yukarıdaki adımlarda (4.1–4.4) anlatıldığı şekilde **sıfırdan** oluşturulmalıdır. Eski temadan kalan sayfaları menüye eklemeyin.

---

## 7. Hero Arka Plan Görselini Yükleme (Hero Image)
Ana sayfadaki büyük Selanik fotoğrafını değiştirmek veya yüklemek için:

1. **Appearance (Görünüm) > Customize (Özelleştir)** sekmesine gidin.
2. Sol panelde **Homepage Settings > Hero Section** bölümüne tıklayın.
3. **"Hero Background Image"** alanına yüksek çözünürlüklü (minimum 1920×1080) bir Selanik fotoğrafı yükleyin.
4. Hero başlığını ve alt başlığını da bu panelden düzenleyebilirsiniz.
5. Üstteki **Publish (Yayınla)** butonuna basın.

> 💡 Tema `assets/images/` klasöründe varsayılan bir görsel (`hero-bg-default.png`) içerir. Ancak bu görselin sunucunuzda mevcut olduğundan emin olun.

---

## 8. Zorunlu Eklentilerin Kurulumu (TGM Plugin Activation)
ThessNest, profesyonel bir tema olarak kendi "Eklenti Yöneticisi" ile gelir.

1. Temayı aktifleştirdiğinizde veya **ThessNest Dashboard**'a girdiğinizde üst tarafta sarı bir uyarı görürsünüz:
   *"This theme requires the following plugins: Redux Framework, WP Mail SMTP..."*
2. **Begin installing plugins** bağlantısına tıklayın.
3. Açılan listedeki tüm eklentileri (Contact Form 7, Loco Translate vb.) seçip topluca **Install** ve ardından **Activate** yapın.
> ✅ Manuel eklenti aramanıza gerek yoktur. Tüm gerekli ve önerilen eklentiler bu ekrandan tek tıkla kurulur.

### 6.1 ThessNest Options Panelini Kullanma
Redux etkinleştirildikten sonra:

1. Sol menüde **ThessNest → ThessNest Options** bağlantısına tıklayın.
2. Veya üst admin çubuğundaki **ThessNest Options** kısayolunu kullanın.
3. Sol tarafta koyu renkli sidebar'da bölüm listesi göreceksiniz:

| Bölüm | Açıklama |
|-------|----------|
| **General** | Site açıklaması, varsayılan dil, Google Maps API |
| **Logos & Favicon** | Logo yükleme (açık/koyu), favicon |
| **Header Nav** | Sticky header, header stili, CTA butonu |
| **Booking** | Min/max kiralama süresi, depozito, onay modu |
| **Price & Currency** | Para birimi, pozisyon, fiyat etiketi |
| **Styling** | Accent renk, dark mode, köşe yuvarlaklığı |
| **Footer** | Copyright metni, sosyal medya açık/kapalı |
| **Contact** | Telefon, e-posta, adres, tüm sosyal medya URL'leri |
| **Live Chat** | Chatbot embed kodu (Tidio, Tawk.to vb.) |

4. Her bölümde ayarları yapıp sağ üstteki **Save Changes** butonuna basın.

> 💡 Redux panelinde **Import/Export** özelliği mevcuttur — bir sunucudan ekleyip diğerine kopyalayabilirsiniz.

---

## 9. Tema Ayarları — Customizer (Ek Ayarlar)
Redux Framework dışında, bazı ek ayarlar hâlâ WordPress Customizer üzerinden kontrol edilir:

1. **Appearance → Customize** sekmesine gidin.
2. **Homepage Settings** → Hero görseli, başlıklar.
3. **Publish** butonuna basın.

---

## 10. Temayı Kendi Dilinize Çevirme (Localization & Translation)
ThessNest temasının tüm metinleri ("Book Now", "Search", "Property Details" vb.) uluslararası dil standartlarına (`.pot` altyapısına) uygun kodlanmıştır.

1. TGM ile kurduğunuz **Loco Translate** eklentisine gidin.
2. **Loco Translate > Themes** sekmesinden **ThessNest**'i seçin.
3. **New Language (Yeni Dil Ekl)** butonuna basın ve kendi dilinizi seçin.
4. "Start Translating" diyerek açılan listedeki İngilizce kelimelerin karşılıklarını çevirip kaydedin. Paneldeki Redux ayarları dışındaki her şey %100 çevrilebilir formattadır!

---

## 10. Mahalleler, Özellikler ve Hedef Grup Ekleme (Manuel Yöntem)
Arama filtreleri ve ilanların düzgün çalışması için taxonomy terimlerini önceden oluşturmalısınız:

### 8.1 Neighborhoods (Mahalleler)
1. **ThessNest → Neighborhoods** sekmesine gidin.
2. Selanik'in mahallelerini ekleyin: Ladadika, Kalamaria, Ano Poli, Toumba, Pylaia, Triangle, vb.

### 8.2 Amenities (Özellikler)
1. **ThessNest → Amenities** sekmesine gidin.
2. Eklenmesi önerilen özellikler: Fast Wi-Fi, Washing Machine, Balcony, Air Conditioning, Dedicated Workspace, Furnished, Elevator, Parking, Dishwasher, vb.

### 8.3 Target Groups (Hedef Gruplar)
1. **ThessNest → Target Groups** sekmesine gidin.
2. Ekleyin: Student, Digital Nomad, Expat

> ✅ Bu terimlerin oluşturulmadan ilan eklenemez (dropdown'lar boş görünür).

---

## 11. Kullanıcı Rolleri (User Roles — Landlord / Tenant)
Tema iki özel kullanıcı rolü tanımlar:
- **Landlord (Ev Sahibi):** İlan ekleyebilir, KYC belgesi yükleyebilir.
- **Tenant (Kiracı):** Favorilere ekleyebilir, rezervasyon yapabilir, mesaj gönderebilir.

Bu roller tema aktifleştirildiğinde otomatik yaratılır.

- **Users > Add New** ekranından yeni bir kullanıcı eklerken rol olarak **Landlord** veya **Tenant** seçebilirsiniz.
- Test için bir adet Landlord ve bir adet Tenant hesabı oluşturup tüm özellikleri denemeniz önerilir.

---

## 12. E-Posta Gönderimi (SMTP Kurulumu) ★ ÖNEMLİ
İletişim formu, rezervasyon bildirimi ve KYC onay e-postalarının çalışması için WordPress'inizin e-posta gönderebiliyor olması gerekir.

- Paylaşımlı hosting'lerde PHP `mail()` fonksiyonu genellikle spama düşer veya hiç çalışmaz.
- **WP Mail SMTP** eklentisini kurup bir SMTP servisi (Gmail, SendGrid, Mailgun vb.) ile yapılandırın.
- Kurulum sonrası **WP Mail SMTP > Tools > Email Test** bölümünden test gönderin.

---

## 13. İnceleme Sistemi (Reviews & Ratings)
5 yıldızlı puanlama sistemi aktiftir. WordPress'in yorum altyapısını kullanır.

1. **Settings > Discussion (Tartışma)** sekmesine gidin.
2. İlan yorumlarının otomatik mi yoksa onay gerektirerek mi yayınlanacağını ayarlayın.
3. Test olarak bir emlak sayfasının altından yorum bırakıp yıldız verin.

---

## 14. Test Verisi Ekleme (Manuel Yöntem)
Sitenin canlı görünmesi için birkaç test ilanı eklemelisiniz:

1. **Properties > Add New** sekmesine gidin.
2. Başlık, açıklama, fotoğraflar (Featured Image + Gallery) ekleyin.
3. Sağ taraftaki kutulardan **Neighborhood**, **Amenities**, **Target Group** seçin.
4. Alt kısımdaki **Property Details** meta kutusundan kira fiyatı, fatura bedeli vs. girin.
5. **Property Location (Map)** meta kutusundan haritada konumu işaretleyin.
6. Yayınlayın.

> Ana sayfada "Featured Properties" bölümünde ilanların görünmesi için en az 1 ilan yayınlanmış olmalıdır.

---

## 15. Önerilen Eklentiler (Recommended Plugins)
| Eklenti | Amaç | Öncelik |
|---------|-------|---------|
| **Redux Framework** | Tema ayar paneli (ThessNest Options) | ★★★ Kritik |
| **WP Mail SMTP** | E-posta gönderimini sağlamak | ★★★ Kritik |
| **Loco Translate** | Tema metinlerini panelden çevirmek/düzenlemek | ★★★ Kritik |
| **Rank Math SEO** (veya Yoast) | Arama motoru optimizasyonu | ★★☆ Yüksek |
| **WP Super Cache** (veya LiteSpeed Cache) | Sayfa hızı / performans | ★★☆ Yüksek |
| **Contact Form 7** (veya WPForms) | İletişim sayfası formu | ★☆☆ Orta |
| **Classic Editor** | Gutenberg yerine klasik editör (opsiyonel) | ★☆☆ Opsiyonel |

---

## ✅ Kurulum Sonrası Kontrol Listesi (Post-Setup Checklist)

- [ ] Tek Tıkla Kurulum (`Run Auto-Setup`) çalıştırıldı (Adım 1)
- [ ] Navigasyon menüsü ("Our Solutions" bağlantısı vb.) kişiselleştirildi (Adım 4)
- [ ] Eski tema sayfaları çöpe taşındı (Adım 6)
- [ ] Ana sayfa Hero arka plan görseli değiştirildi (Adım 7)
- [ ] Redux Framework panelindeki ayarlar incelendi (Adım 8)
- [ ] SMTP e-posta eklentisi kuruldu ve test edildi (Adım 12)

**🎉 Tebrikler! ThessNest Emlak Platformunuz başarıyla kuruldu ve yayına hazır.**
