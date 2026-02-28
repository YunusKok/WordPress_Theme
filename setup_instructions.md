# ThessNest - Kurulum ve Kullanım Rehberi

Temamıza eklediğimiz premium özellikleri (Gelişmiş Filtreleme, Favoriler, Kullanıcı Paneli, İletişim Formu) WordPress tarafında tam olarak aktif edip kullanabilmeniz için yapmanız gereken **tek seferlik** ufak adımlar şunlardır:

## 1. Kalıcı Bağlantıları (Permalinks) Yenileme
Yeni eklediğimiz AJAX uç noktalarının ve emlak (Property) yapısının düzgün çalışması için bağlantıları bir kez güncelleyerek WordPress'in yeni kurallarımızı tanımasını sağlamalısınız.
1. WordPress Admin Paneline giriş yapın.
2. Sol menüden **Settings (Ayarlar) > Permalinks (Kalıcı Bağlantılar)** sekmesine tıklayın.
3. Hiçbir ayarı değiştirmeden sadece en alttaki **Save Changes (Değişiklikleri Kaydet)** butonuna basın.

## 2. Frontend Kullanıcı Panelini (Dashboard) Oluşturma
Eklediğimiz `template-dashboard.php` şablonunu kullanarak kullanıcıların favorilerini görebileceği bir sayfa oluşturmalısınız.
1. Sol menüden **Pages (Sayfalar) > Add New (Yeni Ekle)** deyin.
2. Sayfa başlığına örneğin "Dashboard", "Kullanıcı Paneli" veya "Profilim" yazın. (Başlık size kalmış)
3. Sağ taraftaki **Page Attributes (Sayfa Özellikleri)** veya **Template** kutucuğundan **Frontend Dashboard** şablonunu seçin.
4. Sayfayı **Publish (Yayınla)** diyerek kaydedin.
5. *Not: Header'daki "Sign In / Dashboard" butonu `/dashboard/` linkine yönlendirmeye ayarlıdır. Eğer oluşturduğunuz sayfanın kalıcı bağlantısı (slug) farklı ise, `header.php` içerisindeki `/dashboard/` linkini kendi sayfa linkinize göre güncellemeniz veya sayfa uzantınızı mutlaka `dashboard` yapmanız gerekir.*

## 2.1 İlan Ekleme (Add Listing) Sayfasını Oluşturma
Gelişmiş Frontend Submission (İlan Ekleme) altyapısı için de bir sayfaya ihtiyacımız var.
1. Yeni bir sayfa ekleyin (**Pages > Add New**).
2. Başlığı örneğin "Add Listing" veya "İlan Ekle" yapın.
3. Sağ taraftaki "Template" ayarlarından **Add Listing** şablonunu seçip yayınlayın.
4. Tıpkı Dashboard gibi Header butonları `/add-listing/` uzantısına yönlendirecek şekilde ayarlanmıştır. Sayfa URL'sinin `add-listing` olduğundan emin olun.

## 3. Yeni Rolleri (Landlord / Tenant) Yönetme
Sisteme "Ev Sahibi" (Landlord) ve "Kiracı" (Tenant) rolleri eklendi.
- Yeni bir kullanıcı eklerken veya mevcut bir kullanıcıyı düzenlerken **Users (Kullanıcılar) > Add New** ekranından rol olarak bu iki yeni rolden birini seçebilirsiniz.
- *Test etmek için kendinize ait yönetici hesabınız haricinde bir adet "Tenant" rolünde test hesabı açıp siteye giriş yapabilir, haritadan ilan sayfasına gidip kalbe (Add to Favorites) tıklayarak Dashboard'unuzda ilanların listelendiğini doğrulayabilirsiniz.*

## 4. İletişim Formunu (Mail) Test Etme
Emlak detay sayfasındaki İletişim Formunun (`single-property.php`) sorunsuz çalışması için WordPress'inizin e-posta gönderebiliyor olması gerekir.
- Form doldurulduğunda, e-posta doğrudan o ilanı ekleyen (Author / Yazar) kullanıcının e-posta adresine gönderilir.
- Formun çalışıp çalışmadığını test etmek için **gerçek bir SMTP** eklentisi (örneğin: *WP Mail SMTP*) kurmanız şiddetle tavsiye edilir. Aksi takdirde localhost veya paylaşımlı sunucularda PHP `mail()` fonksiyonu spama düşebilir veya hiç gitmeyebilir.

## 5. İnceleme ve Değerlendirme (Yorum) Sistemini Aktifleştirme
Sisteme **5 Yıldızlı İnceleme Platformu** entegre edildi. WordPress'in standart Yorum (Comments) özelliğini kullanarak eklentisiz bir puanlama sistemi oluşturduk. Test etmek için:
1. İlanlara (Property) yorum yapıldığında onay sürecinden geçmesini veya anında yayınlanmasını ayarlamak için WordPress panelinden **Settings (Ayarlar) > Discussion (Tartışma)** menüsüne gidin. İsterseniz "Yorum yazarının daha önce onaylanmış bir yorumu olmalı" veya "Yönetici her zaman onaylamalı" seçeneklerini ayarlayabilirsiniz.
2. Özel olarak herhangi bir emlak sayfasının (örneğin ID'si 15 olan bir emlak) altında oluşturduğumuz yıldızlı **Yorum Bırak (Leave a Review)** formunu kullanabilirsiniz.
3. Yorum bırakıldığında/onaylandığında, sistem ilanın puan ortalamasını otomatik hesaplayıp sayfa üstüne ve listeleme sayfasındaki kartına küçük bir `⭐ 4.8` rozeti olarak ekleyecektir.

Tüm işlemler bu kadar! Bu 5 adımı tamamladıktan sonra temanızdaki AJAX filtreleme, favoriye ekleme, mail gönderme, özel profil ve 5-yıldızlı puanlama sistemi sorunsuz bir şekilde Listdo/Houzez kalitesinde çalışacaktır.
