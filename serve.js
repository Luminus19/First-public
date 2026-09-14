// Omar's Hair Studio — Node.js + Express + PostgreSQL backend
// Kurulum:
//   npm init -y && npm i express pg cors dotenv nodemailer
//   node server.js
// .env dosyası:
//   DATABASE_URL=postgres://user:pass@localhost:5432/omars_hair
//   PORT=3000
//   NOTIFY_EMAIL=omar@example.com
//   SMTP_HOST=...  SMTP_USER=...  SMTP_PASS=...

require('dotenv').config();
const express = require('express');
const cors = require('cors');
const { Pool } = require('pg');
const nodemailer = require('nodemailer');
const path = require('path');

const pool = new Pool({ connectionString: process.env.DATABASE_URL });
const app = express();
app.use(cors());
app.use(express.json());
app.use(express.static(path.join(__dirname, '..', '..'))); // index.html + assets

const mailer = process.env.SMTP_HOST ? nodemailer.createTransport({
  host: process.env.SMTP_HOST, port: 587,
  auth: { user: process.env.SMTP_USER, pass: process.env.SMTP_PASS },
}) : null;

function validate(b) {
  const errs = [];
  if (!b.full_name || b.full_name.length < 2 || b.full_name.length > 100) errs.push('Ad soyad geçersiz');
  if (!b.phone || b.phone.length < 7 || b.phone.length > 30) errs.push('Telefon geçersiz');
  if (!b.service || b.service.length < 2 || b.service.length > 100) errs.push('Hizmet geçersiz');
  if (b.notes && b.notes.length > 1000) errs.push('Notlar çok uzun');
  return errs;
}

app.post('/api/bookings', async (req, res) => {
  const b = req.body || {};
  const errs = validate(b);
  if (errs.length) return res.status(422).json({ error: errs.join(', ') });

  try {
    const { rows } = await pool.query(
      `INSERT INTO bookings (full_name, phone, service, preferred_date, preferred_time, notes)
       VALUES ($1,$2,$3,$4,$5,$6) RETURNING id`,
      [b.full_name, b.phone, b.service, b.preferred_date || null, b.preferred_time || null, b.notes || null]
    );
    if (mailer) {
      mailer.sendMail({
        from: 'noreply@omarshair.com',
        to: process.env.NOTIFY_EMAIL,
        subject: `Yeni Randevu — ${b.full_name}`,
        text: `Ad: ${b.full_name}\nTelefon: ${b.phone}\nHizmet: ${b.service}\nTarih: ${b.preferred_date || '-'} ${b.preferred_time || ''}\nNot: ${b.notes || '-'}`,
      }).catch(console.error);
    }
    res.json({ ok: true, id: rows[0].id });
  } catch (e) {
    console.error(e);
    res.status(500).json({ error: 'Sunucu hatası' });
  }
});

// Basit admin listesi (HTTP Basic Auth ile koruyun!)
app.get('/api/bookings', async (_req, res) => {
  const { rows } = await pool.query('SELECT * FROM bookings ORDER BY created_at DESC LIMIT 200');
  res.json(rows);
});

app.listen(process.env.PORT || 3000, () => console.log('Running on', process.env.PORT || 3000));
