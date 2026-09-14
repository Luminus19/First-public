# Omar's Hair Studio — Statik Site + Backend

Tek dosyalık (vanilla) HTML/CSS/JS site ve 3 farklı backend seçeneği.

## Klasör yapısı
```
omars-hair-studio/
├── index.html              ← Açıp tarayıcıda görebilirsiniz
├── assets/                 ← Tüm görseller
└── backend/
    ├── schema.sql          ← bookings tablosu (Postgres + MySQL)
    ├── php/bookings.php    ← PHP + MySQL endpoint
    └── node/server.js      ← Node.js + Express + Postgres endpoint
```

## Hızlı başlangıç

### 1) Sadece site (backend olmadan görüntüleme)
`index.html`'i çift tıklayın. Form çalışmaz, sadece tasarımı görürsünüz.

### 2) PHP + MySQL ile (paylaşımlı hosting / cPanel)
1. `backend/schema.sql` içindeki MySQL alternatifini phpMyAdmin'de çalıştırın.
2. `backend/php/bookings.php` dosyasındaki DB ve e-posta ayarlarını güncelleyin.
3. Tüm dosyaları FTP ile yükleyin; `bookings.php`'i `/api/bookings` adresine yönlendirin (Apache `.htaccess`):
   ```
   RewriteEngine On
   RewriteRule ^api/bookings$ /backend/php/bookings.php [L]
   ```

### 3) Node.js + Postgres ile (VPS / Render / Railway)
```bash
cd backend/node
npm init -y && npm i express pg cors dotenv nodemailer
cp .env.example .env   # değerleri doldurun
node server.js
```
`index.html` ve `assets/` aynı sunucudan servis edilir.

## Form endpoint'ini değiştirme
`index.html` içinde:
```js
window.BOOKING_ENDPOINT = "/api/bookings";
```
Farklı bir URL kullanacaksanız (örn. başka bir alt alan adı) burayı düzenleyin.

## Backend özellikleri
- ✅ Sunucu tarafı doğrulama (ad, telefon, hizmet, not uzunlukları)
- ✅ SQL injection'a karşı korumalı (prepared statements)
- ✅ CORS açık (preview için)
- ✅ E-posta bildirimi (PHP: `mail()`, Node: SMTP)
- ✅ Durum takibi: `new → confirmed → completed / cancelled`

## SEO
- Türkçe `lang`, açıklayıcı `<title>` ve meta description
- Open Graph etiketleri (sosyal medya paylaşımı için)
- `loading="lazy"` görseller
- Google Maps embed + yol tarifi linki

## Çağrı dönüşüm noktaları
1. Üst menüde tıklanabilir telefon
2. Hero'da "Hemen Randevu Al" butonu
3. Hizmetler bölümünde "Randevu Al →" linki
4. Yapışkan alt CTA: telefon + Randevu Al (her sayfada görünür)
5. İletişim haritasında "Yol Tarifi Al" butonu
